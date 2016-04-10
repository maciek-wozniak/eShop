<?php

require_once (dirname(__FILE__).'/../src/Admin.php');

Class Admin_Test extends PHPUnit_Extensions_Database_TestCase {
    static private $pdo = null;
    private $conn = null;
    private $mySqli;
    private $admin;

    protected function getConnection() {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = new PDO( $GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'] );
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
        }
        return $this->conn;
    }

    protected function getDataSet() {
        $dataSet = new PHPUnit_Extensions_Database_DataSet_CsvDataSet();
        $dataSet->addTable('admin', dirname(__FILE__)."/fixtures/admin.csv") ;
        return $dataSet;
    }

    protected function setUp() {
        parent::setUp();
        if ($this->mySqli === null) {
            $this->mySqli = new mysqli('localhost', $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'],  $GLOBALS['DB_DBNAME']);
        }
        $this->admin = new Admin();
    }
    protected function tearDown() {
        parent::tearDown();
        $this->admin = null;
    }

    public function testAddAdmin() {
        $admin = new Admin();

        // $this->setExpectedException('Exception');
        // $admin->addAdmin($this->mySqli, 'ala ma kota');

        try {
            $admin->addAdmin($this->mySqli, 'ala ma kota');
        }
        catch (Exception $e) {
            $this->assertSame('Bad admin id', $e->getMessage());
            return;
        }

        $this->fail('Expected Exception is not thrown');

    }
}