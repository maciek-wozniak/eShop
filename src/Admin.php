<<<<<<< HEAD
<?php

require_once 'DbConnection.php';

Class Admin {
    private $id;
    private $email;


    public function __construct() {
        $this->id = -1;
        $this->email = '';
        $this->password = '';
    }

    public function getId() {
        return $this->id;
    }

    public function setEmail( $newEmail) {
        if (filter_var($newEmail, FILTER_VALIDATE_EMAIL) !== false && !empty($newEmail))  {
            $this->email = $newEmail;
        }
        return false;
    }

    public function getEmail() {
        return $this->email;
    }

    public function addAdmin(mysqli $conn, $password) {
        if ($this->id == -1) {
            throw new Exception('Bad admin id');
        }

        if (strlen($password) < 3) {
            throw new Exception('Password too short');
        }

        $sqlAdminCount = $conn->real_escape_string('SELECT id FROM admin WHERE email=' . $this->getId());
        $result = $conn->query($sqlAdminCount);

        if ($result->num_rows != 0) {
            throw new Exception('Admin with that email already is registerd');
        }

        $password = $this->hashPassword($password);
        $sqlAddAdmin = $conn->real_escape_string('INSERT INTO admin (email, password) VALUES
                                            ("'.$this->getEmail().'", "'.$password.'") ');
        return $conn->query($sqlAddAdmin);

    }

    public function loadAdminFromDb(mysqli $conn, $adminEmail) {
        $sqlAdminCount = $conn->real_escape_string('SELECT id FROM admin WHERE email=' . $adminEmail);
        $result = $conn->query($sqlAdminCount);

        if ($result != 1) {
            throw new Exception ('Failed to load admin from database');
        }

        $row = $result->fetch_assoc();
        $newAdmin = new Admin();
        $newAdmin->setEmail($row['email']);
        $newAdmin->id = $row['id'];

        return $newAdmin;
    }

    public function updateAdmin(mysqli $conn, $password = null) {
        if ($this->id == -1) {
            throw new Exception('Cant update not existing admin');
        }

        $sqlSelectUser = $conn->real_escape_string('SELECT id FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);

        $row = $result->fetch_assoc();
        if ($this->getId() != $row['id']) {
            throw new Exception('You can update only yourself');
        }


        if ($result->num_rows != 1) {
            throw new Exception('Cant update admin');
        }

        $password_sql = '';
        if (!is_null($password) && strlen($password) > 3) {
            $password = $this->hashPassword($password);
            $password_sql = ', password="'.$password.'"';
        }

        $sqlUpdate = $conn->real_escape_string('UPDATE admin SET email="'.$this->getEmail().'"'.$password_sql.' ');
        $result = $conn->query($sqlUpdate);

        if ($result === false) {
            throw new Exception('Failed to update admin');
        }

        $_SESSION['admin'] = null;
        $admin = new Admin();
        $admin->setEmail($this->getEmail());
        $_SESSION['admin'] = $admin;
        return true;

    }

    public function deleteAdmin(mysqli $conn) {
        $sqlSelectUser = $conn->real_escape_string('SELECT id FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);

        if ($this->getId() == -1) {
            throw new Exception('Cant delete not existing object (admin)');
        }

        if ($result->num_rows != 1) {
            throw new Exception('User don not exist');
        }

        $row = $result->fetch_assoc();
        if ($this->id != $row['ud']) {
            throw new Exception('You can delete only yourself');
        }

        $sqlDelete = $conn->real_escape_string('DELETE FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlDelete);
        if ($result === false) {
            throw new Exception('Failed to delete admin');
        }
        return true;
    }

    public function logInAdmin(mysqli $conn, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            throw new Exception('Wrong email address or empty password');
        }

        $sqlSelectUser = $conn->real_escape_string('SELECT * FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);
        if ($result->num_rows != 1) {
            throw new Exception('Cant log in');
        }

        $row = $result->fetch_assoc();

        if (!password_verify($password, $row['password'])) {
            return new Exception('Wrong password');
        }

        $admin = new Admin();
        $admin->setEmail($row['email']);
        $_SESSION['admin'] = $admin;

        return true;
    }

    private function hashPassword($password) {
        $options = array(
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
        );
        return password_hash($password, CRYPT_BLOWFISH, $options);
    }

=======
<?php


Class Admin {
    private $id;
    private $email;


    public function __construct() {
        $this->id = -1;
        $this->email = '';
        $this->password = '';
    }

    public function getId() {
        return $this->id;
    }

    public function setEmail( $newEmail) {
        if (filter_var($newEmail, FILTER_VALIDATE_EMAIL) !== false && !empty($newEmail))  {
            $this->email = $newEmail;
        }
        return false;
    }

    public function getEmail() {
        return $this->email;
    }

    public function addAdmin(mysqli $conn, $password) {
        if ($this->id == -1) {
            throw new Exception('Bad admin id');
        }

        if (strlen($password) < 3) {
            throw new Exception('Password too short');
        }

        $sqlAdminCount = $conn->real_escape_string('SELECT id FROM admin WHERE email=' . $this->getId());
        $result = $conn->query($sqlAdminCount);

        if ($result->num_rows != 0) {
            throw new Exception('Admin with that email already is registerd');
        }

        $password = $this->hashPassword($password);
        $sqlAddAdmin = $conn->real_escape_string('INSERT INTO admin (email, password) VALUES
                                            ("'.$this->getEmail().'", "'.$password.'") ');
        return $conn->query($sqlAddAdmin);

    }

    public function loadAdminFromDb(mysqli $conn, $adminEmail) {
        $sqlAdminCount = $conn->real_escape_string('SELECT id FROM admin WHERE email=' . $adminEmail);
        $result = $conn->query($sqlAdminCount);

        if ($result != 1) {
            throw new Exception ('Failed to load admin from database');
        }

        $row = $result->fetch_assoc();
        $newAdmin = new Admin();
        $newAdmin->setEmail($row['email']);
        $newAdmin->id = $row['id'];

        return $newAdmin;
    }

    public function updateAdmin(mysqli $conn, $password = null) {
        if ($this->id == -1) {
            throw new Exception('Cant update not existing admin');
        }

        $sqlSelectUser = $conn->real_escape_string('SELECT id FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);

        $row = $result->fetch_assoc();
        if ($this->getId() != $row['id']) {
            throw new Exception('You can update only yourself');
        }


        if ($result->num_rows != 1) {
            throw new Exception('Cant update admin');
        }

        $password_sql = '';
        if (!is_null($password) && strlen($password) > 3) {
            $password = $this->hashPassword($password);
            $password_sql = ', password="'.$password.'"';
        }

        $sqlUpdate = $conn->real_escape_string('UPDATE admin SET email="'.$this->getEmail().'"'.$password_sql.' ');
        $result = $conn->query($sqlUpdate);

        if ($result === false) {
            throw new Exception('Failed to update admin');
        }

        $_SESSION['admin'] = null;
        $admin = new Admin();
        $admin->setEmail($this->getEmail());
        $_SESSION['admin'] = $admin;
        return true;

    }

    public function deleteAdmin(mysqli $conn) {
        $sqlSelectUser = $conn->real_escape_string('SELECT id FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);

        if ($this->getId() == -1) {
            throw new Exception('Cant delete not existing object (admin)');
        }

        if ($result->num_rows != 1) {
            throw new Exception('User don not exist');
        }

        $row = $result->fetch_assoc();
        if ($this->id != $row['ud']) {
            throw new Exception('You can delete only yourself');
        }

        $sqlDelete = $conn->real_escape_string('DELETE FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlDelete);
        if ($result === false) {
            throw new Exception('Failed to delete admin');
        }
        return true;
    }

    public function logInAdmin(mysqli $conn, $email, $password) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password)) {
            throw new Exception('Wrong email address or empty password');
        }

        $sqlSelectUser = $conn->real_escape_string('SELECT * FROM admin WHERE id='.$this->getId());
        $result = $conn->query($sqlSelectUser);
        if ($result->num_rows != 1) {
            throw new Exception('Cant log in');
        }

        $row = $result->fetch_assoc();

        if (!password_verify($password, $row['password'])) {
            return new Exception('Wrong password');
        }

        $admin = new Admin();
        $admin->setEmail($row['email']);
        $_SESSION['admin'] = $admin;

        return true;
    }

    private function hashPassword($password) {
        $options = array(
            'cost' => 11,
            'salt' => mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)
        );
        return password_hash($password, CRYPT_BLOWFISH, $options);
    }

>>>>>>> 3a9997bb522a54a8853f9197f9162baf81ca3c38
}