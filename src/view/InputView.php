<?php

namespace view;

use model\entities\umls\ClassDiagram;
use Template\View;
use Template\ViewSettings;
use view\umls\ClassDiagramView;

class InputView extends View {
    protected $template = 'input.html';

    public function __construct(ClassDiagramView $classDiagramView, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);
        $this->setVariable('diagram', $classDiagramView);
    }

    public function getUmls() {
        if (isset($_GET['umls'])) {
            return urldecode($_GET['umls']);
        }
        return null;
    }

    public function shouldRender() {
        return isset($_GET['umls']);
    }

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->getVariable('diagram')->setDiagram($classDiagram);
    }

    public function onRender() {
        $this->setVariable('umls', $this->getUmls());
    }
}
