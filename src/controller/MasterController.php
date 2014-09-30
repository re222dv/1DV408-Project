<?php

namespace controller;

use view\services\Router;

class MasterController {
    /**
     * @var Router
     */
    private $router;

    public function __construct(Router $router) {
        $this->router = $router;
    }

    public function render() {
        var_dump($this->router->getFilename());
    }
}
