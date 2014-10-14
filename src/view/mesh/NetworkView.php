<?php

namespace view\mesh;

use model\entities\ClassObject;
use model\mesh\Network;
use model\mesh\Node;
use Template\View;
use view\entities\ClassObjectView;

class NetworkView extends View {
    protected $template = 'mesh/network.svg';

    public function setNetwork(Network $node) {
        $trees = [];
        $width = 0;

        foreach ($node->getTopNodes() as $node) {
            $this->viewify($node);

            $tree = new PartialTreeView($this->settings);
            $tree->setTopNode($node);

            $trees[] = $tree;
            $tree->setPosition($width, 0);
            $width += $tree->getWidth() + PartialTreeView::PADDING;
        }

        $this->variables['trees'] = $trees;
    }

    private function viewify(Node $node) {
        if ($node->data instanceof ClassObject) {
            $class = $node->data;
            $node->data = new ClassObjectView($this->settings);
            $node->data->setClass($class);

            foreach ($node->getChildren() as $child) {
                $this->viewify($child->child);
            }
        }
    }
}
