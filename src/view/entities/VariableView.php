<?php

namespace view\entities;

use model\entities\Variable;
use Template\View;

class VariableView extends View {
    protected $template = 'entities/variable.svg';
    private $variable;
    public $height = 20;

    public function setVariableObject(Variable $variable) {
        $this->variable = $variable;
        $this->variables = [
            'height' => $this->height,
            'name' => $variable->getName(),
        ];
    }
}
