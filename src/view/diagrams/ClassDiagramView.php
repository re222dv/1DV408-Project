<?php

namespace view\diagrams;

use model\diagrams\ClassDiagram;
use Template\View;
use view\entities\ClassObjectView;

class ClassDiagramView extends View {
    protected $template = 'diagrams/classDiagram.svg';
    private $classDiagram;

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        $this->setVariable('classes', []);

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);
            $this->variables['classes'][] = $view;
        }
    }
}
