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

    public function testSelect() {
        $users = $this->database->select(User::class);

        $this->assertEquals(1, count($users));
        $this->assertEquals('Admin', $users[0]->getUsername());
        $this->assertTrue($users[0]->verifyPassword('Password'));
    }
}

class User {
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

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        return $this->username = $username;
    }

    public function setHash($hash) {
        return $this->hash = $hash;
    }

    public function verifyPassword($password) {
        return password_verify($password, $this->hash);
    }
}
