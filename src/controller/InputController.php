<?php

namespace controller;

use model\entities\umls\ClassDiagram;
use view\InputView;

class InputController {
    /**
     * @var InputView
     */
    private $inputView;

    public function __construct(InputView $inputView) {
        $this->inputView = $inputView;
    }

    /**
     * @return InputView
     */
    public function render() {
        if ($this->inputView->shouldRender()) {
            $umls = $this->inputView->getUmls();
            $this->inputView->setDiagram(new ClassDiagram($umls));
        }

        return $this->inputView;
    }
}
