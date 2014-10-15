<?php

namespace view;

use Template\View;

class MasterView extends View {
    protected $template = 'master.html';

    public function setAuth(View $view) {
        $this->setVariable('auth', $view);
    }

    public function setMain(View $view) {
        $this->setVariable('main', $view);
    }
}
