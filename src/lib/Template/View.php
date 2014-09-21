<?php

namespace Template;

require_once('PartialView.php');

/**
 * This is a base class for views.
 *
 * @package Template
 */
abstract class View {
    /**
     * Matches variables with or without a point and a attribute
     *
     * Examples:
     *   user
     *   user.name
     */
    const VARIABLE_REGEX = '/([a-z0-9]+)(?:\.([a-z0-9]+))?/i';

    /**
     * @var ViewSettings
     */
    protected $settings;

    /**
     * @var View Variables and View are inherited from the parent
     */
    private $parent;

    /**
     * @var string Path to the template relative to the templates folder.
     */
    protected $template;
    /**
     * @var mixed[] An assoc array with name => variable of all variables
     *              defined on this View.
     */
    protected $variables = [];

    public function __construct(ViewSettings $settings, View $parent = null) {
        $this->settings = $settings;
        $this->parent = $parent;
    }

    public function getSettings() {
        return $this->settings;
    }

    public function getVariable($variable) {
        if (is_numeric($variable)) {
            return $variable;
        }

        preg_match(self::VARIABLE_REGEX, $variable, $matches);
        $name = $matches[1];

        if (isset($this->variables[$name])) {
            if (count($matches) > 2) {
                $memberName = $matches[2];
                return $this->variables[$name]->$memberName;
            }
            return $this->variables[$name];
        } else if ($this->parent !== null) {
            return $this->parent->getVariable($variable);
        }

        return null;
    }

    public function setVariable($name, $value) {
        $this->variables[$name] = $value;
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
