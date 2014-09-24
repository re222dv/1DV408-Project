<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

class SetDirective extends InlineDirective {

    /**
     * @param View $view       The View this directive is rendered in.
     * @param array $arguments All arguments specified in the template.
     * @throws \InvalidArgumentException If more or less than one argument specified.
     * @return string Return a rendered version of this directive.
     */
    function render(View $view, array $arguments) {
        if (count($arguments) !== 2) {
            throw new \InvalidArgumentException('One variable name and expression name must be specified');
        }
        $name = $arguments[0];
        $value = $arguments[1];

        $definedIn = null;

        for ($current = $view;
             $definedIn === null and $current !== null;
             $current = $current->getParent()) {
            if ($current->isDefined($name)) {
                $definedIn = $current;
            }
        }

        if ($definedIn !== null) {
            $definedIn->setVariable($name, $value);
        } else {
            $view->setVariable($name, $value);
        }
    }
}
