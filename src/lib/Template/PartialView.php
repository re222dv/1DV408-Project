<?php

namespace Template;

class PartialView extends View {

    /**
     * Matches variable insertion points.
     *
     * A variable insertion point starts with {{ and ends with }},
     * between them is a variable name that starts with a lower case
     * and may contains lower case, upper case and numbers. It may be
     * surrounded by a single space.
     */
    const VARIABLE_REGEX = '/{{ ?([^ ]+?) ?}}/';

    /**
     * Matches inline directives
     *
     * An inline directive does only have a header that contains the name
     * of the directive and an optional list of space separated arguments.
     *
     * Example:
     *  {% view content %}
     */
    const INLINE_DIRECTIVE_REGEX = '/{% ?([a-z][a-zA-Z0-9]*)((?: [^ ]*)*?) ?%}/';

    /**
     * Matches block directives
     *
     * A block directive a header and a body. The header contains the name
     * of the directive and an optional list of space separated arguments.
     * The header is ended with a colon (:) and after that the body starts
     * and ends at the closing tag (?}).
     *
     * Example:
     *  {? if loggedIn:
     *     Hello, {{ userName }}!
     *  ?}
     */
    const BLOCK_DIRECTIVE_REGEX = '/({\? ?([a-z][a-zA-Z0-9]*)((?: ?[^ :]*)+) ?:)((?:(?!(?1)|(\?})).|(?R))*)\?}/s';

    public function __construct(View $parent = null) {
        parent::__construct($parent->getSettings(), $parent);
    }

    /**
     * @param string $string
     * @return string[]
     */
    private function extractArguments($string) {
        $arguments = [];
        foreach (preg_split('/ +/', trim($string)) as $argument) {
            $arguments[] = $argument;
        }

        return $arguments;
    }

    /**
     * @param string $string A possibly unsafe string
     * @returns string A string that is safe inside HTML if double quotes are
     *                 used when placing in an attributes value.
     */
    private function htmlEscape($string) {
        $string = str_replace('<', '&lt;', $string);
        $string = str_replace('>', '&gt;', $string);
        $string = str_replace('"', '&quot;', $string);

        return $string;
    }

    /**
     * @param string $template
     * @return string a rendered version of this PartialView
     */
    public function render($template) {

        preg_match_all(self::BLOCK_DIRECTIVE_REGEX, $template, $blockDirectiveMatches, PREG_SET_ORDER);

        foreach ($blockDirectiveMatches as $match) {
            $name = $match[2];
            $arguments = $this->extractArguments($match[3]);
            $body = $match[4];

            $partial = new PartialView($this);

            $rendered = $partial->render($this->settings->blockDirectives[$name]->render($partial, $arguments, $body));

            $template = str_replace($match[0], $rendered, $template);
        }

        preg_match_all(self::INLINE_DIRECTIVE_REGEX, $template, $inlineDirectiveMatches, PREG_SET_ORDER);

        foreach ($inlineDirectiveMatches as $match) {
            $name = $match[1];
            $arguments = $this->extractArguments($match[2]);

            $rendered = $this->settings->inlineDirectives[$name]->render($this, $arguments);

            $template = str_replace($match[0], $rendered, $template);
        }

        preg_match_all(self::VARIABLE_REGEX, $template, $variableMatches, PREG_SET_ORDER);

        foreach ($variableMatches as $match) {
            $variable = $this->getVariable($match[1]);
            $variable = $this->htmlEscape($variable);
            $template = str_replace($match[0], $variable, $template);
        }

        return $template;
    }
}
