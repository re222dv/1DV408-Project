<?php

namespace model\diagrams;

require_once('src/model/diagrams/ClassDiagram.php');

class ClassDiagramTest extends \PHPUnit_Framework_TestCase {
    public function testThatItCanParseADiagram() {
        $diagram = new ClassDiagram('
            [Post||post(text)]
            [Member|/name:string;email|login( username : string, password ):bool;logout()]
            [Member]-Comments-[Comment]
            [Post]-[Comment]
        ');

        $this->assertCount(3, $diagram->getClasses());
        $this->assertCount(2, $diagram->getAssociations());
    }

    public function testThatItCanExtendClasses() {
        $diagram = new ClassDiagram('
            [Post||post(text)]
            [Post|author]
        ');

        $this->assertCount(1, $diagram->getClasses());
        $this->assertCount(1, $diagram->getClasses()[0]->getAttributes());
        $this->assertCount(1, $diagram->getClasses()[0]->getMethods());
        $this->assertEmpty($diagram->getAssociations());
    }
}
