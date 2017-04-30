<?php
namespace Edu\Cnm\DataDesign;
require_once("autoload.php");
/**
 * Favorite Class for Data Design
 * @author kkristl <kkristl@cnm.edu>
 */
class Favorite {
	use ValidateDate;
	/**
	 * id for favorite product id
	 * @var int $favoriteProductId
	 **/
	private $favoriteProductId;
	/**
	 * id for favorite profile id
	 * @var int $favoriteProfileId
	 **/
	private $favoriteProfileId;
	/**
	 * date for favorite date
	 * @var \DateTime $favoriteDate
	 **/
	private $favoriteDate;
	/**
	 * constructor for favorite
	 *
	 * @param int|null $newFavoriteProductId
	 * @param int|null $newFavoriteProfileId
	 * @param \DateTime $favoriteDate
	 *
	 * @throws \InvalidArgumentException
	 * @throws \RangeException
	 * @throws \TypeError
	 * @throws \Exception
	 **/
	public function __construct(?int $newFavoriteProductId, ?int $newFavoriteProfileId, $newFavoriteDate = null){
		try {
			$this->setFavoriteProductId($newFavoriteProductId);
			$this->setFavoriteProfileId($newFavoriteProductId);
			$this->setFavoriteDate($newFavoriteDate);
		}
		catch (\InvalidArgumentException | \RangeException |\Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * acessor for favorite product id
	 * @return int
	 */
	public function getFavoriteProductId(): ?int {
		return ($this->favoriteProductId);
	}
	/**
	 * @param int $newFavoriteProduct
	 */
	public function setFavoriteProductId(?int $newFavoriteProductId) : void {
		if ($newFavoriteProductId === null) {
			$this->$newFavoriteProductId = null;
			return;
		}
		if ($newFavoriteProductId <= 0) {
			throw (new \RangeException("favorite product id is not positive"));
		}
		//Store this favorite product id
		$this->favoriteProductId = $newFavoriteProductId;
	}
	/**
	 * @return int
	 */
	public function getFavoriteProfileId(): ?int {
		return ($this->favoriteProfileId);
	}
	/**
	 * @param int $favoriteProfileId
	 */
	public function setFavoriteProfileId(?int $newFavoriteProfileId) {
		if ($newFavoriteProfileId === null) {
			$this->$newFavoriteProfileId = null;
			return;
		}
		if ($newFavoriteProfileId <= 0) {
			throw (new \RangeException("Favorite profile id is not positive"));
		}
		//Store this profile id
		$this->favoriteProfileId = $newFavoriteProfileId;
	}
	/**
	 * @return \DateTime
	 */
	public function getFavoriteDate(): \DateTime {
		return ($this->favoriteDate);
	}
	public function setFavoriteDate($newFavoriteDate = null) : void
	{
		if ($newFavoriteDate === null) {
			$this->favoriteDate = new \DateTime();
			return;
		}
		try {
			$newFavoriteDate = self::validateDate($newFavoriteDate);
		} catch (\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->favoriteDate = $newFavoriteDate;
	}
	/**
	 * insets into mySQL
	 * @param \PDO $pdo connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError of $pdo is not a PDO connection object
	 *
	 */
	public function insert(\PDO $pdo) : void {
		if ($this->favoriteProfileId === null) {
			throw(new \PDOException("this favorite does not exist"));
		}
		$query = "INSERT INTO favorite(favoriteProfileId, favoriteProductId, favortieDate) VALUES (:favoriteProfileId, :favoriteProductId, :favoriteDate)";
		$statement = $pdo->prepare($query);
		$formattedDate = $this->tweetDate->format("Y-m-d H:i:s");
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId, "favoriteProductId" => $this->favoriteProductId, "favoriteDate" => $formattedDate];
		$statement->execute($parameters);
	}
	/**
	 * deletes favorite from mySQL
	 * @param \PDO $pdo connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError of $pdo is not a PDO connection object
	 *
	 */
	public function delete(\PDO $pdo) : void
	{
		if ($this->favoriteProfileId === null) {
			throw(new \PDOException("unable to delete favorite that does note exisit"));
		}
		// create query
		$query = "DELETE FROM favorite WHERE favoriteProfileId = :favoriteProfileId";
		$statement = $pdo->prepare($query);
		$parameters = ["favoriteProfileId" => $this->favoriteProfileId];
		$statement->execute($parameters);
	}
	/**
	 * gets favorite by favorite profile id
	 * @param \PDO $pdo PDO connection object
	 * @param int $favoriteProfileId profile id to search for
	 * @return $favoriteProfileId
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getFavoriteByFavoriteProfileId(\PDO $pdo, int $favoriteProfileId) : ?Favorite {
		// sanitize this id
		if($favoriteProfileId <= 0) {
			throw(new \PDOException("this favorite profile id is not positive"));
		}
		$query = "SELECT favoriteProfileId, favoriteProductId, favortieDate FROM favorite WHERE favoriteProfileId =:favoriteProfileId";
		$statement = $pdo->prepare($query);
		$parameters = ["favoriteProfileId" => $favoriteProfileId];
		$statement ->execute($parameters);
		//fetch favorite from mySQL
		try {
			$favoriteProfileId = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$favorite = new Favorite($row ["favoriteProfileId"], $row ["favoriteProductId"], $row ["favoriteDate"]);
			}
		} catch (\Exception $exception){
			//if the row is unable to to convert, rethrow
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($favorite);
	}
}