<?php

namespace view\diagrams;

use model\diagrams\ClassDiagram;
use Template\View;
use view\entities\AssociationView;
use view\entities\ClassObjectView;

class ClassDiagramView extends View {
    protected $template = 'diagrams/classDiagram.svg';
    /**
     * @var ClassDiagram
     */
    private $classDiagram;

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        $this->setVariable('classes', []);
        $this->setVariable('associations', []);

        $classes = [];

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);
            $this->variables['classes'][] = $view;
            $classes[$class->getName()] = $view;
        }

        $this->positionClasses($classes);

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];
            $to = $classes[$association->getTo()];

            $view = new AssociationView($this->settings);
            $view->setAssociation($association, $from, $to);
            $this->variables['associations'][] = $view;
        }
    }

    private function positionClasses($classes) {
        $positions = [];

        $this->positionClassesBelowAssociativeClasses($classes);
        $this->positionAssociatedClassesNextToEachOther($classes, $positions);
        $this->positionClassesStraightBelowAssociativeClasses($classes);
        $this->positionAllClassesNextToEachOther($classes, $positions);
    }

    private function positionClassesBelowAssociativeClasses($classes) {
        do {
            $modified = false;

            foreach ($this->classDiagram->getAssociations() as $association) {
                $from = $classes[$association->getFrom()];
                $to = $classes[$association->getTo()];

                if ($from->top >= $to->top) {
                    $to->top = $from->top + 1;
                    $to->y = $from->y + $from->height + 50;

                    $modified = true;
                }
            }
        } while($modified);
    }

    private function positionAssociatedClassesNextToEachOther($classes, $positions) {
        foreach ($this->classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];

            if (isset($positions[$from->top])) {
                while (isset($positions[$from->top][$from->left])) {
                    $from->left += 1;
                }
            }

            $positions[$from->top][$from->left] = $from;
        }
    }

    private function positionClassesStraightBelowAssociativeClasses($classes) {
        foreach ($this->classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];
            $to = $classes[$association->getTo()];

            $to->left = $from->left;
        }
    }

    private function positionAllClassesNextToEachOther($classes, $positions) {
        foreach ($classes as $class) {
            while (isset($positions[$class->top][$class->left]) &&
                $positions[$class->top][$class->left] !== $class) {
                $class->left += 1;
            }

            $positions[$class->top][$class->left] = $class;
        }
    }
}
