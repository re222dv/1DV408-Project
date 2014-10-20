<?php

namespace view\auth;

use model\services\Auth;
use Template\directives\InputDirective;
use Template\View;
use Template\ViewSettings;
use view\services\Router;

class UserView extends View {
    const TV_LOGOUT = 'logout';
    const TV_USERNAME = 'username';
    const TV_MY_DIAGRAMS_URL = 'myDiagramsUrl';

    protected $template = 'auth/user.html';
    /**
     * @var Auth
     */
    private $auth;

    public function __construct(Auth $auth, InputDirective $inputDirective,
                                ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $this->auth = $auth;

        $inputDirective->registerInput($this, self::TV_LOGOUT);
    }

    /**
     * @return bool
     */
    public function haveLoggedOut() {
        return isset($this->variables[self::TV_LOGOUT]);
    }

    public function onRender() {
        $this->variables[self::TV_USERNAME] = $this->auth->getUser()->getUsername();
        $this->variables[self::TV_MY_DIAGRAMS_URL] = Router::MY_DIAGRAMS;
    }
}
