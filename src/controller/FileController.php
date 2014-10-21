<?php

namespace controller;

use model\entities\umls\ClassDiagram;
use view\FileView;

class FileController {
    /**
     * @var FileView
     */
    private $fileView;

    public function __construct(FileView $view) {
        $this->fileView = $view;
    }

    public function render() {
        $classDiagram = new ClassDiagram($this->fileView->getUmls());
        $this->fileView->setDiagram($classDiagram);
        $this->fileView->setMimeType();

        return $this->fileView->render();
    }
}
