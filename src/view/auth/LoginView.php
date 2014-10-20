<?php

namespace view\auth;

use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;
use view\services\Router;

class LoginView extends View {
    const TV_LOGIN = 'login';
    const TV_LOGIN_FAILED = 'loginFailed';
    const TV_PASSWORD = 'password';
    const TV_USERNAME = 'username';
    const TV_REGISTER_URL = 'registerUrl';

    protected $template = 'auth/login.html';

    public function __construct(InputDirective $inputDirective, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $inputDirective->registerInput($this, self::TV_LOGIN);
        $inputDirective->registerInput($this, self::TV_PASSWORD);
        $inputDirective->registerInput($this, self::TV_USERNAME);

        $this->variables[self::TV_REGISTER_URL] = Router::REGISTER;
    }

    public function getUsername() {
        return $this->variables[self::TV_USERNAME];
    }

    public function getPassword() {
        return $this->variables[self::TV_PASSWORD];
    }

    public function haveLoggedIn() {
        return isset($this->variables[self::TV_LOGIN]);
    }

    public function loginFailed() {
        $this->variables[self::TV_LOGIN_FAILED] = true;
    }
}
