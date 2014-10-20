<?php

namespace Template\directives;

require_once('Directive.php');

use Template\PartialView;

/**
 * Renders the body if the expression evaluates to true.
 *
 * Example:
 *   {? if isLoggedIn:
 *      Hello, {{ user }}
 *   ?}
 *
 * @package Template\directives
 */
class IfDirective extends BlockDirective {

    function render(PartialView $view, array $arguments, $body) {
        if (count($arguments) !== 1) {
            throw new \InvalidArgumentException('Exactly one variable name must be specified');
        }

        if (is_string($arguments[0]) and substr($arguments[0], 0, 1) === '!') {
            $condition = !$view->getVariable(substr($arguments[0], 1));
        } else {
            $condition = $view->getVariable($arguments[0]);
        }

        if ($condition) {
            return $body;
        } else {
            return '';
        }
    }
}
