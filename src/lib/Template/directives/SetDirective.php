<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

/**
 * Sets a variable in the scope.
 *
 * Example:
 *   {% set count {{ count + 1 }} %}
 *
 * @package Template\directives
 */
class SetDirective extends InlineDirective {

    function render(View $view, array $arguments) {
        if (count($arguments) !== 2) {
            throw new \InvalidArgumentException('One variable name and expression name must be specified');
        }
        $name = $arguments[0];
        $value = $arguments[1];

        $definedIn = null;

        for ($current = $view;
             $definedIn === null && $current !== null;
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
