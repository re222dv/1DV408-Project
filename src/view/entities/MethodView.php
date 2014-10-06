<?php

namespace view\entities;

use model\entities\Method;
use Template\View;

class MethodView extends View {
    protected $template = 'entities/method.svg';
    private $method;
    public $height = 25;

    public function setMethod(Method $method) {
        $this->method = $method;
        $this->variables = [
            'name'=> $method->getName(),
            'returnType'=> $method->getReturnType(),
            'height'=> $this->height,
            'arguments'=> [],
        ];

        foreach ($method->getArguments() as $argument) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($argument);
            $this->variables['arguments'][] = $view;
        }
    }
}
