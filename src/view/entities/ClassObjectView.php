<?php

namespace view\entities;

use model\entities\ClassObject;
use Template\View;

class ClassObjectView extends View {
    protected $template = 'entities/class.svg';
    private $classObject;
    public $depth = 0;
    public $width = 200;

    public function setClass(ClassObject $classObject) {
        $this->classObject = $classObject;

        $this->variables = [
            'width'=> $this->width,
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

    public function onRender() {
        $height = $this->getVariable('headHeight');

        foreach ($this->getVariable('attributes') as $attribute) {
            $height += $attribute->height;
        }

        foreach ($this->getVariable('methods') as $method) {
            $height += $method->height;
        }

        if ($this->getVariable('attributes') and $this->getVariable('methods')) {
            $height += 10;
        } elseif ($this->getVariable('attributes')) {
            $height += 5;
        } elseif ($this->getVariable('methods')) {
            $height += 15;
        }

        $this->variables['height'] = $height;
    }
}
