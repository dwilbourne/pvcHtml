<?php

namespace pvcTests\html\unit_tests\attribute;

use PHPUnit\Framework\TestCase;
use pvc\html\attribute\AttributeVoid;
use pvc\htmlbuilder\definitions\types\AttributeValueDataType;
use pvc\interfaces\validator\ValTesterInterface;

class AttributeVoidTest extends TestCase
{
    protected AttributeVoid $attribute;
    protected string $name = 'checked';
    protected AttributeValueDataType $dataType = AttributeValueDataType::Bool;
    protected bool $caseSensitiveYn = false;

    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $tester;

    public function setUp() : void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->tester->method('testValue')->willReturn(true);
        $this->attribute = new AttributeVoid(
            $this->name,
            $this->dataType,
            $this->caseSensitiveYn,
            $this->tester,
        );
    }

    /**
     * @return void
     * @covers \pvc\html\attribute\AttributeVoid::render
     */
    public function testRenderWithNoValueSet():void
    {
        $expectedResult = '';
        self::assertEquals($expectedResult, $this->attribute->render());
    }

    /**
     * @return void
     * @throws \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\attribute\AttributeVoid::render
     */
    public function testRenderWithValueSet():void
    {
        $this->attribute->setValue(true);
        $expectedResult = $this->name;
        self::assertEquals($expectedResult, $this->attribute->render());
    }

}
