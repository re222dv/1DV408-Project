<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

/**
 * Formats a string using a format string with placeholders specified by { and }.
 *
 * Example:
 *   {% format "Hello, {name}" {{ username }} %}
 *
 * @package Template\directives
 */
class FormatDirective extends InlineDirective {
    const REPLACEMENTS = '/\{\w+\}/';

    function render(View $view, array $arguments) {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException('One variable name and expression name must be specified');
        }
        $format = $arguments[0];
        $replacements = array_splice($arguments, 1);

        preg_match_all(self::REPLACEMENTS, $format, $matches, PREG_SET_ORDER);
        for ($i = 0; $i < count($matches); $i += 1) {
            $format = str_replace($matches[$i][0], $replacements[$i], $format);
        }

        return $format;
    }
}
