<?php

namespace view\entities;

use model\entities\umls\Association;
use Template\View;

class AssociationView extends View {
    protected $template = 'entities/association.svg';
    /**
     * @var ClassObjectView
     */
    private $from;
    /**
     * @var ClassObjectView
     */
    private $to;

    public function setAssociation(Association $association, ClassObjectView $from,
                                   ClassObjectView $to) {
        $this->from = $from;
        $this->to = $to;
    }

    public function onRender() {
        $this->variables['fromX'] = $this->from->left * ($this->from->width + 20) + $this->from->width / 2;
        $this->variables['fromY'] = $this->from->y + $this->from->getHeight();
        $this->variables['toX'] = $this->to->left * ($this->to->width + 20) + $this->to->width / 2;
        $this->variables['toY'] = $this->to->y;
    }
}
