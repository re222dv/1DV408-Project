<?php

namespace view\entities;

use model\entities\Variable;
use Template\View;

class VariableView extends View {
    protected $template = 'entities/variable';
    private $variable;
    public $height = 25;

    public function setVariableObject(Variable $variable) {
        $this->variable = $variable;
        $this->variables = [
            'name' => $variable->getName(),
        ];
    }
}
