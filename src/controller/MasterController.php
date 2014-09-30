<?php

namespace controller;

use view\diagrams\ClassDiagramView;
use view\services\Router;

class MasterController {
    /**
     * @var ClassDiagramView
     */
    private $classDiagramController;
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router, ClassDiagramController $classDiagramController) {
        $this->router = $router;
        $this->classDiagramController = $classDiagramController;
    }

    public function render() {
        return $this->classDiagramController->render();
    }
}
