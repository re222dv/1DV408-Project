<?php

namespace Autoload;

function autoload($className) {
    $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $fileName = 'src'.DIRECTORY_SEPARATOR.$fileName.'.php';

    if (file_exists($fileName)) {
        require_once($fileName);
    }
}

spl_autoload_register('\Autoload\autoload');
