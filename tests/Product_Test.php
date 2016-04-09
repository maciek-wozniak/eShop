<?php

require_once (dirname(__FILE__).'/../src/Product.php');

class Product_Test extends PHPUnit_Extensions_Database_TestCase
{

    private $product;
    private $mysqliConn;

    // only instantiate pdo once for test clean-up/fixture load
    static private $pdo = null;

    // only instantiate PHPUnit_Extensions_Database_DB_IDatabaseConnection once per test
    private $conn = null;

    public function getConnection()
    {
        if($this->conn == null) {

            if(self::$pdo == null) {
                self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    protected function getDataSet()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $dataSet->addTable("product", dirname(__FILE__)."/fixtures/products.csv");
        return $dataSet;
    }

    public static function setUpBeforeClass(){

    }

    public static function tearDownAfterClass(){

    }

    protected function setUp()
    {
        parent::setUp();
        $this->product = new Product();

        if($this->mysqliConn === null) {
            $this->mysqliConn = new mysqli("localhost", $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'], $GLOBALS['DB_DBNAME']);
        }

        //TODO: can we use other way to establish connection without addConnection method?
        $this->product->addConnection($this->mysqliConn);

        echo "*** setUp ***";
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->product = null;
    }

    public function testTest(){
        $this->assertSame(1, 1, "true");
    }

    public function testAddProductCorrectData(){
        //testing adding product with correct data
        $isAddedProduct = new Product();
        $isAddedProduct->setName('pinapple');
        $isAddedProduct->setDescription('great pinable');
        $isAddedProduct->setPrice('10');
        $isAddedProduct->setQuantity('100');
        $isAddedProduct->setIsDeleted('0');

        $this->assertSame(true, $isAddedProduct->addProduct($this->mysqliConn));

    }

    public function testAddProductWrongData()
    {
        //testing adding product with wrong Name
        $isAddedProduct = new Product();
        $isAddedProduct->setName('');
        $isAddedProduct->setDescription('great pinable');
        $isAddedProduct->setPrice('10');
        $isAddedProduct->setQuantity('100');
        $isAddedProduct->setIsDeleted('0');
        $this->assertSame(false, $isAddedProduct->addProduct($this->mysqliConn));
    }

    public function testAddProductWrongData2()
    {
        $isAddedProduct = new Product();

        //testing adding product with wrong Price < 0
        $isAddedProduct->setName('pinapple');
        $isAddedProduct->setDescription('great pinable');
        $isAddedProduct->setPrice('-1');
        $isAddedProduct->setQuantity('100');
        $isAddedProduct->setIsDeleted('0');
        $this->assertSame("wrong data", $isAddedProduct->addProduct($this->mysqliConn));

        //testing adding product with Quantity < 0
        $isAddedProduct->setPrice('1');
        $isAddedProduct->setQuantity('-1');
        $isAddedProduct->setIsDeleted('0');
        $this->assertSame('Exception: Quantity has to be >= 0', $isAddedProduct->addProduct($this->mysqliConn));

        //testing adding product with IsDeleted true (1)
        $isAddedProduct->setQuantity('1');
        $isAddedProduct->setIsDeleted('1');
        $this->assertSame(false, $isAddedProduct->addProduct($this->mysqliConn));

    }

    public function testDeleteProduct(){
        //testing changing product's flag to is_deleted = true
        $isAddedProduct = new Product();
        $isAddedProduct->loadProductFromDB($this->mysqliConn, 1);
        $isAddedProduct->setIsDeleted(1);
        $isAddedProduct->updateProduct($this->mysqliConn);
    }



}

