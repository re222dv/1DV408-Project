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
     * @var ClassDiagramController
     */
    private $classDiagramController;
    /**
     * @var InputController
     */
    private $inputController;
    /**
     * @var MasterView
     */
    private $masterView;
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router, AuthController $authController,
                                ClassDiagramController $classDiagramController,
                                InputController $inputController, MasterView $masterView) {
        $this->router = $router;
        $this->authController = $authController;
        $this->classDiagramController = $classDiagramController;
        $this->inputController = $inputController;
        $this->masterView = $masterView;
    }

    public function render() {
        if ($this->router->isFile()) {
            return $this->classDiagramController->render();
        }

        $this->masterView->setAuth($this->authController->render());

        switch($this->router->getCurrentPath()) {
            case Router::INDEX:
                $this->masterView->setMain($this->inputController->render());
                break;

            case Router::REGISTER:
                $this->masterView->setMain($this->authController->register());
        }

        return $this->masterView->render();
    }
}
