<?php

namespace view\umls;

use model\entities\umls\ClassDiagram;
use Template\View;
use view\graph\Graph;

class ClassDiagramView extends View {
    use Graph;

    const GV_CHECKBOARD = 'checkboard';

    const TV_ASSOCIATIONS = 'associations';
    const TV_CHECKBOARD = self::GV_CHECKBOARD;
    const TV_CLASSES = 'classes';
    const TV_HEIGHT = 'height';
    const TV_WIDTH = 'width';

    protected $template = 'diagrams/classDiagram.svg';
    /**
     * @var ClassDiagram
     */
    private $classDiagram;

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        $this->variables[self::TV_CLASSES] = [];
        $this->variables[self::TV_ASSOCIATIONS] = [];

        $nodes = [];

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);

            $nodes[$class->getName()] = $view;

            $this->variables[self::TV_CLASSES][] = $view;
        }

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $nodes[$association->getFrom()];
            $to = $nodes[$association->getTo()];

            $view = new AssociationView($this->settings);
            $view->setAssociation($from, $to);

            $from->linksOutgoing[$to->getName()] = $to;
            $to->linksIncoming[$from->getName()] = $from;

            $this->variables[self::TV_ASSOCIATIONS][] = $view;
        }

        $this->calculatePositions($nodes);
    }

    public function setMimeType() {
        header('Content-Type: image/svg+xml');
    }

    public function onRender() {
        $this->variables[self::TV_CHECKBOARD] = isset($_GET[self::GV_CHECKBOARD]);

        $height = 0;
        $width = 0;

        foreach ($this->variables[self::TV_CLASSES] as $class) {
            $height = max($height, $class->y + $class->height);
            $width = max($width, $class->x + $class->width);
        }

        // Set the width and height with an extra marginal to make sure we aren't clipped
        $this->variables[self::TV_HEIGHT] = $height + 1;
        $this->variables[self::TV_WIDTH] = $width + 1;
    }
}
