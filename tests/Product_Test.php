<?php

require_once (dirname(__FILE__).'/../src/Product.php');

class User_Test extends PHPUnit_Extensions_Database_TestCase
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
                self::$pdo = new PDO ( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }

        return $this->conn;
    }

    protected function getDataSet()
    {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $dataSet->addTable("product", dirname(__FILE__)."/fixtures/products.csv");
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
        $this->product->query($this->mysqliConn);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->product = null;
    }

}