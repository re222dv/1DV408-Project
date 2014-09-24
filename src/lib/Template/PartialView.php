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
    const INLINE_DIRECTIVE_REGEX = '({% ?([a-z][a-zA-Z0-9]*)( (?:(?R)|(?:.??(?!%})))+) ?%})';

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
    const BLOCK_DIRECTIVE_REGEX = '(({\? ?([a-z][a-zA-Z0-9]*)((?: ?[^ :]*)+) ?:)((?:(?!(?1)|(\?})).|(?R))*)\?})';

    public function __construct(View $parent = null) {
        parent::__construct($parent->getSettings(), $parent);
    }

    /**
     * @param string $string
     * @return string[]
     */
    private function extractArguments($string) {
        $template = $string;
        while ($string != null) {
            $string = $this->renderInlineDirective($string);
            if ($string !== null) {
                $template = $string;
            }
        }
        $arguments = [];
        foreach (preg_split('/ +/', trim($template)) as $argument) {
            $arguments[] = $argument;
        }

        return $arguments;
    }

    private function renderBlockDirective($template) {
        $matched = preg_match(self::BLOCK_DIRECTIVE_REGEX.'s', $template, $match);

        if ($matched) {
            $name = $match[2];
            $arguments = $this->extractArguments($match[3]);
            $body = $match[4];

            $partial = new PartialView($this);

            $rendered = $partial->render($this->settings->blockDirectives[$name]->render($partial, $arguments, $body));

            $template = preg_replace('/'.preg_quote($match[0], '/').'/', $rendered, $template, 1);

            return $template;
        }

        return null;
    }

    private function renderInlineDirective($template) {
        $matched = preg_match(self::INLINE_DIRECTIVE_REGEX, $template, $match);

        if ($matched) {
            $name = $match[1];
            $arguments = $this->extractArguments($match[2]);

            $rendered = $this->settings->inlineDirectives[$name]->render($this, $arguments);

            $template = preg_replace('/'.preg_quote($match[0], '/').'/', $rendered, $template, 1);

            return $template;
        }

        return null;
    }

    /**
     * @param string $template Template code
     * @return string rendered HTML
     */
    public function render($template) {
        $template = str_replace(['{{', '}}'], ['{% expression ', '%}'], $template);

        preg_match_all('/'.self::BLOCK_DIRECTIVE_REGEX.'|'.self::INLINE_DIRECTIVE_REGEX.'/s', $template, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $rendered = $this->renderBlockDirective($match[0]);
            if ($rendered === null) {
                $rendered = $this->renderInlineDirective($match[0]);
            }
            $template = preg_replace('/'.preg_quote($match[0], '/').'/', $rendered, $template, 1);
        }


        return $template;
    }
}
