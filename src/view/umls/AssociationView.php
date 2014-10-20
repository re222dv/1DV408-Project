<?php

namespace view\umls;

use model\entities\umls\Association;
use Template\View;

class AssociationView extends View {
    const TV_FROM_X = 'fromX';
    const TV_FROM_Y = 'fromY';
    const TV_TO_X = 'toX';
    const TV_TO_Y = 'toY';

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
        $this->variables[self::TV_FROM_X] = $this->from->x + $this->from->width / 2;
        $this->variables[self::TV_FROM_Y] = $this->from->y + $this->from->height;
        $this->variables[self::TV_TO_X] = $this->to->x + $this->to->width / 2;
        $this->variables[self::TV_TO_Y] = $this->to->y;
    }
}
