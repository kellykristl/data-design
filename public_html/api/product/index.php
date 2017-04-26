<?php

require_once "autoload.php";
require_once "xsrf.php";
require_once("/etc/apache2/capstone-mysql/encrypted-config.php");

use Edu\Cnm\kkristl\DataDesign\product;


/**
 * api for the Product class
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


	// handle GET request - if id is present, that product is returned, otherwise all products are returned
	if($method === "GET") {
		//set XSRF cookie
		setXsrfCookie();

		//get a specific product or all products and update reply
		if(empty($id) === false) {
			$product = Product::getProductByProductId($pdo, $id);
			if($product !== null) {
				$reply->data = $product;
			}
		} else if(empty($profileId) === false) {
			$product = Product::getProductByProductProfileId($pdo, $profileId);
			if($products !== null) {
				$reply->data = $products;
			}
		} else if(empty($content) === false) {
			$products = Product::getProductByProductContent($pdo, $content);
			if($products !== null) {
				$reply->data = $products;
			}
		} else {
			$products = Product::getAllProducts($pdo);
			if($products !== null) {
				$reply->data = $products;
			}
		}
	} else if($method === "PUT" || $method === "POST") {

		$requestContent = file_get_contents("php://input");
		$requestObject = json_decode($requestContent);

		//make sure product content is available (required field)
		if(empty($requestObject->productContent) === true) {
			throw(new \InvalidArgumentException ("No content for Product.", 405));
		}

		// make sure product date is accurate (optional field)
		if(empty($requestObject->productDate) === true) {
			$requestObject->productDate = new \DateTime();
		}

		//  make sure profileId is available
		if(empty($requestObject->profileId) === true) {
			throw(new \InvalidArgumentException ("No Profile ID.", 405));
		}

		//perform the actual put or post
		if($method === "PUT") {

			// retrieve the product to update
			$products = Product::getProductByProductId($pdo, $id);
			if($products === null) {
				throw(new RuntimeException("Tweet does not exist", 404));
			}

			// update all attributes
			$products->setProductDate($requestObject->productDate);
			$products->setProductContent($requestObject->productContent);
			$products->update($pdo);

			// update reply
			$reply->message = "Product updated OK";

		} else if($method === "POST") {

			// create new product and insert into the database
			$products = new Product(null, $requestObject->profileId, $requestObject->productContent, null);
			$products->insert($pdo);

			// update reply
			$reply->message = "Tweet created OK";
		}

	} else if($method === "DELETE") {

		// retrieve the Product to be deleted
		$products = Product::getProductByProductId($pdo, $id);
		if($products === null) {
			throw(new RuntimeException("Product does not exist", 404));
		}

		// delete product
		$products->delete($pdo);

		// update reply
		$reply->message = "Product deleted OK";
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