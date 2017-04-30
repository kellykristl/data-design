<?php
namespace Edu\Cnm\DataDesign;
/**
 * Profile for Data Design
 * @author kkristl <kkristl@cnm.edu>
 **/
class Profile {
	/**
	 * Id for this Profile; this is the primary key
	 * @var int $profileId;
	 **/
	private $profileId;
	/**
	 * Token created for Profile
	 * @var string $profileActivationToken;
	 */
	private $profileActivationToken;
	/**
	 * Handle for Profile
	 * @var string $profileAtHandle;
	 */
	private $profileAtHandle;
	/**
	 * User email
	 * @var string $profileEmail;
	 */
	private $profileEmail;
	/**
	 * @var string $profilePassHash;
	 */
	private $profilePassHash;
	/**
	 * @var string $profileSaltHash;
	 */
	private $profileSaltHash;
	/** There should be a public function construct here */
	public function __construct(?int $newProfileId, string $newProfileActivationToken, string $newProfileAtHandle, string $newProfileEmail, string $newProfilePassHash, string $newProfileSaltHash) {
		try {
			$this->setProfileId($newProfileId);
			$this->setProfileActivationToken($newProfileActivationToken);
			$this->setProfileAtHandle($newProfileAtHandle);
			$this->setProfileEmail($newProfileEmail);
			$this->setProfilePassHash($newProfilePassHash);
			$this->setProfileSaltHash($newProfileSaltHash);
		}
		catch (\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * accessor method for profile id
	 *
	 * @return int|null value of profile id
	 */
	/**
	 * @return int|null value
	 */
	public function getProfileId() : ?int {
		return($this->profileId);
	}
	/**
	 * mutator method for profile id
	 * @param int|null profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 */
	public function setProfileId(?int $newProfileId) : void {
		//if new tweet if is null return it immediately
		if($newProfileId === null) {
			$this->profileId = null;
			return;
		}
		// verify the tweet id is positive
		if($newProfileId <=0) {
			throw(new \RangeException("profile id is not positive"));
		}
		// convert the new profile id to a profile id and store it
		$this->profileId = $newProfileId;
	}
	/**
	 * accessor method for activation token
	 * @return int value of activation token
	 */
	public function getProfileActivationToken() : string {
		return ($this->profileActivationToken);
	}
	/**
	 * mutator method for profile activation token
	 * @param string profile activation token
	 * @throws \InvalidArgumentException if activation token is empty
	 * @throws \RangeException if $newProfileActivationToken is not positive
	 * @throws \TypeError if $newProfileActivationToken is not an integer
	 */
	public function setProfileActivationToken(string $newProfileActivationToken) : void {
		//enforce formatting on activation token
		$newProfileActivationToken = trim($newProfileActivationToken);
		$newProfileActivationToken = strtolower($newProfileActivationToken);
		//enforce content in profile activation token
		if (empty($newProfileActivationToken) === true) {
			throw(new \InvalidArgumentException("profile activation token is empty or insecure"));
		}
		//enforce hex on profile activation token
		if (!ctype_xdigit($newProfileActivationToken)) {
			throw(new \InvalidArgumentException("profile activation token is empty or insecure"));
		}
		//enforce string length on profile activation token
		if (strlen($newProfileActivationToken) !== 32) {
			throw(new \RangeException("profile activation token must be 32 characters"));
		}
		//store the profile activation token
		$this->profileActivationToken = $newProfileActivationToken;
	}
	/**
	 * accessor method for profile at hondle
	 * @return string value of profile at handle
	 **/
	public function getProfileAtHandle() : string {
		return ($this->profileAtHandle);
	}
	/**
	 * mutator method for profile at handle
	 * @param $profileAtHandle;
	 * @throws \InvalidArgumentException if empty or not alphanumeric
	 * @throws \RangeException if not 32 characters
	 */
	public function setProfileAtHandle(string $newProfileAtHandle) : void {
		//enforce formatting on profile at handle
		$newProfileAtHandle = trim($newProfileAtHandle);
		$newProfileAtHandle = strtolower($newProfileAtHandle);
		//enforce content in profile at handle
		if (empty($newProfileAtHandle) === true) {
			throw(new \InvalidArgumentException("profile at handle is empty"));
		}
		//enforce alphanumeric string
		if (!ctype_alnum($newProfileAtHandle)) {
			throw(new \InvalidArgumentException("profile at handle must contain only alphanumeric characters"));
		}
		//enforce max string length on profile at handle
		if (strlen($newProfileAtHandle) !== 32) {
			throw(new \RangeException("profile at handle exceeds length limit"));
		}
		//store the profile at handle
		$this->profileAtHandle = $newProfileAtHandle;
	}
	/**
	 * accessor for profile email
	 * @return string
	 */
	public function getProfileEmail() : string {
		return ($this->profileEmail);
	}
	/**
	 * mutator method for profile email
	 * @param string $profileEmail
	 * @throws \InvalidArgumentException if profile email is empty
	 * @throws \RangeException if profile email is not 32 characters
	 */
	public function setProfileEmail(string $newProfileEmail) : void {
		//enforce content in profile email
		if (empty($newProfileEmail) === true) {
			throw(new \InvalidArgumentException("profile email is empty"));
		}
		//enforce max string length on profile email
		if (strlen($newProfileEmail) !== 32) {
			throw(new \RangeException("profile email exceeds length limit"));
		}
		//store the profile email
		$this->profileEmail = $newProfileEmail;
	}
	/**
	 * accessor for profile password hash
	 * @return string
	 */
	public function getProfilePassHash(): string {
		return ($this->profilePassHash);
	}
	/**
	 * mutator for profile password hash
	 * @param string $profilePassHash
	 * @throws \InvalidArgumentException if string is empty or not hex
	 * @throws \RangeException if profile password hash is not 128 characters
	 * @throws \TypeError if profile password hash is not a sting
	 */
	public function setProfilePassHash(string $newProfilePassHash) : void {
		//enforce that the profile password hash is properly formatted
		$newProfilePassHash = trim($newProfilePassHash);
		$newProfilePassHash = strtolower($newProfilePassHash);
		if (empty($newProfilePassHash) === true) {
			throw (new InvalidArgumentException("profile password hash is empty or insecure"));
		}
		//enforce that the hash is hex
		if (!ctype_xdigit($newProfilePassHash)) {
			throw(new \InvalidArgumentException("profile hash is empty or insecure"));
		}
		//enforce that the hash is exactly 128 characters
		if (strlen($newProfilePassHash) !== 128) {
			throw(new \RangeException("profile hash must be 128 characters"));
		}
		//store this hash
		$this->profilePassHash = $newProfilePassHash;
	}
	/**
	 * accessor for profile salt of hash
	 * @return string
	 */
	public function getProfileSaltHash(): string {
		return ($this->profileSaltHash);
	}
	/**
	 * mutator for profile hash salt
	 * @param string $profileSaltHash
	 * @throws \InvalidArgumentException if string is empty or not hex
	 * @throws \RangeException if profile passeord hash is not 128 characters
	 * @throws \TypeError if profile password hash is not a sting
	 */
	public function setProfileSaltHash(string $newProfileSaltHash) : void {
		//enforce that the profile hash salt is properly formatted
		$newProfileSaltHash = trim($newProfileSaltHash);
		$newProfileSaltHash = strtolower($newProfileSaltHash);
		if (empty($newProfileSaltHash) === true) {
			throw(new \InvalidArgumentException("profile hash salt is empty or insecure"));
		}
		//enforce that the salt is hex
		if (!ctype_xdigit($newProfileSaltHash)) {
			throw(new \InvalidArgumentException("profile hash is empty or insecure"));
		}
		//enforce that the salt is exactly 64 characters
		if (strlen($newProfileSaltHash) !== 64) {
			throw(new \RangeException("profile salt must be 64 characters"));
		}
		//store this salt
		$this->profileSaltHash = $newProfileSaltHash;
	}
	/**
	 * @param \PDO $pdo connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError of $pdo is not a PDO connection object
	 *
	 */
	public function insert(\PDO $pdo) : void {
		if($this->profileId === null) {
			throw(new \PDOException("unable to delete a tweet that does not exist"));
		}
		$query = "INSERT INTO profile(profileID, profileActivationToken, profileAtHandle, profileEmail, profilePassHash, profilePassSalt) VALUES (:profileId, :profileActicationToken, :profileAtHandle, :profileEmail, :profilePassHash, :profilePassSalt)";
		$statement = $pdo->prepare($query);
		$parameters = ["profileId" => $this->profileId];
		$statement->execute($parameters);
	}
	/**
	 * deletes this profile from mySQL
	 *
	 * @params \PDO $pdo connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {
		//enforces that profileId is not null
		if($this->profileId === null) {
			throw(new \PODException ("unable to update a profile that does not exisit"));
		}
		// create query
		$query = "DELETE FROM profile WHERE profileId = :profileId";
		$statement = $pdo->prepare($query);
		$parameters = ["profileId" => $this->profileId];
		$statement->execute($parameters);
	}
	/**
	 *Updates this profile in mySQL
	 *@param \PDO $pdo PDO connection style
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a pdo connection object
	 **/
	public function update(\PDO $pdo) : void {
		//enforce the profileId is not null
		if($this->profileId === null) {
			throw(new \PDOException("unable to update a profile that does not exist"));
		}
		$query = "UPDATE profile SET profileAtHandle = :profileAtHandle, profileEmail = :profileEmail";
		$statement = $pdo->prepare($query);
		$parameters = ["profileAtHandle" => $this->profileAtHandle, "profileEmail" => $this->profileEmail];
		$statement->execute($parameters);
	}
	/**
	 * gets profile by profile id
	 * @param \PDO $pdo PDO connection object
	 * @param int $profileId profile id to search for
	 * @return $profile\null profile found or not foudn
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProfileByProfileId(\PDO $pdo, int $profileId) : ?Profile {
		// sanitize this profile id
		if($profileId <= 0) {
			throw(new \PDOException("profile id is not positive"));
		}
		// create query
		$query = "SELECT profileId, profileAtHandle, profileEmail FROM profile WHERE profileID = :profileId";
		$statement = $pdo->prepare($query);
		$parameters = ["profileId" => $profileId];
		$statement->execute($parameters);
		//fetch profile from mySQL
		try {
			$profile = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$tweet = new Profile($row ["profileId"], $row["profileAtHandle"], $row["profileEmail"]);
			}
		} catch (\Exception $exception){
			// if the row is unable to convert, rethrow
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($profile);
	}
}
