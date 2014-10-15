<?php

namespace model\repositories;

use model\entities\auth\Token;
use model\services\Database;

class TokenRepository {

    /**
     * @var Database
     */
    public $database;

    public function __construct(Database $database) {
        $this->database = $database;
        $this->database->assertTable(Token::class);
    }

    /**
     * @param Token $token
     */
    public function insert(Token $token) {
        $this->database->insert($token);
    }

    /**
     * @param string $secret
     * @return Token
     * @throws \InvalidArgumentException if the token is invalid
     */
    public function getBySecret($secret) {
        $token = $this->database->select(Token::class, '`secret` = ?', [$secret], 1);

        if (!$token or !$token->isValid()) {
            throw new \InvalidArgumentException('Token is not valid');
        }

        return $token;
    }
}
