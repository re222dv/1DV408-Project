<?php

namespace view;

use view\umls\ClassDiagramView;

class FileView extends ClassDiagramView {
    const RV_UMLS = 'umls';
    const TV_EXTERNAL = 'external';

    public function getUmls() {
        if (isset($_POST[self::RV_UMLS])) {
            return $_POST[self::RV_UMLS];
        } elseif (isset($_GET[self::RV_UMLS])) {
            return urldecode($_GET[self::RV_UMLS]);
        }

        return null;
    }

    public function onRender() {
        parent::onRender();

        $this->variables[self::TV_EXTERNAL] = true;
    }
}
