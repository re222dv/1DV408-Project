<?php

namespace view;

use model\entities\Diagram;
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

    const GV_RENDER = 'render';
    const PV_SAVE = 'save';
    const RV_NAME = 'name';
    const RV_UMLS = 'umls';

    const TV_DIAGRAM_VIEW = 'diagram';
    const TV_ERRORS = 'errors';
    const TV_LOGGED_IN = 'loggedIn';
    const TV_NAME = self::RV_NAME;
    const TV_UMLS = self::RV_UMLS;

    protected $template = 'input.html';

    public function __construct(Auth $auth, ClassDiagramView $classDiagramView,
                                ViewSettings $viewSettings) {
        parent::__construct($viewSettings);

        $this->variables = [
            self::TV_DIAGRAM_VIEW => $classDiagramView,
            self::TV_ERRORS => [],
            self::TV_LOGGED_IN => $auth->isLoggedIn(),
        ];
    }

    public function getName() {
        if (isset($_POST[self::RV_NAME])) {
            return $_POST[self::RV_NAME];
        } elseif (isset($_GET[self::RV_NAME])) {
            return urldecode($_GET[self::RV_NAME]);
        }

        return null;
    }

    public function getUmls() {
        if (isset($_POST[self::RV_UMLS])) {
            return $_POST[self::RV_UMLS];
        } elseif (isset($_GET[self::RV_UMLS])) {
            return urldecode($_GET[self::RV_UMLS]);
        }

        return self::EXAMPLE;
    }

    public function setName($name) {
        $this->variables[self::TV_NAME] = $name;
    }

    public function setUmls($umls) {
        $this->setVariable(self::TV_UMLS, $umls);
    }

    public function onRender() {
        if (!isset($this->variables[self::TV_UMLS])) {
            $this->variables[self::TV_UMLS] = $this->getUmls();
        }
        $this->variables[self::TV_DIAGRAM_VIEW]->setDiagram(
            new ClassDiagram($this->variables[self::TV_UMLS])
        );

        if (!isset($this->variables[self::TV_NAME])) {
            $this->setName($this->getName());
        }
    }

    public function wantToRender() {
        return isset($_GET[self::GV_RENDER]);
    }

    public function wantToSave() {
        return isset($_POST[self::PV_SAVE]);
    }

    public function populateDiagram(Diagram $diagram) {
        $diagram->setUmls($this->getUmls());
        try {
            $diagram->setName($this->getName());
        } catch (\InvalidArgumentException $e) {
            $length = $e->getMessage();
            switch ($e->getCode()) {
                case Diagram::TOO_SHORT:
                    $this->addError("The name is too short, a minimum of $length characters is required");
                    break;
                case Diagram::TOO_LONG:
                    $this->addError("The name is too long, a maximum of $length characters is required");
                    break;
            }
        }
    }

    /**
     * @param string $message
     */
    private function addError($message) {
        $this->variables[self::TV_ERRORS][] = $message;
    }
}
