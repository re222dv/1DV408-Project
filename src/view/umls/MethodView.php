<?php

namespace view\umls;

use model\entities\umls\Method;
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

        $arguments = [];

        foreach ($method->getArguments() as $argument) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($argument);
            $arguments[] = trim($view->render());
        }

        $arguments = join(', ', $arguments);
        $this->variables['arguments'] = $arguments;
    }
}
