<?php

Class User {

    private $id;
    private $name;
    private $surname;
    private $email;
    private $isDeleted;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        if (is_string($name) && strlen($name) > 2) {
            $this->name = $name;
        }
        throw new Exception('Wrong user name');
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        if (is_string($surname) && strlen($surname) > 2) {
            $this->surname = $surname;
        }
        throw new Exception('Wrong user surname');
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        }
        throw new Exception('Wrong email address');
    }

    public function getIsDeleted() {
        return $this->isDeleted;
    }

    public function setIsDeleted($isDeleted) {
        if (in_array($isDeleted, [0, 1, true, false])) {
            $this->isDeleted = $isDeleted;
        }
        throw new Exception('Wrong value');
    }

    public function __construct() {
        $this->id = -1;
        $this->email = '';
        $this->name = '';
        $this->surname = '';
        $this->isDeleted = -1;
    }

    public function addUser(mysqli $conn) {
        if ($this->getId() == -1) {
            if ($this->getEmail() == '' || $this->getName() == '' || $this->getSurname() == '') {
                throw new Exception('Missing user data');
            }
            $sql = 'INSERT INTO users (email, name, surname) VALUES
              ("'.$this->getEmail().'", "'.$this->getName().'", "'.$this->getSurname().'")';
        }
        throw new Exception('User already registered');
    }

    public function updateUser(mysqli $conn, $mail, $name, $surname) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
            throw new Exception('Wrong email address');
        }

        $sql = 'SELECT id FROM users WHERE id='.$this->getId();
        $result = $conn->query($sql);
        if ($result->num_rows !=1) {
            throw new Exception('Fatal error, no such user');
        }
        $row = $result->fetch_assoc();
        if ($this->getId() != $row['id']) {
            throw new Exception('You can only update yourself!');
        }

        $sqlIsUser = 'SELECT email FROM users WHERE deleted=0 AND email="'.$mail.'" ';
        $result = $conn->query($sqlIsUser);
        if ($result->num_rows == 1 && !($this->email == $mail)) {
            throw new Exception('Email address already taken');

        }
        $sqlIsUser = 'SELECT * FROM users WHERE deleted=0 AND (email="'.$mail.'" OR id="'.$this->userId.'") ';
        $result = $conn->query($sqlIsUser);
        if ($result->num_rows != 1) {
            throw new Exception('Something went wrong');
        }
        $updateUserQuery = 'UPDATE users SET surname="'.$surname.'", name="'.$name.'", email="'.$mail
                                .'" WHERE id="'.$this->userId.'"';
        $result = $conn->query($updateUserQuery);
        if ($result) {
            unset($_SESSION['user']);
            $this->setEmail($mail);
            $this->setSurname($surname);
            $this->setName($name);
            $_SESSION['user'] = $this;
        }
        return $result;
    }

    public function updateUserPassword(mysqli $conn, $oldPassword, $newPassword, $confirmPassword) {
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            throw new Exception('Empty password');
        }
        if ($newPassword != $confirmPassword) {
            throw new Exception('Confirm password dont match new password');
        }

        $getUserQuery = 'SELECT * FROM users WHERE deleted=0 AND id=' . $this->getId();
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (!password_verify($oldPassword, $user['password'])) {
                throw new Exception('Wrong password');
            }

            $hashedNewPassword = $this->hashPassword($newPassword);
            $updateUserQuery = 'UPDATE users SET password="' . $hashedNewPassword . '" WHERE id="' . $this->getId() . '"';
            return $conn->query($updateUserQuery);
        }
        throw new Exception('Somethig went wrong');
    }

    public function deleteUser(mysqli $conn) {
        $sql = 'UPDATE user SET is_deleted=1 WHERE id='.$this->getId();
        return $conn->query($sql);
    }

    public function loadUserFromDb(mysqli $conn, $userId, $onlyWhenNotDeleted = null) {
        if (!is_numeric($userId) || $userId < 1) {
            throw new Exception('Wrong user id');
        }

        $sql = 'SELECT * FROM users WHERE id='.$userId;
        if ($onlyWhenNotDeleted === true) {
            $sql .= ' AND deleted=0';
        }
        $result = $conn->query($sql);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            $user = new User();
            $user->id = $row['id'];
            $user->setName($row['name']);
            $user->setSurname($row['surname']);
            $user->setEmail($row['email']);
            $user->setIsDeleted($row['is_deleted']);

            return $user;
        }
        throw new Exception('Something went wrong');
    }

    public function logInUser(mysqli $conn, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !is_string($password) || strlen($password) < 3) {
            throw new Exception('Wrong email address or password');
        }

        $sqlUser = 'SELECT * FROM users WHERE is_deleted=0 AND email='.$email;
        $result = $conn->query($sqlUser);
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (!password_verify($password, $row['password'])) {
                throw new Exception ('Wrong password');
            }

            $user = new User();
            $user->id = $row['id'];
            $user->setName($row['name']);
            $user->setSurname($row['surname']);
            $user->setEmail($row['email']);
            $user->setIsDeleted($row['is_deleted']);

            $_SESSION['user'] = $user;
            return true;
        }
        throw new Exception('No user in DB');
    }

    public function logOutUser() {
        unset($_SESSION['user']);
    }

    public function getUserOrders(mysqli $conn) {
        //return Address::GetUserOrders($conn, $this->getId()); <-- TO IMPLEMENT
    }

    public function getUserAddresses(mysqli $conn) {
        return Address::GetUserAddresses($conn, $this->getId());
    }

    static public function GetAllUsers(mysqli $conn, $selectAlsoDeletedUsers = null) {
        $allUsers = [];
        $sql = 'SELECT * FROM users';
        if ($selectAlsoDeletedUsers !== true) {
            $sql .= ' WHERE is_deleter=0';
        }

        $result = $conn->query($sql);
        if ($result->num_rows>0) {
            while ($row = $result->fetch_assoc()) {
                $user = new User() ;
                $user->id = $row['id'];
                $user->setEmail($row['email']);
                $user->setName($row['name']);
                $user->setSurname($row['surname']);
                $user->setIsDeleted($row['is_deleted']);

                $allUsers[] = $user;
            }
        }
        return $allUsers;
    }

    private function hashPassword($password) {
        $options = array(
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
        );
        return password_hash($password, CRYPT_BLOWFISH, $options);
    }
}