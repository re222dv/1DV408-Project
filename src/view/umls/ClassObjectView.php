<?php

namespace view\umls;

use model\entities\umls\ClassObject;
use Template\View;
use view\point_graph\Node;

class ClassObjectView extends View {
    use Node;

    const TV_ATTRIBUTES = 'attributes';
    const TV_HEAD_HEIGHT = 'headHeight';
    const TV_HEIGHT = 'height';
    const TV_METHODS = 'methods';
    const TV_NAME = 'name';
    const TV_WIDTH = 'width';

    protected $template = 'entities/class.svg';
    /**
     * @var ClassObject
     */
    private $classObject;

    public function getName() {
        return $this->classObject->getName();
    }

    public function setClass(ClassObject $classObject) {
        $this->classObject = $classObject;
        $this->width = 200;

        $this->variables = [
            self::TV_WIDTH => $this->width,
            self::TV_HEAD_HEIGHT => 50,
            self::TV_NAME => $classObject->getName(),
            self::TV_ATTRIBUTES => [],
            self::TV_METHODS => [],
        ];

        foreach ($classObject->getAttributes() as $attribute) {
            $view = new VariableView($this->settings);
            $view->setVariableObject($attribute);
            $this->variables[self::TV_ATTRIBUTES][] = $view;
        }

        foreach ($classObject->getMethods() as $method) {
            $view = new MethodView($this->settings);
            $view->setMethod($method);
            $this->variables[self::TV_METHODS][] = $view;
        }

        $this->height = $this->getVariable(self::TV_HEAD_HEIGHT);

        foreach ($this->getVariable(self::TV_ATTRIBUTES) as $attribute) {
            $this->height += $attribute->height;
        }

        foreach ($this->getVariable(self::TV_METHODS) as $method) {
            $this->height += $method->height;
        }

        if ($this->getVariable(self::TV_ATTRIBUTES) and $this->getVariable(self::TV_METHODS)) {
            $this->height += 10;
        } elseif ($this->getVariable(self::TV_ATTRIBUTES)) {
            $this->height += 5;
        } elseif ($this->getVariable(self::TV_METHODS)) {
            $this->height += 15;
        }
    }

    public function onRender() {
        $this->variables[self::TV_HEIGHT] = $this->height;
    }
}
