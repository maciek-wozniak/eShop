<?php


Class Address {
    private $id;
    private $user_id;
    private $street;
    private $city;
    private $post_code;
    private $country;



    static public function GetUserAddresses(mysqli $conn, $userId) {
        $userAdressess = [];

        // TO DO ... //

        return $userAdressess;
    }
}