<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

/**
 * Render a view.
 *
 * Example:
 *   {% view partial %}
 *
 * @package Template\directives
 */
class ViewDirective extends InlineDirective {

    function render(View $view, array $arguments) {
        if (count($arguments) !== 1) {
            throw new \InvalidArgumentException('Exactly one view must be specified');
        }

        return $view->getVariable($arguments[0])->render();
    }
}
