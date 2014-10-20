<?php

namespace view\umls;

use model\entities\umls\Variable;
use Template\View;

class VariableView extends View {
    const TV_NAME = 'name';
    const TV_TYPE = 'type';

    protected $template = 'entities/variable';
    private $variable;
    public $height = 25;

    public function setVariableObject(Variable $variable) {
        $this->variable = $variable;
        $this->variables = [
            self::TV_NAME => $variable->getName(),
            self::TV_TYPE => $variable->getType(),
        ];
    }
}
