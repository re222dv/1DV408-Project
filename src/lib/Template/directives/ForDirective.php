<?php

namespace Template\directives;

require_once('Directive.php');

use Template\PartialView;

/**
 * Evaluates the body once for every item in the array, with the item in the scope.
 *
 * Example:
 *   {? for link in links:
 *      <a href="{{ link.url }}">{{ link.name }}</a>
 *   ?}
 *
 * @package Template\directives
 */
class ForDirective implements BlockDirective {

    function render(PartialView $view, array $arguments, $body) {
        if (count($arguments) !== 3) {
            throw new \InvalidArgumentException('Exactly three arguments must be specified');
        } elseif ($arguments[1] !== 'in') {
            throw new \InvalidArgumentException('The second argument must be in');
        }

        $rendered = '';

        foreach ($view->getVariable($arguments[2]) as $value) {
            $partial = new PartialView($view);
            $partial->setVariable($arguments[0], $value);
            $rendered .= $partial->renderPartial($body);
        }

        return $rendered;
    }
}
