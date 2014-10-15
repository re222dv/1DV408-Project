<?php

namespace view\services;

class Router {
    const FILENAME_REGEX = '/\/(.+).svg$/i';
    const INDEX = '/';
    const REGISTER = '/register';

    public function getCurrentPath() {
        return (isset($_GET['path'])) ? $_GET['path'] : self::INDEX;
    }

    public function getFilename() {
        if (preg_match(self::FILENAME_REGEX, $this->getCurrentPath(), $match)) {
            return $match[1];
        }

        return null;
    }

    public function isFile() {
        return preg_match(self::FILENAME_REGEX, $this->getCurrentPath()) > 0;
    }

    public function redirectTo($url) {
        header('location: '.$url);
    }
}
