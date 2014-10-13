<?php

namespace controller;

use view\MasterView;
use view\services\Router;

class MasterController {
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

    public function __construct(Router $router, ClassDiagramController $classDiagramController,
                                InputController $inputController, MasterView $masterView) {
        $this->router = $router;
        $this->classDiagramController = $classDiagramController;
        $this->inputController = $inputController;
        $this->masterView = $masterView;
    }

    public function render() {
        if ($this->router->isFile()) {
            return $this->classDiagramController->render();
        } elseif ($this->router->isInput()) {
            $this->masterView->setMain($this->inputController->render());
            return $this->masterView->render();
        }
    }
}
