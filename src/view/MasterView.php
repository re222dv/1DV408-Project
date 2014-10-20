<?php

namespace view;

use Template\View;
use view\services\Router;

class MasterView extends View {
    const TV_AUTH_VIEW = 'auth';
    const TV_MAIN_VIEW = 'main';
    const TV_INDEX_URL = 'indexUrl';

    protected $template = 'master.html';

    public function setAuth(View $view) {
        $this->variables[self::TV_AUTH_VIEW] = $view;
    }

    public function setMain(View $view) {
        $this->variables[self::TV_MAIN_VIEW] = $view;
    }

    public function onRender() {
        $this->variables[self::TV_INDEX_URL] = Router::INDEX;
    }
}
