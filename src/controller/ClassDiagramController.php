<?php

namespace controller;

use model\entities\umls\ClassDiagram;
use view\diagrams\ClassDiagramView;
use view\services\Router;

class ClassDiagramController {
    /**
     * @var ClassDiagramView
     */
    private $view;

    public function __construct(Router $router, ClassDiagramView $view) {
        $classDiagram = new ClassDiagram($router->getFilename());
        $this->view = $view;
        $view->setDiagram($classDiagram);
    }

    public function render() {
        return $this->view->render();
    }
}
