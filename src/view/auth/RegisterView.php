<?php

namespace view\auth;

use model\entities\auth\User;
use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;

class RegisterView extends View {
    protected $template = 'auth/register.html';

    public function __construct(InputDirective $inputDirective, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $inputDirective->registerInput($this, 'username');
        $inputDirective->registerInput($this, 'password');
        $inputDirective->registerInput($this, 'password2');
        $inputDirective->registerInput($this, 'register');
    }

    /**
     * @return User
     */
    public function getUser() {
        if ($this->variables['password'] !== $this->variables['password2']) {
            $this->addError('The passwords does not match');
        }

        $user = new User();
        try {
            $user->setUsername($this->variables['username']);
        } catch (\InvalidArgumentException $e) {
            $length = $e->getMessage();
            switch ($e->getCode()) {
                case User::TOO_SHORT:
                    $this->addError("The username is too short, a minimum of $length characters is required");
                    break;
                case User::TOO_LONG:
                    $this->addError("The username is too long, a maximum of $length characters is required");
                    break;
            }
        }

        $user->setPassword($this->variables['password']);

        return $user;
    }

    public function addUsernameExistsError() {
        $this->addError('The username is taken');
    }

    /**
     * @return bool
     */
    public function haveRegistered() {
        return isset($this->variables['register']);
    }

    /**
     * @param string $message
     */
    private function addError($message) {
        $this->variables['errors'][] = $message;
    }
}
