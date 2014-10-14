<?php

namespace view\mesh;

use model\diagrams\ClassDiagram;
use model\entities\Association;
use model\entities\ClassObject;
use model\mesh\Node;
use Template\View;
use view\entities\AssociationView;
use view\entities\ClassObjectView;

class PartialTreeView extends View {
    const PADDING = 30;
    protected $template = 'mesh/tree.svg';
    private $width;

    public function setTopNode(Node $node) {
        $subTrees = [];

        $childTop = $node->data->getHeight() + self::PADDING;
        $width = 0;

        foreach ($node->getChildren() as $childNode) {
            $subTree = new PartialTreeView($this->settings);
            $subTree->setTopNode($childNode->child);

            $subTrees[] = $subTree;
            $subTree->setPosition($width, $childTop);
            $width += $subTree->getWidth() + self::PADDING;
        }

        $this->width = max([$node->data->getWidth(), $width]);

        $this->variables['class'] = $node->data;
        $this->variables['nodeLeft'] = $width / 2 - $node->data->getWidth() / 2;
        $this->variables['subTrees'] = $subTrees;
    }

    public function getWidth() {
        return $this->width;
    }

    public function setPosition($left, $top) {
        $this->variables['left'] = $left;
        $this->variables['top'] = $top;
    }
}
