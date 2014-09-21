<?php

namespace Template;

class PartialView extends View {

    /**
     * Matches inline directives
     *
     * An inline directive does only have a header that contains the name
     * of the directive and an optional list of space separated arguments.
     *
     * Example:
     *  {% view content %}
     */
    const INLINE_DIRECTIVE_REGEX = '/{% ?([a-z][a-zA-Z0-9]*)( (?:(?R)|(?:.??(?!%})))+) ?%}/';

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
        $string = $this->renderInlineDirectives($string);
        $arguments = [];
        foreach (preg_split('/ +/', trim($string)) as $argument) {
            $arguments[] = $argument;
        }

        return $arguments;
    }

    private function renderBlockDirectives($template) {
        preg_match_all(self::BLOCK_DIRECTIVE_REGEX, $template, $blockDirectiveMatches, PREG_SET_ORDER);

        foreach ($blockDirectiveMatches as $match) {
            $name = $match[2];
            $arguments = $this->extractArguments($match[3]);
            $body = $match[4];

            $partial = new PartialView($this);

            $rendered = $partial->render($this->settings->blockDirectives[$name]->render($partial, $arguments, $body));

            $template = str_replace($match[0], $rendered, $template);
        }

        return $template;
    }

    private function renderInlineDirectives($template) {
        preg_match_all(self::INLINE_DIRECTIVE_REGEX, $template, $inlineDirectiveMatches, PREG_SET_ORDER);

        foreach ($inlineDirectiveMatches as $match) {
            $name = $match[1];
            $arguments = $this->extractArguments($match[2]);

            $rendered = $this->settings->inlineDirectives[$name]->render($this, $arguments);

            $template = str_replace($match[0], $rendered, $template);
        }

        return $template;
    }

    /**
     * @param string $template Template code
     * @return string rendered HTML
     */
    public function render($template) {
        $template = str_replace(['{{', '}}'], ['{% expression ', '%}'], $template);

        $template = $this->renderBlockDirectives($template);
        $template = $this->renderInlineDirectives($template);

        return $template;
    }
}
