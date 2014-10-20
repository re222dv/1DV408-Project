<?php

namespace view;

use model\entities\Diagram;
use Template\View;
use view\services\Router;


class MyDiagramsView extends View {
    protected $template = 'myDiagrams.html';

    /**
     * @param Diagram[] $diagrams
     */
    public function setDiagrams($diagrams) {
        $this->variables['diagrams'] = [];

        foreach ($diagrams as $diagram) {
            $this->variables['diagrams'][] = new DiagramViewModel($diagram);
        }
    }

    public function onRender() {
        $this->setVariable('diagramUrl', Router::DIAGRAM_FORMAT);
    }
}

class DiagramViewModel {
    public $name;
    public $id;

    public function __construct(Diagram $diagram) {
        $this->name = $diagram->getName();
        $this->id = $diagram->getId();
    }
}
