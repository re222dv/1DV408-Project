<?php

namespace view\umls;

use model\entities\umls\ClassObject;
use Template\View;
use view\services\spring\Node;

class ClassObjectView extends View {
    use Node;

    protected $template = 'entities/class.svg';
    /**
     * @var ClassObject
     */
    private $classObject;

    public function setClass(ClassObject $classObject) {
        $this->classObject = $classObject;
        $this->width = 200;

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

        $this->height = $this->getVariable('headHeight');

        foreach ($this->getVariable('attributes') as $attribute) {
            $this->height += $attribute->height;
        }

        foreach ($this->getVariable('methods') as $method) {
            $this->height += $method->height;
        }

        if ($this->getVariable('attributes') and $this->getVariable('methods')) {
            $this->height += 10;
        } elseif ($this->getVariable('attributes')) {
            $this->height += 5;
        } elseif ($this->getVariable('methods')) {
            $this->height += 15;
        }
    }

    public function onRender() {
        $this->variables['height'] = $this->height;
    }

    public function getName() {
        return $this->classObject->getName();
    }
}
