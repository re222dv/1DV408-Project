<?php

namespace view\services;

class Router {
    const DIAGRAM_REGEX = '/^\/diagram\/(\d+)$/i';
    const FILENAME_REGEX = '/^\/(.+).svg$/is';

    const DIAGRAM_FORMAT = '/diagram/{id}';
    const FILENAME_FORMAT = '/{umls}.svg';
    const INDEX = '/';
    const MY_DIAGRAMS = '/diagrams';
    const REGISTER = '/register';

    public function getCurrentPath() {
        return (isset($_GET['path'])) ? $_GET['path'] : self::INDEX;
    }

    public function getDiagramId() {
        if (preg_match(self::DIAGRAM_REGEX, $this->getCurrentPath(), $match)) {
            return $match[1];
        }

        return null;
    }

    public function getFilename() {
        if (preg_match(self::FILENAME_REGEX, rawurldecode($this->getCurrentPath()), $match)) {
            return $match[1];
        }

        return null;
    }

    public function isDiagram() {
        return preg_match(self::DIAGRAM_REGEX, $this->getCurrentPath()) > 0;
    }

    public function isFile() {
        return preg_match(self::FILENAME_REGEX, $this->getCurrentPath()) > 0;
    }

    public function redirectTo($url) {
        header("location: $url");
    }
}
