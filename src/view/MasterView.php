<?php

namespace view;

use Template\View;

class MasterView extends View {
    protected $template = 'master.html';

    public function setMain(View $view) {
        $this->setVariable('main', $view);
    }
}
