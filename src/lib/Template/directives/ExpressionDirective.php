<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

class ExpressionDirective extends InlineDirective {
    const PRIORITY_MATH = '/([a-z0-9.]+)\s*(\*|\/)\s*([a-z0-9.]+)/i';
    const MATH = '/([a-z0-9.]+)\s*(\+|-|%)\s*([a-z0-9.]+)/i';
    const BOOLEAN_EXPRESSION = '/([a-z0-9.]+)\s*((?:[!=]==?)|[<>])\s*([a-z0-9.]+)/i';

    private $expression;
    /**
     * @var View
     */
    private $view;

    private function getValue($expression) {
        if (is_numeric($expression)) {
            return $expression;
        }
        return $this->view->getVariable($expression);
    }

    private function calculate($pattern) {
        if (preg_match($pattern, $this->expression, $match) !== 0) {
            $first = $this->getValue($match[1]);
            $second = $this->getValue($match[3]);
            $result = 0;

            switch ($match[2]) {
                case '*':
                    $result = $first * $second;
                    break;
                case '/':
                    $result = $first / $second;
                    break;
                case '+':
                    $result = $first + $second;
                    break;
                case '-':
                    $result = $first - $second;
                    break;
                case '%':
                    $result = $first % $second;
                    break;
            }

            $this->expression = str_replace($match[0], $result, $this->expression);

            $this->calculate($pattern);
        }
    }

    private function check($pattern) {
        if (preg_match($pattern, $this->expression, $match) !== 0) {
            $first = $this->getValue($match[1]);
            $second = $this->getValue($match[3]);
            $result = 0;

            switch ($match[2]) {
                case '==':
                    $result = $first == $second;
                    break;
                case '===':
                    $result = $first === $second;
                    break;
                case '!=':
                    $result = $first != $second;
                    break;
                case '!==':
                    $result = $first !== $second;
                    break;
                case '<':
                    $result = $first < $second;
                    break;
                case '>':
                    $result = $first > $second;
                    break;
            }

            $result = $result ? 1 : 0;

            $this->expression = str_replace($match[0], $result, $this->expression);

            $this->check($pattern);
        }
    }

    /**
     * @param View $view       The View this directive is rendered in.
     * @param array $arguments All arguments specified in the template.
     * @throws \InvalidArgumentException If more or less than one argument specified.
     * @return string Return a rendered version of this directive.
     */
    function render(View $view, array $arguments) {
        $this->view = $view;
        $this->expression = join(' ', $arguments);

        $this->calculate(self::PRIORITY_MATH);
        $this->calculate(self::MATH);
        $this->check(self::BOOLEAN_EXPRESSION);

        return $this->expression;
    }
}
