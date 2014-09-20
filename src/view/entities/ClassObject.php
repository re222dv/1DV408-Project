<?php

namespace view\entities;

use model\entities\ClassObject;
use Template\View;

class ClassObjectView extends View {
    protected $template = 'entities/class.svg';
    private $classObject;

    public function __construct(ClassObject $classObject) {
        $this->classObject = $classObject;
    }
}
