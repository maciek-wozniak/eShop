<?php

require_once 'DbConnection.php';

class Product{

    static public function showAllProducts(mysqli $conn)
    {
        $allProducts = array();

        $loadFromDBQuery = "SELECT * FROM Product";

        $result = $conn->query($loadFromDBQuery);

        if($result != FALSE) {
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $product = new Product();
                    $product->id = $row['id'];
                    $product->setName($row['name']);
                    $product->setDescription($row['description']);
                    $product->setPrice($row['price']);
                    $product->setQuantity($row['quantity']);

                    //adding the object to an array as new element
                    $allProducts[] = $product;
                }
            }
            return $allProducts;
        }
        $conn->close();
        $conn = null;

        return false;
    }

    private $id;
    private $name;
    private $description;
    private $price;
    private $quantity;
    private $isDeleted;

    public function __construct()
    {
        $this->id = -1;
        $this->setName('');
        $this->setDescription('');
        $this->price = -1;
        $this->quantity = -1;
        $this->isDeleted = 0;
    }

    public function addConnection(mysqli $newConn){
        $this->conn = $newConn;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($newName)
    {
        //TODO:: check why this condition doesn't stop the script
        if(strlen($newName) > 0){
            $this->name = $newName;
        }

        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($newDescription)
    {
        $this->description = $newDescription;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setPrice($newPrice)
    {
        if($newPrice >= 0) {
            $this->price = $newPrice;
        }
        return false;
    }

    public function getPrice()
    {
        return $this->price;
    }

    public function setQuantity($newQuantity)
    {
        if($newQuantity >= 0) {
            $this->quantity = $newQuantity;
        } else {
            throw new Exception("Quantity has to be >= 0");
        }
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setIsDeleted($isDeleted = 0)
    {
        try {
            if(!$isDeleted >= 0 ){
                throw new Exception ("IsDeleted flag has to be set at >= 0");
            } else {
                $this->isDeleted = $isDeleted;
                return true;
            }
        } catch (Exception $e) {
            return $e->getMessage().$e->getLine();
        }
    }

    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    //using set'ers on front-end and create object
    //having the object, call addProduct method to save it to DB
    public function addProduct(mysqli $conn)
    {
        if($this->getName() != null && $this->getPrice()>0) {
            if ($this->id === -1) {
                $addProductQuery = "INSERT INTO product(name, description, price, quantity, is_deleted)
                          VALUES(
                                '{$this->getName()}',
                                '{$this->getDescription()}',
                                '{$this->getPrice()}',
                                '{$this->getQuantity()}',
                                '{$this->getIsDeleted()}'
                                )";

                $result = $conn->query($addProductQuery);

                if ($result === TRUE) {
                    $this->id = $conn->insert_id;

                    $conn->close();
                    $conn = null;

                    return $result;
                }
            }
        }
        return false;
    }

    public function updateProduct(mysqli $conn)
    {
        if(isset($this->id)) {
            $updateProductQuery = "UPDATE product SET
                                name={$this->getName()},
                                description={$this->getDescription()},
                                price={$this->getPrice()},
                                quantity={$this->getQuantity()},
                                is_deleted={$this->getIsDeleted()}
                                WHERE id={$this->getId()}
                                ";

            $result = $conn->query($updateProductQuery);
            if($result === TRUE) {

                $conn->close();
                $conn = null;

                return true;
            }
        }
        return false;
    }

    public function loadProductFromDB(mysqli $conn, $idToFind)
    {
        $loadFromDBQuery = "SELECT * FROM Product WHERE product_id={$idToFind}";

        $result = $conn->query($loadFromDBQuery);

        if($result != FALSE) {
            if($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $this->id = $row['id'];
                $this->setName($row['name']);
                $this->setDescription($row['description']);
                $this->setPrice($row['price']);
                $this->setQuantity($row['quantity']);
                $this->setIsDeleted($row['is_deleted']);
            }
        }
        $conn->close();
        $conn = null;

        return false;
    }

    public function showProduct()
    {
        //checking if the product was loaded from DB (id != -1)
        if(isset($this->id) && $this->id >= 0) {
            $product = '';
            $product .= "<div class='panel panel-default'>";
            $product .= "<div class='panel-body'>Product name: {$this->getName()}</div>";
            $product .= "<div class='panel-body'>Product description: {$this->getDescription()}</div>";
            $product .= "<div class='panel-body'>Product unit price: {$this->getPrice()}</div>";
            $product .= "<div class='panel-footer'>Available units: {$this->getQuantity()}</div>";
            $product .= "</div>";

            return $product;
        }
        return false;
    }
}
