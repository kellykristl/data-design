<?php

require_once "autoload.php";
require_once "xsrf.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\kkristl\DataDesign\profile;


/**
 * api for the profile class
 *
 * @author Kelly Kristl <kkristl@cnm.edu>
 **/

//verify the session, start if not active
if(session_status() !== PHP_SESSION_ACTIVE) {
	session_start();
}

//prepare an empty reply
$reply = new stdClass();
$reply->status = 200;
$reply->data = null;

try {
	//grab the mySQL connection
	$pdo = connectToEncryptedMySQL("/etc/apache2/capstone-mysql/kkristl.ini");

	//determine which HTTP method was used
	$method = array_key_exists("HTTP_X_HTTP_METHOD", $_SERVER) ? $_SERVER["HTTP_X_HTTP_METHOD"] : $_SERVER["REQUEST_METHOD"];

	//sanitize input
	$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
	$profileId = filter_input(INPUT_GET, "profileId", FILTER_VALIDATE_INT);
	$content = filter_input(INPUT_GET, "content", FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);

	//make sure the id is valid for methods that require it
	if(($method === "DELETE" || $method === "PUT") && (empty($id) === true || $id < 0)) {
		throw(new InvalidArgumentException("id cannot be empty or negative", 405));
	}


	// handle GET request - if id is present, that profile is returned, otherwise all profiles are returned
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();

		//get a specific profile or all profiles and update reply
		if(empty($id) === false) {
			$profile = profile::getProfileByProfileId($pdo, $id);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else if(empty($profileId) === false) {
			$profile = profile::getProfileByProfileId($pdo, $profileId);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else if(empty($content) === false) {
			$profile = profile::getProfileByProfileContent($pdo, $content);
			if($profile !== null) {
				$reply->data = $profile;
			}
		} else {
			$profile = profile::getAllProfile($pdo);
			if($profile !== null) {
				$reply->data = $profile;
			}
		}
	} else if($method === "PUT" || $method === "POST") {

		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//make sure profile content is available (required field)
		if(empty($requestObject->profileContent) === true) {
			throw(new \InvalidArgumentException ("No content for profile.", 405));
		}

		// make sure profile date is accurate (optional field)
		if(empty($requestObject->profileDate) === true) {
			$requestObject->profileDate = new \DateTime();
		}

		//  make sure profileId is available
		if(empty($requestObject->profileId) === true) {
			throw(new \InvalidArgumentException ("No Profile ID.", 405));
		}

		//perform the actual put or post
		if($method === "PUT") {

			// retrieve the profile to update
			$profiles = profile::getProfileByProfileId($pdo, $id);
			if($profiles === null) {
				throw(new RuntimeException("Profile does not exist", 404));
			}

			// update all attributes
			$profiles->setProfileDate($requestObject->profileDate);
			$profiles->setProfileContent($requestObject->profileContent);
			$profiles->update($pdo);

			// update reply
			$reply->message = "profile updated OK";

		} else if($method === "POST") {

			// create new profile and insert into the database
			$profiles = new profile(null, $requestObject->profileId, $requestObject->profileContent, null);
			$profiles->insert($pdo);

			// update reply
			$reply->message = "profile created OK";
		}

	} else if($method === "DELETE") {

		// retrieve the profile to be deleted
		$profiles = profile::getProfileByProfileId($pdo, $id);
		if($profiles === null) {
			throw(new RuntimeException("profile does not exist", 404));
		}

		// delete profile
		$profiles->delete($pdo);

		// update reply
		$reply->message = "profile deleted OK";
	} else {
		throw (new InvalidArgumentException("Invalid HTTP method request"));
	}

	// update reply with exception information
} catch(Exception $exception) {
	$reply->status = $exception->getCode();
	$reply->message = $exception->getMessage();
} catch(TypeError $typeError) {
	$reply->status = $typeError->getCode();
	$reply->message = $typeError->getMessage();
}

header("Content-type: application/json");
if($reply->data === null) {
	unset($reply->data);
}

// encode and return reply to front end caller
echo json_encode($reply);