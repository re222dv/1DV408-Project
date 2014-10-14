<?php

namespace model\entities\umls;

require_once('src/model/entities/umls/ClassObject.php');

class ClassObjectTest extends \PHPUnit_Framework_TestCase {

    public function testThatItParsesClassesInGarbage() {
        $classObject = new ClassObject('dgfshdfgh[Simple]gfdfgsgsdfg');

        $this->assertEquals('Simple', $classObject->getName());
        $this->assertEmpty($classObject->getAttributes());
        $this->assertEmpty($classObject->getMethods());
    }

    public function testThatItParsesClassesWithAnAttribute() {
        $classObject = new ClassObject('[Attribute|attribute]');

        $this->assertEquals('Attribute', $classObject->getName());
        $this->assertCount(1, $classObject->getAttributes());
        $this->assertEquals('attribute', $classObject->getAttributes()[0]->getName());
        $this->assertNull($classObject->getAttributes()[0]->getType());
        $this->assertEmpty($classObject->getMethods());
    }

    public function testThatItParsesClassesWithMultipleAttributes() {
        $classObject = new ClassObject('[Attributes|firstname;lastname]');

        $this->assertEquals('Attributes', $classObject->getName());
        $this->assertCount(2, $classObject->getAttributes());
        $this->assertEquals('firstname', $classObject->getAttributes()[0]->getName());
        $this->assertNull($classObject->getAttributes()[0]->getType());
        $this->assertEquals('lastname', $classObject->getAttributes()[1]->getName());
        $this->assertNull($classObject->getAttributes()[1]->getType());
        $this->assertEmpty($classObject->getMethods());
    }

    public function testThatItParsesClassesWithTypedAttributes() {
        $classObject = new ClassObject('[Attributes|firstname:string;lastname:string]');

        $this->assertEquals('Attributes', $classObject->getName());
        $this->assertCount(2, $classObject->getAttributes());
        $this->assertEquals('firstname', $classObject->getAttributes()[0]->getName());
        $this->assertEquals('string', $classObject->getAttributes()[0]->getType());
        $this->assertEquals('lastname', $classObject->getAttributes()[1]->getName());
        $this->assertEquals('string', $classObject->getAttributes()[1]->getType());
        $this->assertEmpty($classObject->getMethods());
    }

    public function testThatItParsesClassesWithAMethod() {
        $classObject = new ClassObject('[Method||go()]');

        $this->assertEquals('Method', $classObject->getName());
        $this->assertEmpty($classObject->getAttributes());
        $this->assertCount(1, $classObject->getMethods());
        $this->assertEquals('go', $classObject->getMethods()[0]->getName());
        $this->assertEmpty($classObject->getMethods()[0]->getArguments());
        $this->assertNull($classObject->getMethods()[0]->getReturnType());
    }

    public function testThatItParsesClassesWithMultipleMethods() {
        $classObject = new ClassObject('[Methods||go();stop()]');

        $this->assertEquals('Methods', $classObject->getName());
        $this->assertEmpty($classObject->getAttributes());
        $this->assertCount(2, $classObject->getMethods());
        $this->assertEquals('go', $classObject->getMethods()[0]->getName());
        $this->assertEmpty($classObject->getMethods()[0]->getArguments());
        $this->assertNull($classObject->getMethods()[0]->getReturnType());
        $this->assertEquals('stop', $classObject->getMethods()[1]->getName());
        $this->assertEmpty($classObject->getMethods()[1]->getArguments());
        $this->assertNull($classObject->getMethods()[1]->getReturnType());
    }

    public function testThatItParsesClassesWithMethodsWithArguments() {
        $classObject = new ClassObject('[Methods||go(length:int, direction);stop(force:bool)]');

        $this->assertEquals('Methods', $classObject->getName());
        $this->assertEmpty($classObject->getAttributes());
        $this->assertCount(2, $classObject->getMethods());
        $this->assertEquals('go', $classObject->getMethods()[0]->getName());
        $this->assertCount(2, $classObject->getMethods()[0]->getArguments());
        $this->assertEquals('length', $classObject->getMethods()[0]->getArguments()[0]->getName());
        $this->assertEquals('int', $classObject->getMethods()[0]->getArguments()[0]->getType());
        $this->assertEquals('direction', $classObject->getMethods()[0]->getArguments()[1]->getName());
        $this->assertNull($classObject->getMethods()[0]->getArguments()[1]->getType());
        $this->assertNull($classObject->getMethods()[0]->getReturnType());
        $this->assertEquals('stop', $classObject->getMethods()[1]->getName());
        $this->assertCount(1, $classObject->getMethods()[1]->getArguments());
        $this->assertEquals('force', $classObject->getMethods()[1]->getArguments()[0]->getName());
        $this->assertEquals('bool', $classObject->getMethods()[1]->getArguments()[0]->getType());
        $this->assertNull($classObject->getMethods()[1]->getReturnType());
    }

    public function testThatItParsesClassesWithMethodsWithReturnType() {
        $classObject = new ClassObject('[Methods||go(length:int, direction):Position;stop():bool]');

        $this->assertEquals('Methods', $classObject->getName());
        $this->assertEmpty($classObject->getAttributes());
        $this->assertCount(2, $classObject->getMethods());
        $this->assertEquals('go', $classObject->getMethods()[0]->getName());
        $this->assertCount(2, $classObject->getMethods()[0]->getArguments());
        $this->assertEquals('length', $classObject->getMethods()[0]->getArguments()[0]->getName());
        $this->assertEquals('int', $classObject->getMethods()[0]->getArguments()[0]->getType());
        $this->assertEquals('direction', $classObject->getMethods()[0]->getArguments()[1]->getName());
        $this->assertNull($classObject->getMethods()[0]->getArguments()[1]->getType());
        $this->assertEquals('Position', $classObject->getMethods()[0]->getReturnType());
        $this->assertEquals('stop', $classObject->getMethods()[1]->getName());
        $this->assertEmpty($classObject->getMethods()[1]->getArguments());
        $this->assertEquals('bool', $classObject->getMethods()[1]->getReturnType());
    }
}
