<?php

namespace view\auth;

use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;
use view\services\Router;

class LoginView extends View {
    protected $template = 'auth/login.html';

    public function __construct(InputDirective $inputDirective, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $inputDirective->registerInput($this, 'username');
        $inputDirective->registerInput($this, 'password');
        $inputDirective->registerInput($this, 'login');

        $this->variables['registerUrl'] = Router::REGISTER;
    }

    public function getUsername() {
        return $this->variables['username'];
    }

    public function getPassword() {
        return $this->variables['password'];
    }

    public function haveLoggedIn() {
        return isset($this->variables['login']);
    }

    public function loginFailed() {
        $this->variables['loginFailed'] = true;
    }
}
