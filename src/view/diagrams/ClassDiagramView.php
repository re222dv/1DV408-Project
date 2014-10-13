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

        $classes = [];

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);
            $this->variables['classes'][] = $view;
            $classes[$class->getName()] = $view;
        }

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];
            $to = $classes[$association->getTo()];

            if ($from->depth >= $to->depth) {
                $to->depth = $from->depth + 1;
            }
        }
    }
}
