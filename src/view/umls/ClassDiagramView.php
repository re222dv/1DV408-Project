<?php

namespace view\umls;

use model\entities\umls\ClassDiagram;
use Template\View;
use view\point_graph\Graph;
use view\point_graph\Node;

class ClassDiagramView extends View {
    use Graph;

    protected $template = 'diagrams/classDiagram.svg';
    /**
     * @var ClassDiagram
     */
    private $classDiagram;

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->classDiagram = $classDiagram;

        $this->setVariable('classes', []);
        $this->setVariable('associations', []);

        $nodes = [];

        foreach ($classDiagram->getClasses() as $class) {
            $view = new ClassObjectView($this->settings);
            $view->setClass($class);

            $nodes[$class->getName()] = $view;

            $this->variables['classes'][] = $view;
        }

        foreach ($classDiagram->getAssociations() as $association) {
            $from = $nodes[$association->getFrom()];
            $to = $nodes[$association->getTo()];

            $view = new AssociationView($this->settings);
            $view->setAssociation($association, $from, $to);

            $from->linksOutgoing[$to->getName()] = $to;
            $to->linksIncoming[$from->getName()] = $from;

            $this->variables['associations'][] = $view;
        }

        $this->calculatePositions($nodes);
    }

    public function setMimeType() {
        header('Content-Type: image/svg+xml');
    }

    public function onRender() {
        $this->variables['checkboard'] = isset($_GET['checkboard']);
    }
}
