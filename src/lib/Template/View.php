<?php

namespace Template;

require_once('PartialView.php');

/**
 * Matches variable insertion points.
 *
 * A variable insertion point starts with {{ and ends with }},
 * between them is a variable name that starts with a lower case
 * and may contains lower case, upper case and numbers. It may be
 * surrounded by a single space.
 */
const VARIABLE_REGEX = '{{{ ?([a-z][a-zA-Z0-9]*?) ?}}}';

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

/**
 * This is a base class for views.
 *
 * @package Template
 */
abstract class View {

    /**
     * @var ViewSettings
     */
    protected $settings;

    /**
     * @var View
     */
    private $parent;

    /**
     * @var string Path to the template relative to the templates folder.
     */
    protected $template;
    /**
     * @var mixed[] An assoc array with name => variable of all variables
     *              that should be inserted in the output.
     */
    protected $variables = [];
    /**
     * @var View[] An assoc array with name => View of all children views
     *             that should be inserted in the output.
     */
    protected $views = [];

    public function __construct(ViewSettings $settings, View $parent = null) {
        $this->settings = $settings;
        $this->parent = $parent;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getVariable($name) {
        if (isset($this->variables[$name])) {
            return $this->variables[$name];
        } else if ($this->parent !== null) {
            return $this->parent->getVariable($name);
        }

        return null;
    }

    public function setVariable($name, $value) {
        $this->variables[$name] = $value;
    }

    public function getView($name) {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        } else if ($this->parent !== null) {
            return $this->parent->getView($name);
        }

        throw new \Exception("View '$name' not found");
    }

    public function setView($name, View $view) {
        $this->views[$name] = $view;
    }

    /**
     * Called on render before the template is actually rendered
     */
    public function onRender() {}

    /**
     * @returns string Rendered HTML.
     * @throws \Exception If the template file doesn't exist.
     */
    public function render() {
        $this->onRender();

        if (!is_file($this->settings->templatePath.$this->template)) {
            throw new \Exception("Template file '$this->template' don't exists");
        }
        $template = file_get_contents($this->settings->templatePath.$this->template);
        $partial = new PartialView($this);

        return $partial->render($template);
    }
}
