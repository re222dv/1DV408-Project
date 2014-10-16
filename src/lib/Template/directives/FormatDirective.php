<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

class FormatDirective extends InlineDirective {
    const REPLACEMENTS = '/\{\w+\}/';

    /**
     * @param View $view       The View this directive is rendered in.
     * @param array $arguments All arguments specified in the template.
     * @throws \InvalidArgumentException If more or less than one argument specified.
     * @return string Return a rendered version of this directive.
     */
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
