<?php

require_once (dirname(__FILE__).'/../src/User.php');

Class User_Test extends PHPUnit_Extensions_Database_TestCase {
    static private $pdo = null;
    private $conn = null;
    private $mySqli;
    private $user;

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
        $dataSet->addTable('user', dirname(__FILE__)."/fixtures/users.csv") ;
        return $dataSet;
    }

    protected function setUp() {
        parent::setUp();
        if ($this->mySqli === null) {
            $this->mySqli = new mysqli('localhost', $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD'],  $GLOBALS['DB_DBNAME']);
        }
        $this->user = new User();
    }
    protected function tearDown() {
        parent::tearDown();
        $this->user = null;
    }

    public function testAddUser() {
        $user = $this->user;
        $user->setName('kalafior');
        $user->setSurname('kalafiorowy');
        $user->setEmail('kalafior@warzywowo.pl');

        $this->assertTrue($user->addUser($this->mySqli, 'kalafior'));
    }

    public function testAddUserWrongData() {
        $this->setExpectedException('Exception');
        $this->assertInstanceOf('User', $this->user = User::LoadUserFromDb($this->mySqli, 1));
        $this->user->addUser($this->mySqli, 'kalafior');
    }

    public function testEmailSetter() {
        $this->user->setEmail('kas.ia@my.tech');
        $this->user->setEmail('my-mail@black.black');
        $this->user->setEmail('admin_adi@adult.xxx');
        $this->user->setEmail('kasia@google.com.pl');
        $this->user->setEmail('kasia@wp.pl');
    }

    public function testNameSetter() {
        $this->user->setName('asda');
        $this->user->setName('123');
        $this->user->setName('karol555');
        $this->user->setName('ada@koniec.pl');
    }

    public function testSurnameSetter() {
        $this->user->setSurname('asda');
        $this->user->setSurname('123');
        $this->user->setSurname('karol555');
        $this->user->setSurname('ada@koniec.pl');
    }

    public function testNameSettersWrongData1() {
        $this->setExpectedException('Exception');
        $this->user->setName(-1);
    }

    public function testNameSettersWrongData2() {
        $this->setExpectedException('Exception');
        $this->user->setName(null);
    }

    public function testNameSettersWrongData3() {
        $this->setExpectedException('Exception');
        $this->user->setName(666);
    }

    public function testNameSettersWrongData4() {
        $this->setExpectedException('Exception');
        $this->user->setName(true);
    }

    public function testNameSettersWrongData5() {
        $this->setExpectedException('Exception');
        $this->user->setName(false);
    }

    public function testNameSettersWrongData6() {
        $this->setExpectedException('Exception');
        $this->user->setName([[455]]);
    }

    public function testNameSettersWrongData7() {
        $this->setExpectedException('Exception');
        $this->user->setName(['test']);
    }

    public function testNameSettersWrongData8() {
        $this->setExpectedException('Exception');
        $this->user->setName('');
    }

    public function testEmailSetterWrongData1() {
        $this->setExpectedException('Exception');
        $this->user->setEmail(-1);
    }

    public function testEmailSetterWrongData2() {
        $this->setExpectedException('Exception');
        $this->user->setEmail(true);
    }

    public function testEmailSetterWrongData3() {
        $this->setExpectedException('Exception');
        $this->user->setEmail(666);
    }

    public function testEmailSetterWrongData4() {
        $this->setExpectedException('Exception');
        $this->user->setEmail([3245]);
    }

    public function testEmailSetterWrongData5() {
        $this->setExpectedException('Exception');
        $this->user->setEmail(['adsa']);
    }

    public function testEmailSetterWrongData6() {
        $this->setExpectedException('Exception');
        $this->user->setEmail(null);
    }

    public function testEmailSetterWrongData7() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('asda');
    }

    public function testEmailSetterWrongData8() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('@wp.pl');
    }

    public function testEmailSetterWrongData9() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('kasia@@wp.pl');
    }

    public function testEmailSetterWrongData10() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('kasia@wppl');
    }

    public function testEmailSetterWrongData11() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('kasia.wp.pl');
    }

    public function testEmailSetterWrongData12() {
        $this->setExpectedException('Exception');
        $this->user->setEmail('');
    }

    public function testSurnameSetterWrongData1() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(true);
    }

    public function testSurnameSetterWrongData2() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(false);
    }

    public function testSurnameSetterWrongData3() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(-1);
    }

    public function testSurnameSetterWrongData4() {
        $this->setExpectedException('Exception');
        $this->user->setSurname('l');
    }

    public function testSurnameSetterWrongData5() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(555);
    }

    public function testSurnameSetterWrongData6() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(['adsa']);
    }

    public function testSurnameSetterWrongData7() {
        $this->setExpectedException('Exception');
        $this->user->setSurname(null);
    }

    public function testSurnameSetterWrongData8() {
        $this->setExpectedException('Exception');
        $this->user->setSurname('');
    }

    public function testUpdateUser() {
        $user = $this->user;
        $user->setName('karol');
        $user->setEmail('karol@wp.pl');
        $user->setSurname('kowalski');
        $user->addUser($this->mySqli, 'katana');

        $user->updateUser($this->mySqli, 'kolega@wp.pl', 'kamil', 'nowak');
        $id = $user->getId();
        $newUser = User::LoadUserFromDb($this->mySqli, $id);

        $this->assertSame('kolega@wp.pl', $newUser->getEmail());
        $this->assertSame('kamil', $newUser->getName());
        $this->assertSame('nowak', $newUser->getSurname());
    }

    public function testUpdateUserWrongData1() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail', 'name', 'surname');
    }

    public function testUpdateUserWrongData2() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, null, 'name', 'surname');
    }

    public function testUpdateUserWrongData3() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, -1, 'name', 'surname');
    }

    public function testUpdateUserWrongData4() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, true, 'name', 'surname');
    }

    public function testUpdateUserWrongData5() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@mail', 'name', 'surname');
    }

    public function testUpdateUserWrongData6() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, '@wp.pl', 'name', 'surname');
    }

    public function testUpdateUserWrongData7() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, '', 'name', 'surname');
    }

    public function testUpdateUserWrongData8() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', '', 'surname');
    }

    public function testUpdateUserWrongData9() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', true, 'surname');
    }

    public function testUpdateUserWrongData10() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', -1, 'surname');
    }

    public function testUpdateUserWrongData11() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 666, 'surname');
    }

    public function testUpdateUserWrongData12() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', null, 'surname');
    }

    public function testUpdateUserWrongData13() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', ['name'], 'surname');
    }

    public function testUpdateUserWrongData14() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', '');
    }

    public function testUpdateUserWrongData15() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', true);
    }

    public function testUpdateUserWrongData16() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', null);
    }

    public function testUpdateUserWrongData17() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', -1);
    }

    public function testUpdateUserWrongData18() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', 666);
    }

    public function testUpdateUserWrongData19() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 1)->updateUser($this->mySqli, 'mail@wp.pl', 'name', ['6666']);
    }

    public function testUpdatePassword() {
        $user = User::LoadUserFromDb($this->mySqli, 1);
        $this->assertTrue($user->updateUserPassword($this->mySqli, 'user1', 'test23', 'test23'));
        $this->assertTrue($user->logInUser($this->mySqli, 'test23'));

        $user = User::LoadUserFromDb($this->mySqli, 3);
        $this->assertTrue($user->updateUserPassword($this->mySqli, 'user3', 'kalamarnica', 'kalamarnica'));
        $this->assertTrue($user->logInUser($this->mySqli, 'kalamarnica'));

    }

    public function testUpdatePasswordWrongData1() {
        $this->setExpectedException('Exception');
        $this->user->updateUserPassword($this->mySqli, 'aaa', 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData2() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'aaa', 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData3() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, true, 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData4() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, -1, 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData5() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, '', 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData6() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, null, 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData7() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, ['user3'], 'bbb', 'bbb');
    }

    public function testUpdatePasswordWrongData8() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', 'aaa', 'bbb');
    }

    public function testUpdatePasswordWrongData9() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', null, null);
    }

    public function testUpdatePasswordWrongData10() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', true, true);
    }

    public function testUpdatePasswordWrongData11() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', '', '');
    }

    public function testUpdatePasswordWrongData12() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', 666, 666);
    }

    public function testUpdatePasswordWrongData13() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', -1, -1);
    }

    public function testUpdatePasswordWrongData14() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', ['bbb'], ['bbb']);
    }

    public function testUpdatePasswordWrongData15() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 2)->updateUserPassword($this->mySqli, 'user3', null, 'bbb');
    }

    public function testDeleteUser1() {
        $this->assertTrue(User::LoadUserFromDb($this->mySqli, 2)->deleteUser($this->mySqli));
    }

    public function testDeleteUser2() {
        $user = $this->user;
        $user->setName('arek');
        $user->setSurname('karwowski');
        $user->setEmail('arek@koko.pl');

        $this->assertTrue($user->addUser($this->mySqli, 'haslo'));
        $this->assertTrue($user->deleteUser($this->mySqli));
    }

    public function testDeleteUserWrongData1() {
        $this->setExpectedException('Exception');
        $this->user->deleteUser($this->mySqli);
    }

    public function testDeleteUserWrongData2() {
        $this->setExpectedException('Exception');
        $this->user = User::LoadUserFromDb($this->mySqli, 3);
        $this->user->deleteUser($this->mySqli);
        $this->user->deleteUser($this->mySqli);
    }

    public function testLoadUserFromDb() {
        $user = User::LoadUserFromDb($this->mySqli, 1);
        $this->assertInstanceOf('User', $user);
        $this->assertSame('user1', $user->getName());
        $this->assertSame('user1@user.pl', $user->getEmail());
        $this->assertSame('sruname1', $user->getSurname());
        $user = null;

        $user = User::LoadUserFromDb($this->mySqli, 1, true);
        $this->assertInstanceOf('User', $user);
        $this->assertSame('user1', $user->getName());
        $this->assertSame('user1@user.pl', $user->getEmail());
        $this->assertSame('sruname1', $user->getSurname());
        $user->deleteUser($this->mySqli);
        $user = null;

        try {
            User::LoadUserFromDb($this->mySqli, 1, true);
        }
        catch (Exception $e) {
            $this->assertSame('Something went wrong', $e->getMessage());
            return;
        }
        $this->fail('Exception not thrown');
    }

    public function testLoadUserFromDbWrongData1() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, -1);
    }

    public function testLoadUserFromDbWrongData2() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 7000);
    }

    public function testLoadUserFromDbWrongData3() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, 'asda');
    }

    public function testLoadUserFromDbWrongData4() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, true);
    }

    public function testLoadUserFromDbWrongData5() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, false);
    }

    public function testLoadUserFromDbWrongData6() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, null);
    }

    public function testLoadUserFromDbWrongData7() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, '');
    }

    public function testLoadUserFromDbWrongData8() {
        $this->setExpectedException('Exception');
        User::LoadUserFromDb($this->mySqli, [4523]);
    }

    public function testLoginUser() {
        $user = User::LoadUserFromDb($this->mySqli, 1);
        $this->assertTrue($user->logInUser($this->mySqli, 'user1'));
    }

    public function testLoginUserWrongData1() {
        $user = $this->user;
        $user->setName('marta');
        $user->setEmail('marta@wp.pl');
        $user->setSurname('kowalska');
        $user->addUser($this->mySqli, 'faraon');
        $this->assertTrue($user->logInUser($this->mySqli, 'faraon'));
        $this->assertTrue($this->user->updateUserPassword($this->mySqli, 'faraon', 'aaa', 'aaa'));

        $this->setExpectedException('Exception');
        $user->logInUser($this->mySqli, 'faraon');
    }

    public function testLoginUserWrongData2() {
        $user = $this->user;
        $user->setName('karolina');
        $user->setEmail('karolina@wp.pl');
        $user->setSurname('nowak');
        $user->addUser($this->mySqli, 'karkowka');
        $this->assertTrue($user->logInUser($this->mySqli, 'karkowka'));
        $this->assertTrue($this->user->deleteUser($this->mySqli));

        $this->setExpectedException('Exception');
        $user->logInUser($this->mySqli, 'karkowka');
    }

    public function testLoginUserWrongData3() {
        $this->setExpectedException('Exception');
        $this->user->logInUser($this->mySqli, 'faraon');
    }

    public function testLoginUserWrongData4() {
        $this->setExpectedException('Exception');
        $user = User::LoadUserFromDb($this->mySqli, 4);
        $user->logInUser($this->mySqli, 'use4');
    }

    public function testLoginUserWrongData5() {
        $this->setExpectedException('Exception');
        $user = User::LoadUserFromDb($this->mySqli, 4);
        $user->logInUser($this->mySqli, '');
    }

    public function testLoginUserWrongData6() {
        $this->setExpectedException('Exception');
        $user = User::LoadUserFromDb($this->mySqli, 4);
        $user->logInUser($this->mySqli, null);
    }

    public function testLoginUserWrongData7() {
        $this->setExpectedException('Exception');
        $user = User::LoadUserFromDb($this->mySqli, 4);
        $user->logInUser($this->mySqli, true);
    }

    public function testLoginUserWrongData8() {
        $this->setExpectedException('Exception');
        $user = User::LoadUserFromDb($this->mySqli, 4);
        $user->logInUser($this->mySqli, -1);
    }

    public function testGetAllUsers() {
        $users = User::GetAllUsers($this->mySqli);
        $this->assertTrue(is_array($users));
        $this->assertInstanceOf('User', $users[0]);

        foreach ($users as $user) {
            if ($user->getId() == 4) {
                $this->assertSame('user4', $user->getName());
                $this->assertSame('sruname4', $user->getSurname());
                $this->assertSame('user4@user.pl', $user->getEmail());
            }
        }
    }

}
