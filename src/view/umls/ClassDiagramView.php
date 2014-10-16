<?php

namespace view\umls;

use model\entities\umls\ClassDiagram;
use Template\View;
use view\services\spring\Graph;
use view\services\spring\Node;

class ClassDiagramView extends View {
    use Graph;

    protected $template = 'diagrams/classDiagram.svg';
    /**
     * @var ClassDiagram
     */
    private $classDiagram;
    /**
     * @var ClassObjectView[]
     */
    private $classes;

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        $this->setVariable('classes', []);
        $this->setVariable('associations', []);

        $this->classes = [];

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);
            $this->variables['classes'][] = $view;
            $this->classes[$class->getName()] = $view;
        }

        $this->positionClasses($this->classes);

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $this->classes[$association->getFrom()];
            $to = $this->classes[$association->getTo()];

            $view = new AssociationView($this->settings);
            $view->setAssociation($association, $from, $to);
            $this->variables['associations'][] = $view;
        }

        $this->applyForce();
    }

    /**
     * @return Node[] All nodes in the graph with name as keys
     */
    public function getNodes() {
        foreach ($this->classDiagram->getAssociations() as $association) {
            $from = $this->classes[$association->getFrom()];
            $to = $this->classes[$association->getTo()];
            $from->addFriend($to);
            $to->addFriend($from);
        }

        return $this->classes;
    }

    private function positionClasses($classes) {
        $positions = [];

        $this->positionClassesBelowAssociativeClasses($classes);
        $this->positionAssociatedClassesNextToEachOther($classes, $positions);
        $this->positionClassesStraightBelowAssociativeClasses($classes);
        $this->positionAllClassesNextToEachOther($classes, $positions);
    }

    /**
     * @param ClassObjectView[] $classes
     */
    private function positionClassesBelowAssociativeClasses($classes) {
        do {
            $modified = false;

            foreach ($this->classDiagram->getAssociations() as $association) {
                $from = $classes[$association->getFrom()];
                $to = $classes[$association->getTo()];

                if ($from->y >= $to->y) {
                    $to->y = $from->y + 200;

                    $modified = true;
                }
            }
        } while($modified);
    }

    /**
     * @param ClassObjectView[] $classes
     * @param array $positions
     */
    private function positionAssociatedClassesNextToEachOther($classes, $positions) {
        foreach ($this->classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];

            if (isset($positions[$from->y])) {
                while (isset($positions[$from->y][$from->x])) {
                    $from->x += 200;
                }
            }

            $positions[$from->y][$from->x] = $from;
        }
    }

    /**
     * @param ClassObjectView[] $classes
     */
    private function positionClassesStraightBelowAssociativeClasses($classes) {
        foreach ($this->classDiagram->getAssociations() as $association) {
            $from = $classes[$association->getFrom()];
            $to = $classes[$association->getTo()];

            $to->x = $from->x;
        }
    }

    /**
     * @param ClassObjectView[] $classes
     * @param array $positions
     */
    private function positionAllClassesNextToEachOther($classes, $positions) {
        foreach ($classes as $class) {
            while (isset($positions[$class->y][$class->x]) &&
                $positions[$class->y][$class->x] !== $class) {
                $class->x += 200;
            }

            $positions[$class->y][$class->x] = $class;
        }
    }
}
