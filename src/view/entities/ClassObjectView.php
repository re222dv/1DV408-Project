<?php

namespace view\entities;

use model\entities\ClassObject;
use Template\View;

class ClassObjectView extends View {
    protected $template = 'entities/class.svg';
    private $classObject;

    public function setClass(ClassObject $classObject) {
        $this->classObject = $classObject;

        $this->variables = [
            'width'=> 200,
            'height'=> 50,
            'headHeight'=> 50,
            'name'=> $classObject->getName(),
            'attributes'=> [],
            'methods'=> [],
        ];
    }
}
