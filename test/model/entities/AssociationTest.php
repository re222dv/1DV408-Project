<?php

namespace model\entities;

require_once('src/model/entities/Association.php');

class AssociationTest extends \PHPUnit_Framework_TestCase {

    public function testThatItParsesAssociationsInGarbage() {
        $association = new Association('dgfshdfgh[Member]-[Comment]gfdfgsgsdfg');

        $this->assertEmpty($association->getName());
        $this->assertEquals('Member', $association->getFrom());
        $this->assertEquals('Comment', $association->getTo());
    }

    public function testThatItParsesAssociationsWithName() {
        $association = new Association('[Member]-Posts-[Comment]');

        $this->assertEquals('Posts', $association->getName());
        $this->assertEquals('Member', $association->getFrom());
        $this->assertEquals('Comment', $association->getTo());
    }
}
