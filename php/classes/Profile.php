<?php
namespace Edu\Cnm\kkristl\DataDesign;
require_once ("autoload.php");
/**
 * Small cross section of a product favorite message
 *
 * this product can be considered a small example of what services like etsy store when products are favorited using etsy. this can easily be extended to emulate more features of etsy.
 *
 * @author Kelly Kristl <kkristl@cnm.edu>
 * @version
 */
class Product implements \JsonSerializable {
	use ValidateDate;
	/**
	 * id for this product; this is the primary key
	 * @var int $productId
	 */
	private $productId;
	/**
	 * id of the profile that sent this product; this is a foreign key
	 * @var int $productProfileId
	 */
	private $productProfileId;
	/**
	 * actual textual content of this product
	 * @var string $productContent
	 */
	private $productContent;
	/**
	 * date and time this product was swent, in a PHP DateTime object
	 * @var \DateTime $productDate
	 */
	private $productDate;
	/**
	 *constructor for this product
	 *
	 * @param int|null $newProductId id of this product or null if a new product
	 * @param int $newproductProfileId id of the Profile that sent this product
	 * @param string $newProductContent string containing actual product data
	 * @param \DateTime|string|null $newproductDate date and time product was sent or null if set to current date and time
	 * @throws \InvalidArgumentException if data types are not valid
	 * @throws \RangeException if data values are out of bounds (e.g., strings too long, negative integers)
	 * @throws \TypeError if data types violate type hints
	 * @throws \Exception if some other exception occurs
	 * @Documentation https://php.net/manual/en/language.oop5.decon.php
	 **/
	public function __construct(?int $newProductId, int $newproductProfileId, string $newProductContent, $newproductDate = null) {
		try {
			$this->setProductId($newProductId);
			$this->setproductProfileId($newproductProfileId);
			$this->setProductContent($newProductContent);
			$this->setproductDate($newproductDate);
		}
			//determine what exception type was thrown
		catch(\InvalidArgumentException | \RangeException | \Exception | \TypeError $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
	}
	/**
	 * accessor method for product id
	 *
	 * @return int|null value of product id
	 **/
	public function getProductId() : ?int {
		return($this->productId);
	}
	/**
	 * mutator method for product id
	 *
	 * @param int|null $newProductId new value of product id
	 * @throws \RangeException if $newProductId is not positive
	 * @throws \TypeError if $newProductId is not an integer
	 **/
	public function setProductId(?int $newProductId) : void {
		//if product id is null immediately return it
		if($newProductId === null) {
			$this->productId = null;
			return;
		}
		// verify the product id is positive
		if($newProductId <= 0) {
			throw(new \RangeException("product id is not positive"));
		}
		// convert and store the product id
		$this->productId = $newProductId;
	}
	/**
	 * accessor method for product profile id
	 *
	 * @return int value of product profile id
	 **/
	public function getproductProfileId() : int{
		return($this->productProfileId);
	}
	/**
	 * mutator method for product profile id
	 *
	 * @param int $newproductProfileId new value of product profile id
	 * @throws \RangeException if $newProfileId is not positive
	 * @throws \TypeError if $newProfileId is not an integer
	 **/
	public function setproductProfileId(int $newproductProfileId) : void {
		// verify the profile id is positive
		if($newproductProfileId <= 0) {
			throw(new \RangeException("product profile id is not positive"));
		}
		// convert and store the profile id
		$this->productProfileId = $newproductProfileId;
	}
	/**
	 * accessor method for product content
	 *
	 * @return string value of product content
	 **/
	public function getProductContent() :string {
		return($this->productContent);
	}
	/**
	 * mutator method for product content
	 *
	 * @param string $newProductContent new value of product content
	 * @throws \InvalidArgumentException if $newProductContent is not a string or insecure
	 * @throws \RangeException if $newProductContent is > 140 characters
	 * @throws \TypeError if $newProductContent is not a string
	 **/
	public function setProductContent(string $newProductContent) : void {
		// verify the product content is secure
		$newProductContent = trim($newProductContent);
		$newProductContent = filter_var($newProductContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($newProductContent) === true) {
			throw(new \InvalidArgumentException("product content is empty or insecure"));
		}
		// verify the product content will fit in the database
		if(strlen($newProductContent) > 140) {
			throw(new \RangeException("product content too large"));
		}
		// store the product content
		$this->productContent = $newProductContent;
	}
	/**
	 * accessor method for product date
	 *
	 * @return \DateTime value of product date
	 **/
	public function getproductDate() : \DateTime {
		return($this->productDate);
	}
	/**
	 * mutator method for product date
	 *
	 * @param \DateTime|string|null $newproductDate product date as a DateTime object or string (or null to load the current time)
	 * @throws \InvalidArgumentException if $newproductDate is not a valid object or string
	 * @throws \RangeException if $newproductDate is a date that does not exist
	 **/
	public function setproductDate($newproductDate = null) : void {
		// base case: if the date is null, use the current date and time
		if($newproductDate === null) {
			$this->productDate = new \DateTime();
			return;
		}
		// store the like date using the ValidateDate trait
		try {
			$newproductDate = self::validateDateTime($newproductDate);
		} catch(\InvalidArgumentException | \RangeException $exception) {
			$exceptionType = get_class($exception);
			throw(new $exceptionType($exception->getMessage(), 0, $exception));
		}
		$this->productDate = $newproductDate;
	}
	/**
	 * inserts this product into mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function insert(\PDO $pdo) : void {
		// enforce the productId is null (i.e., don't insert a product that already exists)
		if($this->productId !== null) {
			throw(new \PDOException("not a new product"));
		}
		// create query template
		$query = "INSERT INTO product(productProfileId, productContent, productDate) VALUES(:productProfileId, :productContent, :productDate)";
		$statement = $pdo->prepare($query);
		// bind the member variables to the place holders in the template
		$formattedDate = $this->productDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productContent" => $this->productContent, "productDate" => $formattedDate];
		$statement->execute($parameters);
		// update the null productId with what mySQL just gave us
		$this->productId = intval($pdo->lastInsertId());
	}
	/**
	 * deletes this product from mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function delete(\PDO $pdo) : void {
		// enforce the productId is not null (i.e., don't delete a product that hasn't been inserted)
		if($this->productId === null) {
			throw(new \PDOException("unable to delete a product that does not exist"));
		}
		// create query template
		$query = "DELETE FROM product WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// bind the member variables to the place holder in the template
		$parameters = ["productId" => $this->productId];
		$statement->execute($parameters);
	}
	/**
	 * updates this product in mySQL
	 *
	 * @param \PDO $pdo PDO connection object
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError if $pdo is not a PDO connection object
	 **/
	public function update(\PDO $pdo) : void {
		// enforce the productId is not null (i.e., don't update a product that hasn't been inserted)
		if($this->productId === null) {
			throw(new \PDOException("unable to update a product that does not exist"));
		}
		// create query template
		$query = "UPDATE product SET productProfileId = :productProfileId, productContent = :productContent, productDate = :productDate WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// bind the member variables to the place holders in the template
		$formattedDate = $this->productDate->format("Y-m-d H:i:s");
		$parameters = ["productProfileId" => $this->productProfileId, "productContent" => $this->productContent, "productDate" => $formattedDate, "productId" => $this->productId];
		$statement->execute($parameters);
	}
	/**
	 * gets the product by content
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param string $productContent product content to search for
	 * @return \SplFixedArray SplFixedArray of products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductContent(\PDO $pdo, string $productContent) {
		// sanitize the description before searching
		$productContent = trim($productContent);
		$productContent = filter_var($productContent, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
		if(empty($productContent) === true) {
			throw(new \PDOException("product content is invalid"));
		}
		// create query template
		$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productContent LIKE :productContent";
		$statement = $pdo->prepare($query);
		// bind the product content to the place holder in the template
		$productContent = "%$productContent%";
		$parameters = ["productContent" => $productContent];
		$statement->execute($parameters);
		// build an array of products
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($products);
	}
	/**
	 * gets the product by productId
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $productId product id to search for
	 * @return product|null product found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByProductId(\PDO $pdo, int $productId) : ?product {
		// sanitize the productId before searching
		if($productId <= 0) {
			throw(new \PDOException("product id is not positive"));
		}
		// create query template
		$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productId = :productId";
		$statement = $pdo->prepare($query);
		// bind the product id to the place holder in the template
		$parameters = ["productId" => $productId];
		$statement->execute($parameters);
		// grab the product from mySQL
		try {
			$product = null;
			$statement->setFetchMode(\PDO::FETCH_ASSOC);
			$row = $statement->fetch();
			if($row !== false) {
				$product = new product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
			}
		} catch(\Exception $exception) {
			// if the row couldn't be converted, rethrow it
			throw(new \PDOException($exception->getMessage(), 0, $exception));
		}
		return($product);
	}
	/**
	 * gets the product by profile id
	 *
	 * @param \PDO $pdo PDO connection object
	 * @param int $productProfileId profile id to search by
	 * @return \SplFixedArray SplFixedArray of products found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getProductByproductProfileId(\PDO $pdo, int $productProfileId) : \SPLFixedArray {
		// sanitize the profile id before searching
		if($productProfileId <= 0) {
			throw(new \RangeException("product profile id must be positive"));
		}
		// create query template
		$query = "SELECT productId, productProfileId, productContent, productDate FROM product WHERE productProfileId = :productProfileId";
		$statement = $pdo->prepare($query);
		// bind the product profile id to the place holder in the template
		$parameters = ["productProfileId" => $productProfileId];
		$statement->execute($parameters);
		// build an array of products
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return($products);
	}
	/**
	 * gets all products
	 *
	 * @param \PDO $pdo PDO connection object
	 * @return \SplFixedArray SplFixedArray of products found or null if not found
	 * @throws \PDOException when mySQL related errors occur
	 * @throws \TypeError when variables are not the correct data type
	 **/
	public static function getAllProducts(\PDO $pdo) : \SPLFixedArray {
		// create query template
		$query = "SELECT productId, productProfileId, productContent, productDate FROM product";
		$statement = $pdo->prepare($query);
		$statement->execute();
		// build an array of products
		$products = new \SplFixedArray($statement->rowCount());
		$statement->setFetchMode(\PDO::FETCH_ASSOC);
		while(($row = $statement->fetch()) !== false) {
			try {
				$product = new product($row["productId"], $row["productProfileId"], $row["productContent"], $row["productDate"]);
				$products[$products->key()] = $product;
				$products->next();
			} catch(\Exception $exception) {
				// if the row couldn't be converted, rethrow it
				throw(new \PDOException($exception->getMessage(), 0, $exception));
			}
		}
		return ($products);
	}
	/**
	 * formats the state variables for JSON serialization
	 *
	 * @return array resulting state variables to serialize
	 **/
	public function jsonSerialize() {
		$fields = get_object_vars($this);
		//format the date so that the front end can consume it
		$fields["productDate"] = round(floatval($this->productDate->format("U.u")) * 1000);
		return($fields);
	}
}