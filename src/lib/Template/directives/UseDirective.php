<?php

namespace Template\directives;

require_once('Directive.php');

use Template\PartialView;

/**
 * Creates a new scope with a new variable.
 *
 * Example:
 *  {? use {{ user.name }} as username:
 *      Hello, {{ username }}!
 *  ?}
 *
 * @package Template\directives
 */
class UseDirective extends BlockDirective {

    function render(PartialView $view, array $arguments, $body) {
        if (count($arguments) !== 3) {
            throw new \InvalidArgumentException('Exactly three arguments must be specified');
        } elseif ($arguments[1] !== 'as') {
            throw new \InvalidArgumentException('The second argument must be as');
        }


        $partial = new PartialView($view);
        $partial->setVariable($arguments[2], $view->getVariable($arguments[0]));

        return $partial->renderPartial($body);
    }
}
