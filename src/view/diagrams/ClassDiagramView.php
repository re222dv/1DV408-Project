<?php

namespace view\diagrams;

use model\diagrams\ClassDiagram;
use model\entities\Association;
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
        $this->positionAllClassesNextToEachOther($classes, $positions);
        $this->positionClassesStraightBelowAssociativeClasses($classes, $positions);
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

    private function positionClassesStraightBelowAssociativeClasses($classes, $positions) {
        do {
            $modified = false;

            $associations = $this->classDiagram->getAssociations();

            foreach ($associations as $association) {
                $from = $classes[$association->getFrom()];
                $to = $classes[$association->getTo()];

                if ($to->left < $from->left) {
                    if (isset($positions[$to->top][$from->left]) &&
                        $positions[$to->top][$from->left] !== $to) {
                        $this->moveClass($positions, $to->top, $from->left);
                    }
                    $to->left = $from->left;
                    $positions[$to->top][$to->left] = $to;

                    $modified = true;
                }
            }
        } while($modified);
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

    private function moveClass($positions, $top, $left) {
        $class = $positions[$top][$left];

        if (isset($positions[$top][$left + 1])) {
            $this->moveClass($positions, $top, $left + 1);
        }

        $class->left = $left + 1;
        $positions[$top][$left + 1] = $class;
    }
}
