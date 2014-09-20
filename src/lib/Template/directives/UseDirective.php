<?php

namespace Template\directives;

require_once('Directive.php');

use Template\PartialView;
use Template\View;

class UseDirective extends BlockDirective {

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

    /**
     * @param View $view       The View this directive is rendered in.
     * @param array $arguments All arguments specified in the template.
     * @param string $body     The body of this template.
     * @throws \InvalidArgumentException If more or less than one argument specified.
     * @return string Return a rendered version of this directive.
     */
    function render(View $view, array $arguments, $body) {
        if (count($arguments) !== 3) {
            throw new \InvalidArgumentException('Exactly three arguments must be specified');
        } elseif ($arguments[1] !== 'as') {
            throw new \InvalidArgumentException('The second argument must be as');
        }
        $this->view = $view;


        $partial = new PartialView($view);
        $partial->setVariable($arguments[2], $this->getValue($arguments[0]));

        return $partial->render($body);
    }
}
