<?php

namespace view;

use Template\View;
use view\services\Router;

class MasterView extends View {
    protected $template = 'master.html';

    public function setAuth(View $view) {
        $this->setVariable('auth', $view);
    }

    public function setMain(View $view) {
        $this->setVariable('main', $view);
    }

    public function onRender() {
        $this->variables['indexUrl'] = Router::INDEX;
    }
}
