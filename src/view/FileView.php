<?php

namespace view;

use view\umls\ClassDiagramView;

class FileView extends ClassDiagramView {
    const RV_UMLS = 'umls';

    public function getUmls() {
        if (isset($_POST[self::RV_UMLS])) {
            return $_POST[self::RV_UMLS];
        } elseif (isset($_GET[self::RV_UMLS])) {
            return urldecode($_GET[self::RV_UMLS]);
        }

        return null;
    }
}
