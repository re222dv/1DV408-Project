<?php

namespace Template;

class PartialView extends View {
    /**
     * Matches string literals
     *
     * A string literal starts and ends with double quotes (") and double
     * quotes inside of it may be escaped by a backslash (\)
     *
     * Example:
     *  "Today's quote: \"Clarity is better than cleverness.\""
     */
    const STRING_LITERAL_REGEX = '("((?:[^"])+)")';

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
        parent::__construct($parent->getSettings());
        $this->setParent($parent);
    }

    /**
     * Splits the string on a regex match and inserts the return value of
     * $matchCallback in between the parts. If $notMatchedCallback is
     * specified it's return value is inserted for every part that didn't
     * match.
     *
     * @param String $pattern
     * @param String $subject
     * @param callable $matchCallback Called with the match once for every match
     * @param callable $notMatchedCallback Optional, called for every part that isn't matched
     * @return array
     */
    private function splitOnRegex($pattern, $subject, $matchCallback, $notMatchedCallback = null) {
        $parts = [];

        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $start = mb_strpos($subject, $match[0]);
            if ($start > 0) {
                $before = mb_substr($subject, 0, $start);
                if ($notMatchedCallback) {
                    $parts = array_merge($parts, $notMatchedCallback($before));
                } else {
                    $parts[] = $before;
                }
                $subject = mb_substr($subject, $start);
            }
            $parts[] = $matchCallback($match);
            $subject = mb_substr($subject, mb_strlen($match[0]));
        }
        if ($notMatchedCallback) {
            $parts = array_merge($parts, $notMatchedCallback($subject));
        } else {
            $parts[] = $subject;
        }

        return $parts;
    }

    /**
     * @param string $string
     * @return string[]
     */
    private function extractArguments($string) {
        $inlineDirectivesParsed = $this->splitOnRegex(self::INLINE_DIRECTIVE_REGEX, $string,
            function($match) {
                return $this->runInlineDirective($match[0]);
            }
        );

        $arguments = [];

        foreach ($inlineDirectivesParsed as $argument) {
            if (!is_string($argument)) {
                $arguments[] = $argument;
            } else {
                $arguments = array_merge($arguments, $this->splitOnRegex(self::STRING_LITERAL_REGEX, $argument,
                    function($match) {
                        return $match[1];
                    },
                    function($notMatched) {
                        return preg_split('/\s+/', $notMatched, -1, PREG_SPLIT_NO_EMPTY);
                    }
                ));
            }
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

            $rendered = $partial->renderPartial(
                $this->settings->blockDirectives[$name]->render($partial, $arguments, $body)
            );

            $template = preg_replace('/'.preg_quote($match[0], '/').'/', $rendered, $template, 1);

            return $template;
        }

        return null;
    }

    private function runInlineDirective($template) {
        $matched = preg_match(self::INLINE_DIRECTIVE_REGEX, $template, $match);

        if ($matched) {
            $name = $match[1];
            $arguments = $this->extractArguments($match[2]);

            return $this->settings->inlineDirectives[$name]->render($this, $arguments);
        }

        return null;
    }

    private function renderInlineDirective($template) {
        $matched = preg_match(self::INLINE_DIRECTIVE_REGEX, $template, $match);

        if ($matched) {
            $rendered = $this->runInlineDirective($template);
            return preg_replace('/'.preg_quote($match[0], '/').'/', $rendered, $template, 1);
        }

        return null;
    }

    /**
     * @param string $template Template code
     * @return string rendered HTML
     */
    public function renderPartial($template) {
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
