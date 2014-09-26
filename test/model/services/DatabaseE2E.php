<?php

namespace model\services;

require_once(__DIR__.'/../../../src/model/services/Database.php');

class DatabaseE2E extends \PHPUnit_Framework_TestCase {
    /**
     * @var Database
     */
    private $database;

    public function __construct() {
        $this->database = new Database();
    }

    public function testCreation() {
        $this->database->assertTable(User::class);
    }

    public function testInsert() {
        $this->database->insert(new User('Admin', 'Password'));
    }

    public function testInsertWithSave() {
        $this->database->save(new User('User', 'MyCatsName'));
    }

    public function testSelect() {
        $users = $this->database->select(User::class);

        $this->assertEquals(2, count($users));
        $this->assertEquals('Admin', $users[0]->getUsername());
        $this->assertTrue($users[0]->verifyPassword('Password'));
        $this->assertEquals('User', $users[1]->getUsername());
        $this->assertTrue($users[1]->verifyPassword('MyCatsName'));
    }

    public function testUpdate() {
        $user = $this->database->select(User::class, '', [], 1);
        $user->setUsername('Administrator');
        $this->database->save($user);

        $user = $this->database->select(User::class, '', [], 1);
        $this->assertEquals('Administrator', $user->getUsername());
    }

    public function testGet() {
        $selectUsers = $this->database->select(User::class);
        $ids = [];
        foreach ($selectUsers as $user) {
            $ids[] = $user->getId();
        }

        $user = $this->database->get(User::class, $selectUsers[0]->getId());
        $users = $this->database->get(User::class, $ids);

        $this->assertEquals($selectUsers[0], $user);
        $this->assertEquals($selectUsers, $users);
    }

    public function testDelete() {
        $users = $this->database->select(User::class);
        $this->database->delete($users[0]);

        $users = $this->database->select(User::class);
        $this->assertEquals(1, count($users));
    }

    public function testDeleteAll() {
        $users = $this->database->select(User::class);
        $this->database->delete($users);

        $users = $this->database->select(User::class);
        $this->assertEquals(0, count($users));
    }
}

class User {
    /**
     * @var int A special id that is set to the database id
     */
    private $id;
    /**
     * [column varchar(20)]
     */
    private $username;
    /**
     * [column varchar(256)]
     */
    private $hash;

    public function __construct($username, $password) {
        $this->username = $username;
        $this->hash = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getId() {
        return $this->id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        return $this->username = $username;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->hash);
    }
}
