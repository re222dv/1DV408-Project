<?php

namespace Template\directives;

require_once('Directive.php');

use Di\Injector;
use Template\View;

/**
 * Inject a View. The View will be instantiated using Di.
 *
 * Example:
 *   {% injectView \view\DateView %}
 *
 * @package Template\directives
 */
class InjectViewDirective extends InlineDirective {
    /**
     * @var Injector
     */
    private $injector;

    public function __construct(Injector $injector) {
        $this->injector = $injector;
    }

    function render(View $view, array $arguments) {
        if (count($arguments) !== 1) {
            throw new \InvalidArgumentException('Exactly one view class must be specified');
        }

        $injectedView = $this->injector->get($arguments[0]);

        return $injectedView->render();
    }
}
