<?php

namespace controller;

use model\services\Auth;
use view\MasterView;
use view\services\Router;

class MasterController {
    /**
     * @var Auth
     */
    private $auth;
    /**
     * @var AuthController
     */
    private $authController;
    /**
     * @var FileController
     */
    private $fileController;
    /**
     * @var InputController
     */
    private $inputController;
    /**
     * @var MasterView
     */
    private $masterView;
    /**
     * @var MyDiagramsController
     */
    private $myDiagramsController;
    /**
     * @var Router
     */
    private $router;

    public function __construct(Auth $auth, Router $router, AuthController $authController,
                                FileController $fileController,
                                InputController $inputController,
                                MyDiagramsController $myDiagramsController,
                                MasterView $masterView) {
        $this->auth = $auth;
        $this->router = $router;
        $this->authController = $authController;
        $this->fileController = $fileController;
        $this->inputController = $inputController;
        $this->masterView = $masterView;
        $this->myDiagramsController = $myDiagramsController;
    }

    public function render() {
        $this->masterView->setAuth($this->authController->render());

        switch($this->router->getCurrentPath()) {
            case Router::INDEX:
                $this->masterView->setMain($this->inputController->render());
                break;

            case Router::MY_DIAGRAMS:
                if ($this->auth->isLoggedIn()) {
                    $this->masterView->setMain($this->myDiagramsController->render());
                } else {
                    $this->router->redirectTo(Router::INDEX);
                    return null;
                }
                break;

            case Router::REGISTER:
                $this->masterView->setMain($this->authController->register());
                break;

            case Router::FILE:
                return $this->fileController->render();

            default:
                if ($this->router->isDiagram()) {
                    $this->masterView->setMain($this->inputController->render());
                } else {
                    $this->router->redirectTo(Router::INDEX);
                    return null;
                }
        }

        return $this->masterView->render();
    }
}
