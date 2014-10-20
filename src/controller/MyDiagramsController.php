<?php

namespace controller;

use model\repositories\DiagramRepository;
use model\services\Auth;
use view\MyDiagramsView;

class MyDiagramsController {
    /**
     * @var Auth
     */
    private $auth;
    private $diagramRepository;
    /**
     * @var MyDiagramsView
     */
    private $myDiagramsView;

    public function __construct(Auth $auth, DiagramRepository $diagramRepository,
                                MyDiagramsView $myDiagramsView) {
        $this->auth = $auth;
        $this->diagramRepository = $diagramRepository;
        $this->myDiagramsView = $myDiagramsView;
    }

    public function render() {
        $diagrams = $this->diagramRepository->getByUser($this->auth->getUser());

        $idToDelete = $this->myDiagramsView->shouldDelete();
        if ($idToDelete != null) {
            foreach ($diagrams as $index => $diagram) {
                if ($diagram->getId() == $idToDelete) {
                    $this->diagramRepository->delete($diagram);
                    unset($diagrams[$index]);
                }
            }
        }

        $this->myDiagramsView->setDiagrams($diagrams);

        return $this->myDiagramsView;
    }
}
