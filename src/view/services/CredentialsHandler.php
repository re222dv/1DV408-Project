<?php

namespace view\services;

use model\entities\auth\Token;

class CredentialsHandler {
    const CV_SECRET = 'secret';

    public function deleteSecret() {
        unset($_COOKIE[self::CV_SECRET]);
        setcookie(self::CV_SECRET, null, 1);
    }

    public function getSecret () {
        if ($this->hasSecret()) {
            return $_COOKIE[self::CV_SECRET];
        }

        return null;
    }

    public function hasSecret() {
        return isset($_COOKIE[self::CV_SECRET]);
    }

    public function saveSecret(Token $token) {
        setcookie(self::CV_SECRET, $token->getSecret(), $token->getExpirationDate());
        $_COOKIE[self::CV_SECRET] = $token->getSecret();
    }
}
