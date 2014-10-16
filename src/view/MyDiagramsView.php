<?php

namespace view;

use model\entities\Diagram;
use Template\View;


class MyDiagramsView extends View {
    protected $template = 'myDiagrams.html';

    /**
     * @param Diagram[] $diagrams
     */
    public function setDiagrams($diagrams) {
        $this->variables['diagrams'] = [];

        foreach ($diagrams as $diagram) {
            $this->variables['diagrams'][] = $diagram->getName();
        }
    }
}
