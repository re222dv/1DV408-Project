<?php

namespace controller;

use view\MasterView;
use view\services\Router;

class MasterController {
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

    public function __construct(Router $router, AuthController $authController,
                                FileController $fileController,
                                InputController $inputController,
                                MyDiagramsController $myDiagramsController,
                                MasterView $masterView) {
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

                $this->masterView->setMain($this->myDiagramsController->render());
                break;

            case Router::REGISTER:
                $this->masterView->setMain($this->authController->register());
                break;

            case Router::FILE:
                return $this->fileController->render();

            default:
                if ($this->router->isDiagram()) {
                    $this->masterView->setMain($this->inputController->render());
                }
        }

        return $this->masterView->render();
    }
}
