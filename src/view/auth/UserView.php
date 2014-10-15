<?php

namespace view\auth;

use model\services\Auth;
use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;

class UserView extends View {
    protected $template = 'auth/user.html';
    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth, InputDirective $inputDirective,
                                ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $this->auth = $auth;

        $inputDirective->registerInput($this, 'logout');
    }

    /**
     * @return bool
     */
    public function haveLoggedOut() {
        return isset($this->variables['logout']);
    }

    public function onRender() {
        $this->variables['username'] = $this->auth->getUser()->getUsername();
    }
}
