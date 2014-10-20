<?php

namespace controller;

use model\entities\umls\ClassDiagram;
use view\services\Router;
use view\umls\ClassDiagramView;

class ClassDiagramController {
    /**
     * @var ClassDiagramView
     */
    private $classDiagramView;

    public function __construct(Router $router, ClassDiagramView $view) {
        $classDiagram = new ClassDiagram($router->getFilename());
        $this->classDiagramView = $view;
        $view->setDiagram($classDiagram);
    }

    /**
     * @return ClassDiagramView
     */
    public function render() {
        $this->classDiagramView->setMimeType();
        return $this->classDiagramView->render();
    }
}
