<?php

namespace view;

use model\entities\umls\ClassDiagram;
use Template\View;
use Template\ViewSettings;
use view\umls\ClassDiagramView;

const EXAMPLE = <<<EXAMPLE
// Class declarations
[Post|-text:string|getText():string;setText(text:string)]
[User|username;password|login(username, password)l]

// Associations
[Post]-author-[User]
EXAMPLE;


class InputView extends View {
    protected $template = 'input.html';

    public function __construct(ClassDiagramView $classDiagramView, ViewSettings $viewSettings) {
        parent::__construct($viewSettings);
        $this->setVariable('diagram', $classDiagramView);
    }

    public function getUmls() {
        if (isset($_GET['umls'])) {
            return urldecode($_GET['umls']);
        } else {
            return EXAMPLE;
        }
    }

    public function setDiagram(ClassDiagram $classDiagram) {
        $this->getVariable('diagram')->setDiagram($classDiagram);
    }

    public function onRender() {
        $this->setVariable('umls', $this->getUmls());
    }
}
