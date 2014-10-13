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
        $this->positionClassesTogetherWithAssociativeClasses($classes, $positions);

        foreach ($positions as $top => $leftArray) {
            foreach ($leftArray as $left => $positionedClass) {
                $positions[$top][$left] = true;
            }
        }

        var_dump('Map');
        var_dump($positions);
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

    private function positionClassesTogetherWithAssociativeClasses($classes, &$positions) {
        do {
            $modified = false;

            foreach ($this->classDiagram->getAssociations() as $association) {
                $from = $classes[$association->getFrom()];
                $to = $classes[$association->getTo()];

                if (!$to->positioningDone) {
                    $this->clearClass($positions, $to);
                    $this->invalidateChildren($classes, $to->getName());
                    $to->left = $from->left;

                    if (isset($positions[$to->top][$to->left]) &&
                        $positions[$to->top][$to->left] !== $to) {
                        $this->moveClass($positions, $to->top, $to->left);
                    }
                    $positions[$to->top][$to->left] = $to;

                    $to->positioningDone = true;
                    $modified = true;
                }
            }
        } while($modified);
    }

    private function positionAllClassesNextToEachOther($classes, &$positions) {
        foreach ($classes as $class) {
            while (isset($positions[$class->top][$class->left]) &&
                   $positions[$class->top][$class->left] !== $class) {
                $class->left += 1;
            }

            $this->clearClass($positions, $class);
            $positions[$class->top][$class->left] = $class;
        }
    }

    private function clearClass(&$positions, $class) {
        foreach ($positions as $top => $leftArray) {
            foreach ($leftArray as $left => $positionedClass) {
                if ($positionedClass === $class) {
                    unset($positions[$top][$left]);
                }
            }
        }
    }

    private function moveClass(&$positions, $top, $left) {
        $class = $positions[$top][$left];

        $length = 0;

        $name = $positions[$top][$left]->getName();
        var_dump("moving $top $left $name");

        do {
            $length += 1;

            if (isset($positions[$top][$left + $length])) {
                $test = $left - $length;
                var_dump("testing $test with length $length");

                if ($left - $length >= 0 &&
                    !isset($positions[$top][$left - $length])) {
                    var_dump("it's empty!");
                    $length = -$length;
                }
            }

            $notEmpty = isset($positions[$top][$left + $length]);
        } while($notEmpty);

        $class->left = $left + $length;
        $positions[$top][$left + $length] = $class;
        unset($positions[$top][$left]);
    }

    private function getChildren($class) {
        $children = [];

        foreach ($this->classDiagram->getAssociations() as $association) {
            if ($association->getFrom() === $class) {
                $children[] = $association->getTo();
            }
        }

        return $children;
    }

    private function invalidateChildren($classes, $class) {
        foreach ($this->getChildren($class) as $child) {
            $classes[$child]->positioningDone = false;
        }
    }
}
