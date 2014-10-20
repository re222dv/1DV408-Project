<?php

namespace Template\directives;

use Template\View;

require_once('Directive.php');

/**
 * Handles formfields sent with POST, values are set and read using template variables
 * and fields are autofilled with the previously posted value if not overridden.
 * Inputs must be registered before rendering, it's preferably done in the constructor.
 *
 * Examples:
 *   {% input username %}
 *   {% input rememberMe checkbox %}
 *
 * @package Template\directives
 */
class InputDirective extends InlineDirective {
    private $registeredInputs = [];

    function registerInput(View $view, $modelName) {
        $this->registeredInputs[] = $modelName;

        if (isset($_POST[$modelName])) {
            $view->setVariable($modelName, $_POST[$modelName]);
        }
    }

    function render(View $view, array $arguments) {
        $flags = [];

        if (count($arguments) === 2) {
            if ($arguments[1] !== 'checkbox') {
                throw new \InvalidArgumentException("Unsupported flag '$arguments[1]'");
            }

            $flags[] = $arguments[1];
        } elseif (count($arguments) !== 1) {
            throw new \InvalidArgumentException('Exactly one variable name must be specified,'
                .'with one optional flag');
        }
        $inputName = $arguments[0];

        if (!in_array($inputName, $this->registeredInputs)) {
            throw new \Exception("InputDirective $inputName have not been registered");
        }

        if (isset($_POST[$inputName])) {
            if (in_array('checkbox', $flags) && $_POST[$inputName] === 'on') {
                return 'name="'.$inputName.'" checked';
            }
            return 'name="'.$inputName.'" value="'.$view->getVariable($inputName).'"';
        }

        return 'name="'.$inputName.'"';
    }
}
