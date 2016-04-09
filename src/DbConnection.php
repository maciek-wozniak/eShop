<?php

require_once 'Config.php';

Class DbConnection {


    public static function getConnection() {
        $config = new Config();
        if ($conn = new mysqli($config->getServerName(), $config->getUserName(), $config->getPassword(), $config->getDatabase())) {
            return $conn;
        }
        return false;
    }
}



$conn = DbConnection::getConnection();

if ($conn == false) {
    die ('Brak połączenia');
}