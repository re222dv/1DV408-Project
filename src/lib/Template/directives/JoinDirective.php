<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

class JoinDirective extends InlineDirective {

    /**
     * @param View $view       The View this directive is rendered in.
     * @param array $arguments All arguments specified in the template.
     * @throws \InvalidArgumentException If more or less than one argument specified.
     * @return string Return a rendered version of this directive.
     */
    function render(View $view, array $arguments) {
        if (count($arguments) !== 2) {
            throw new \InvalidArgumentException('Exactly two arguments must be specified');
        }

        if (is_string($arguments[0])) {
            $arguments[0] = $view->getVariable($arguments[0]);
        }

        return join($arguments[1], $arguments[0]);
    }
}
