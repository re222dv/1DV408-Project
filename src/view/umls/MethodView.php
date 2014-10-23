<?php

namespace view\umls;

use model\entities\umls\Method;
use Template\View;

class MethodView extends View {
    /**
     * The width of a character in the specified monospace font at the specified font-size
     */
    const FONT_WIDTH = 8;
    const FONT_HEIGHT = 15;

    const TV_ARGUMENTS = 'arguments';
    const TV_FONT_HEIGHT = 'fontHeight';
    const TV_MULTI_LINE = 'multiLine';
    const TV_NAME = 'name';
    const TV_RETURN_TYPE = 'returnType';

    protected $template = 'entities/method.svg';
    /**
     * @var Method
     */
    private $method;
    public $height = 25;

    /**
     * @param Method $method
     * @param int $maxWidth Optional, maximum width in pixels. Will never break if negative
     */
    public function setMethod(Method $method, $maxWidth = -1) {
        $this->method = $method;
        $this->variables = [
            self::TV_NAME => $method->getName(),
            self::TV_RETURN_TYPE => $method->getReturnType(),
            self::TV_ARGUMENTS => [],
            self::TV_FONT_HEIGHT => self::FONT_HEIGHT,
            self::TV_MULTI_LINE => false,
        ];

        $arguments = [];

        foreach ($this->method->getArguments() as $argument) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($argument);
            $arguments[] = trim($view->render());
        }

        $joinedArguments = join(', ', $arguments);

        $characters = mb_strlen($method->getName()) +
                      mb_strlen($joinedArguments) +
                      mb_strlen($method->getReturnType());

        if ($characters * self::FONT_WIDTH < $maxWidth || $maxWidth < 0) {
            $this->variables[self::TV_ARGUMENTS] = $joinedArguments;
        } else {
            $this->height = count($arguments) * self::FONT_HEIGHT + 40;
            $this->variables[self::TV_MULTI_LINE] = true;
            $this->variables[self::TV_ARGUMENTS] = $arguments;
        }
    }
}
