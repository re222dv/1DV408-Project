<?php

namespace view\entities;

use model\entities\ClassObject;
use Template\View;

class ClassObjectView extends View {
    protected $template = 'entities/class.svg';
    private $classObject;
    public $width = 200;

    public function setClass(ClassObject $classObject) {
        $this->classObject = $classObject;

        $this->variables = [
            'width'=> $this->width,
            'height'=> 50,
            'headHeight'=> 50,
            'name'=> $classObject->getName(),
            'attributes'=> [],
            'methods'=> [],
        ];

        foreach ($classObject->getAttributes() as $attribute) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($attribute);
            $this->variables['attributes'][] = $view;
        }

        foreach ($classObject->getMethods() as $method) {
            $view = new MethodView($this->settings);
            $view->setMethod($method);
            $this->variables['methods'][] = $view;
        }
    }
}
