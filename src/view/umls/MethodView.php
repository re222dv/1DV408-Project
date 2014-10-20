<?php

namespace view\umls;

use model\entities\umls\Method;
use Template\View;

class MethodView extends View {
    const TV_ARGUMENTS = 'arguments';
    const TV_NAME = 'name';
    const TV_RETURN_TYPE = 'returnType';

    protected $template = 'entities/method.svg';
    private $method;
    public $height = 25;

    public function setMethod(Method $method) {
        $this->method = $method;
        $this->variables = [
            self::TV_NAME => $method->getName(),
            self::TV_RETURN_TYPE => $method->getReturnType(),
            self::TV_ARGUMENTS => [],
        ];

        $arguments = [];

        foreach ($method->getArguments() as $argument) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($argument);
            $arguments[] = trim($view->render());
        }

        $arguments = join(', ', $arguments);
        $this->variables[self::TV_ARGUMENTS] = $arguments;
    }
}
