<?php

namespace view\services;

class Router {
    const FILENAME = '/\/(.+).svg/i';

    private function getCurrentPath() {
        return (isset($_GET['path'])) ? $_GET['path'] : '/';
    }

    public function getFilename() {
        if (preg_match(self::FILENAME, $this->getCurrentPath(), $match)) {
            return $match[1];
        }

        return null;
    }
}
