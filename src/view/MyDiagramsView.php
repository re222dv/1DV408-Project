<?php

namespace view;

use model\entities\Diagram;
use Template\View;
use view\services\Router;


class MyDiagramsView extends View {
    const PV_DELETE = 'delete';
    const PV_ID = 'id';

    const TV_DIAGRAMS = 'diagrams';
    const TV_DIAGRAM_URL = 'diagramUrl';

    protected $template = 'myDiagrams.html';

    /**
     * @param Diagram[] $diagrams
     */
    public function setDiagrams($diagrams) {
        $this->variables[self::TV_DIAGRAMS] = [];

        foreach ($diagrams as $diagram) {
            $this->variables[self::TV_DIAGRAMS][] = new DiagramViewModel($diagram);
        }
    }

    public function onRender() {
        $this->variables[self::TV_DIAGRAM_URL] = Router::DIAGRAM_FORMAT;
    }

    public function shouldDelete() {
        if (isset($_POST[self::PV_DELETE])) {
            return $_POST[self::PV_ID];
        }

        return null;
    }
}

class DiagramViewModel {
    public $name;
    public $id;
    public $url;

    public function __construct(Diagram $diagram) {
        $this->name = $diagram->getName();
        $this->id = $diagram->getId();
        $this->url = Router::FILE.'?'.FileView::RV_UMLS.'='.rawurlencode($diagram->getUmls());
    }
}
