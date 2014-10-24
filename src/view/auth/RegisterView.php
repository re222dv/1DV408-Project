<?php

namespace view\auth;

use model\entities\auth\User;
use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;

class RegisterView extends View {
    const TV_ERRORS = 'errors';
    const TV_PASSWORD = 'password';
    const TV_PASSWORD2 = 'password2';
    const TV_REGISTER = 'register';
    const TV_USERNAME = 'username';

    protected $template = 'auth/register.html';

    public function __construct(InputDirective $inputDirective, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $inputDirective->registerInput($this, self::TV_REGISTER);
        $inputDirective->registerInput($this, self::TV_PASSWORD);
        $inputDirective->registerInput($this, self::TV_PASSWORD2);
        $inputDirective->registerInput($this, self::TV_USERNAME);
    }

    /**
     * @return User
     */
    public function getUser() {
        $user = new User();
        try {
            $user->setUsername($this->variables[self::TV_USERNAME]);
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

        if ($this->variables[self::TV_PASSWORD] !== $this->variables[self::TV_PASSWORD2]) {
            $this->addError('The passwords does not match');
        } else {
            try {
                $user->setPassword($this->variables[self::TV_PASSWORD]);
            } catch (\InvalidArgumentException $e) {
                $this->addError('The password can not be empty');
            }
        }

        return $user;
    }

    public function addUsernameExistsError() {
        $this->addError('The username is taken');
    }

    /**
     * @return bool
     */
    public function haveRegistered() {
        return isset($this->variables[self::TV_REGISTER]);
    }

    /**
     * @param string $message
     */
    private function addError($message) {
        $this->variables[self::TV_ERRORS][] = $message;
    }
}
