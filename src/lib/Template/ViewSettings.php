<?php

namespace Template;

require_once('directives/ExpressionDirective.php');
require_once('directives/ForDirective.php');
require_once('directives/FormatDirective.php');
require_once('directives/IfDirective.php');
require_once('directives/InjectViewDirective.php');
require_once('directives/InputDirective.php');
require_once('directives/JoinDirective.php');
require_once('directives/SetDirective.php');
require_once('directives/UseDirective.php');
require_once('directives/ViewDirective.php');

class ViewSettings {
    /**
     * @var directives\BlockDirective[] An assoc array with name => Directive of
     *                                  all registered block directives.
     */
    public $blockDirectives;
    /**
     * @var directives\InlineDirective[] An assoc array with name => Directive of
     *                                   all registered inline directives.
     */
    public $inlineDirectives;

    public $templatePath = 'templates/';

    public function __construct(directives\ExpressionDirective $expression,
                                directives\ForDirective $for,
                                directives\FormatDirective $format,
                                directives\IfDirective $if,
                                directives\InjectViewDirective $injectView,
                                directives\InputDirective $input,
                                directives\JoinDirective $join,
                                directives\SetDirective $set,
                                directives\UseDirective $use,
                                directives\ViewDirective $view) {
        $this->blockDirectives = [
            'for' => $for,
            'if' => $if,
            'use' => $use,
        ];

        $this->inlineDirectives = [
            'expression' => $expression,
            'format' => $format,
            'injectView' => $injectView,
            'input' => $input,
            'join' => $join,
            'set' => $set,
            'view' => $view,
        ];
    }
}
