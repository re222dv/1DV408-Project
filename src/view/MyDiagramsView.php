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

    public function shouldDelete() {
        if (isset($_POST['delete'])) {
            return $_POST['id'];
        }

        return null;
    }
}

class DiagramViewModel {
    public $name;
    public $id;
    public $umls;

    public function __construct(Diagram $diagram) {
        $this->name = $diagram->getName();
        $this->id = $diagram->getId();
        $this->umls = rawurlencode($diagram->getUmls());
    }
}
