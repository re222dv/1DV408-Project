<?php

namespace controller;

use model\entities\Diagram;
use model\repositories\DiagramRepository;
use model\services\Auth;
use view\InputView;
use view\services\Router;

class InputController {
    private $auth;
    private $diagramRepository;
    /**
     * @var InputView
     */
    private $inputView;
    /**
     * @var Router
     */
    private $router;

    public function __construct(Auth $auth, Router $router, DiagramRepository $diagramRepository,
                                InputView $inputView) {
        $this->auth = $auth;
        $this->router = $router;
        $this->diagramRepository = $diagramRepository;
        $this->inputView = $inputView;
    }

    /**
     * @return InputView
     */
    public function render() {
        $id = $this->router->getDiagramId();

        if ($this->inputView->wantToSave()) {

            $diagram = new Diagram($id, $this->auth->getUser());
            $this->inputView->populateDiagram($diagram);

            if ($diagram->isValid()) {
                $this->diagramRepository->save($diagram);
                $this->router->redirectTo(str_replace('{id}', $diagram->getId(), Router::DIAGRAM_FORMAT));
            }

        } elseif (!$this->inputView->wantToRender() && $id != null) {
            $diagram = $this->diagramRepository->getById($id);
            $this->inputView->setName($diagram->getName());
            $this->inputView->setUmls($diagram->getUmls());
        }

        return $this->inputView;
    }
}
