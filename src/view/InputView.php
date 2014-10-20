<?php

namespace view;

use model\entities\umls\ClassDiagram;
use model\services\Auth;
use Template\View;
use Template\ViewSettings;
use view\umls\ClassDiagramView;


class InputView extends View {
    const EXAMPLE = <<<EXAMPLE
// Class declarations
[Post|-text:string|getText():string;setText(text:string)]
[User|username;password|login(username, password)l]

// Associations
[Post]-author-[User]
EXAMPLE;

    protected $template = 'input.html';

    public function __construct(Auth $auth, ClassDiagramView $classDiagramView,
                                ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $this->setVariable('loggedIn', $auth->isLoggedIn());
        $this->setVariable('diagram', $classDiagramView);
    }

    public function getName() {
        if (isset($_POST['name'])) {
            return $_POST['name'];
        } elseif (isset($_GET['name'])) {
            return urldecode($_GET['name']);
        }

        return null;
    }

    public function getUmls() {
        if (isset($_POST['umls'])) {
            return $_POST['umls'];
        } elseif (isset($_GET['umls'])) {
            return urldecode($_GET['umls']);
        }

        return self::EXAMPLE;
    }

    public function setName($name) {
        $this->variables['name'] = $name;
    }

    public function setUmls($umls) {
        $this->setVariable('umls', $umls);
    }

    public function onRender() {
        if (!isset($this->variables['umls'])) {
            $this->variables['umls'] = $this->getUmls();
        }
        $this->getVariable('diagram')->setDiagram(new ClassDiagram($this->variables['umls']));

        if (!isset($this->variables['name'])) {
            $this->setName($this->getName());
        }
    }

    public function wantToRender() {
        return isset($_GET['render']);
    }

    public function wantToSave() {
        return isset($_POST['save']);
    }
}
