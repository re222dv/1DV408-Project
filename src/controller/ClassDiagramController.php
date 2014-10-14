<?php

namespace controller;

use model\diagrams\ClassDiagram;
use model\mesh\Network;
use view\mesh\NetworkView;
use view\services\Router;

class ClassDiagramController {
    /**
     * @var NetworkView
     */
    private $view;

    public function __construct(Router $router, NetworkView $view) {
        $classDiagram = new ClassDiagram($router->getFilename());
        $network = new Network($classDiagram);
        $this->view = $view;
        $view->setNetwork($network);
    }

    public function render() {
        return $this->view->render();
    }
}
