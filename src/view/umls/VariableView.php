<?php

namespace view\umls;

use model\entities\umls\Variable;
use Template\View;

class VariableView extends View {
    protected $template = 'entities/variable';
    private $variable;
    public $height = 25;

    public function setVariableObject(Variable $variable) {
        $this->variable = $variable;
        $this->variables = [
            'name' => $variable->getName(),
            'type' => $variable->getType(),
        ];
    }
}
