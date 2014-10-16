<?php

namespace view\umls;

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
        $this->variables['fromX'] = $this->from->x * ($this->from->width + 20) + $this->from->width / 2;
        $this->variables['fromY'] = $this->from->y + $this->from->height;
        $this->variables['toX'] = $this->to->x * ($this->to->width + 20) + $this->to->width / 2;
        $this->variables['toY'] = $this->to->y;
    }
}
