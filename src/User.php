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
        if (!is_string($name) || strlen($name) < 3) {
            throw new Exception('Wrong user name');
        }
        $this->name = $name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function setSurname($surname) {
        if (!is_string($surname) || strlen($surname) < 3) {
            throw new Exception('Wrong user surname');
        }
        $this->surname = $surname;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Wrong email address');
        }
        $this->email = $email;
    }

    public function getIsDeleted() {
        return $this->isDeleted;
    }

    public function setIsDeleted($isDeleted) {
        if (!in_array($isDeleted, [0, 1, true, false])) {
            throw new Exception('Wrong value');
        }
        $this->isDeleted = $isDeleted;
    }

    public function __construct() {
        $this->id = -1;
        $this->email = '';
        $this->name = '';
        $this->surname = '';
        $this->isDeleted = -1;
    }

    public function addUser(mysqli $conn, $password) {
        if ($this->getId() == -1) {
            if ($this->getEmail() == '' || $this->getName() == '' || $this->getSurname() == '') {
                throw new Exception('Missing user data');
            }

            $password = self::hashPassword($password);

            $sql = 'INSERT INTO user (email, name, surname, password) VALUES
              ("'.$this->getEmail().'", "'.$this->getName().'", "'.$this->getSurname().'", "'.$password.'")';
            $result =  $conn->query($sql);
            if ($result === true) {
                $this->id = $conn->insert_id;
            }
            return $result;
        }
        throw new Exception('User already registered');
    }

    public function updateUser(mysqli $conn, $mail, $name, $surname) {
        if (!filter_var($mail, FILTER_VALIDATE_EMAIL) ) {
            throw new Exception('Wrong email address');
        }

        if (!is_string($name) || !is_string($surname)) {
            throw new Exception('Wrong data');
        }

        $sql = 'SELECT id FROM user WHERE id='.$this->getId();
        $result = $conn->query($sql);
        if ($result->num_rows !=1) {
            throw new Exception('Fatal error, no such user');
        }
        $row = $result->fetch_assoc();
        if ($this->getId() != $row['id']) {
            throw new Exception('You can only update yourself!');
        }

        $sqlIsUser = 'SELECT email FROM user WHERE is_deleted=0 AND email="'.$mail.'" ';
        $result = $conn->query($sqlIsUser);
        if ($result->num_rows == 1 && !($this->email == $mail)) {
            throw new Exception('Email address already taken');

        }
        $sqlIsUser = 'SELECT * FROM user WHERE is_deleted=0 AND (email="'.$mail.'" OR id="'.$this->getId().'") ';
        $result = $conn->query($sqlIsUser);
        if ($result->num_rows != 1) {
            throw new Exception('Something went wrong');
        }
        $updateUserQuery = 'UPDATE user SET surname="'.$surname.'", name="'.$name.'", email="'.$mail
                                .'" WHERE id="'.$this->getId().'"';
        $result = $conn->query($updateUserQuery);
        if ($result) {
            $this->setEmail($mail);
            $this->setSurname($surname);
            $this->setName($name);
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

        if (!is_string($newPassword) || !is_string($oldPassword)) {
            throw new Exception('Not supported data type');
        }

        $getUserQuery = 'SELECT * FROM user WHERE is_deleted=0 AND id=' . $this->getId();
        $result = $conn->query($getUserQuery);
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (!password_verify($oldPassword, $user['password'])) {
                throw new Exception('Wrong password');
            }

            $hashedNewPassword = self::hashPassword($newPassword);
            $updateUserQuery = 'UPDATE user SET password="' . $hashedNewPassword . '" WHERE id="' . $this->getId() . '"';
            return $conn->query($updateUserQuery);
        }
        throw new Exception('Somethig went wrong');
    }

    public function deleteUser(mysqli $conn) {
        if ($this->getId() < 1) {
            throw new Exception('User doesnt exists');
        }

        $sql = 'SELECT id FROM user WHERE is_deleted=0 AND id='.$this->getId();
        $result = $conn->query($sql);
        if ($result->num_rows != 1) {
            throw new Exception('User doesnt exists');
        }

        $sql = 'UPDATE user SET is_deleted=1 WHERE id=' . $this->getId();
        return $conn->query($sql);
    }

    static public function LoadUserFromDb(mysqli $conn, $userId, $onlyWhenNotDeleted = null) {
        if (!is_numeric($userId) || $userId < 1) {
            throw new Exception('Wrong user id');
        }

        $sql = 'SELECT * FROM user WHERE id='.$userId;
        if ($onlyWhenNotDeleted === true) {
            $sql .= ' AND is_deleted=0';
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

    public function logInUser(mysqli $conn, $password) {
        if (!filter_var($this->getEmail(), FILTER_VALIDATE_EMAIL) || !is_string($password) || strlen($password) < 3) {
            throw new Exception('Wrong email address or password');
        }

        $sqlUser = 'SELECT * FROM user WHERE is_deleted=0 AND email="'.$this->getEmail().'"';
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

    public function getUserOrders(mysqli $conn) {
        //return Address::GetUserOrders($conn, $this->getId()); <-- TO IMPLEMENT
    }

    public function getUserAddresses(mysqli $conn) {
        return Address::GetUserAddresses($conn, $this->getId());
    }

    static public function GetAllUsers(mysqli $conn, $selectAlsoDeletedUsers = null) {
        $allUsers = [];
        $sql = 'SELECT * FROM user';
        if ($selectAlsoDeletedUsers !== true) {
            $sql .= ' WHERE is_deleted=0';
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

    static public function hashPassword($password) {
        $options = array(
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
        );
        return password_hash($password, CRYPT_BLOWFISH, $options);
    }
}