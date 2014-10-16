<?php

namespace controller;

use model\services\Auth;
use view\MyDiagramsView;

class MyDiagramsController {
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var MyDiagramsView
     */
    private $myDiagramsView;

    public function __construct(Auth $auth, MyDiagramsView $myDiagramsView) {
        $this->auth = $auth;
        $this->myDiagramsView = $myDiagramsView;
    }

    public function render() {
        $this->myDiagramsView->setDiagrams([]);

        return $this->myDiagramsView;
    }
}
