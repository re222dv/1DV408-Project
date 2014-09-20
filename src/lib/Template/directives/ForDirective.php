<?php

namespace Template\directives;

require_once('Directive.php');

use Template\PartialView;
use Template\View;

class ForDirective extends BlockDirective {

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
        } elseif ($arguments[1] !== 'in') {
            throw new \InvalidArgumentException('The second argument must be in');
        }

        $rendered = '';

        foreach ($view->getVariable($arguments[2]) as $value) {
            $partial = new PartialView($view);
            $partial->setVariable($arguments[0], $value);
            $rendered .= $partial->render($body);
        }

        return $rendered;
    }
}
