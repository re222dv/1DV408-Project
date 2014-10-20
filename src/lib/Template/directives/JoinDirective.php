<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

/**
 * Joins an array into a String with glue.
 *
 * Example:
 *   {% join arguments ", " %}
 *
 * @package Template\directives
 */
class JoinDirective extends InlineDirective {

    function render(View $view, array $arguments) {
        if (count($arguments) !== 2) {
            throw new \InvalidArgumentException('Exactly two arguments must be specified');
        }

        $glue = $arguments[1];
        $pieces = $arguments[0];

        if (is_string($pieces)) {
            $pieces = $view->getVariable($pieces);
        }

        $renderedPieces = [];

        foreach ($pieces as $piece) {
            if ($piece instanceof View) {
                $renderedPieces[] = $piece->render();
            } else {
                $renderedPieces[] = $piece;
            }
        }

        return join($glue, $renderedPieces);
    }
}
