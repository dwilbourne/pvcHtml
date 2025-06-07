<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\element;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\err\ErrorHandler;
use pvc\err\stock\ErrorException;
use pvc\html\content_model\ContentModel;
use pvc\html\element\ElementVoid;
use pvc\html\err\GetDataTypeException;
use pvc\html\err\InvalidAttributeException;
use pvc\html\factory\HtmlFactory;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;

class ElementVoidTest extends TestCase
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string
     */
    protected string $attr1;

    /**
     * @var string
     */
    protected string $attr2;

    /**
     * @var array<string>
     */
    protected array $allowedAttributes;

    /**
     * @var HtmlFactory|MockObject
     */
    protected HtmlFactory|MockObject $htmlFactory;

    protected ContentModel|MockObject $contentModel;

    /**
     * @var ElementVoid
     */
    protected ElementVoid $element;

    /**
     * setUp
     */
    public function setUp(): void
    {
        $this->name = 'col';
        $this->attr1 = 'attr1';
        $this->attr2 = 'attr2';
        $this->allowedAttributes = [$this->attr1, $this->attr2];
        $this->htmlFactory = $this->createMock(HtmlFactory::class);
        $this->contentModel = $this->createMock(ContentModel::class);
        $this->element = new ElementVoid(
            $this->name,
            $this->allowedAttributes,
            $this->htmlFactory,
            $this->contentModel);
    }

    /**
     * @return void
     * @covers \pvc\html\element\ElementVoid::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(ElementVoid::class, $this->element);
    }

    /**
     * testSetAttributeFailsWithUnknownAttributeName
     * @covers \pvc\html\element\ElementVoid::setAttribute()
     */
    public function testSetAttributeFailsWithUnknownAttributeName(): void
    {
        $name = 'foo';
        $value = 15;
        self::expectException(InvalidAttributeException::class);
        $this->element->setAttribute($name, $value);
    }

    /**
     * testSetAttributeCreatesNewAttribute
     * @covers \pvc\html\element\ElementVoid::setAttribute()
     * @covers \pvc\html\element\ElementVoid::isAllowedAttribute
     */
    public function testSetAttributeCreatesNewAttribute(): void
    {
        $value = 'bar';
        $attribute = $this->createMock(AttributeInterface::class);
        $this->htmlFactory->expects($this->once())->method('makeAttribute')->with($this->attr1)->willReturn($attribute);
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->element->setAttribute($this->attr1, $value);
    }

    /**
     * @param  ElementVoid  $element
     *
     * @return void
     * @covers \pvc\html\element\ElementVoid::setAttribute()
     */
    public function testSetAttributeGetsExistingAttribute(): void
    {
        $value = 'bar';
        $attribute = $this->createMock(AttributeInterface::class);
        /**
         * makes it once, sets it twice
         */
        $this->htmlFactory->expects($this->once())->method('makeAttribute')->with($this->attr1)->willReturn($attribute);
        $attribute->expects($this->exactly(2))->method('setValue');
        $this->element->setAttribute($this->attr1, $value);
        $this->element->setAttribute($this->attr1, $value);
    }

    /**
     * @return void
     * @throws InvalidAttributeException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @covers \pvc\html\element\ElementVoid::addCustomData
     */
    public function testAddCustomDataCreatesNewAttribute(): void
    {
        $valueType = null;
        $caseSensitive = null;
        $valTester = null;
        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $this->htmlFactory->expects($this->once())
            ->method('makeCustomData')
            ->with($this->name, $valueType, $caseSensitive, $valTester)
            ->willReturn($attribute);
        $this->element->addCustomData($this->name, $valueType, $caseSensitive, $valTester);
    }

    /**
     * @return void
     * @throws ErrorException
     * @covers \pvc\html\element\ElementVoid::addCustomData
     */
    public function testAddCustomDataFailsAddingTheSameAttributeNameTwice(): void
    {
        $valueType = null;
        $caseSensitive = null;
        $valTester = null;
        $attribute = $this->createMock(AttributeCustomDataInterface::class);
        $this->htmlFactory->expects($this->once())
            ->method('makeCustomData')
            ->with($this->name, $valueType, $caseSensitive, $valTester)
            ->willReturn($attribute);
        $this->element->addCustomData($this->name, $valueType, $caseSensitive, $valTester);
        $errorHandler = new ErrorHandler();
        set_error_handler([$errorHandler, 'handle']);
        try {
            $this->element->addCustomData($this->name, $valueType, $caseSensitive, $valTester);
        } catch (ErrorException $e) {
            self::assertEquals($e->getSeverity(), E_USER_WARNING);
            self::assertEquals($e->getMessage(), 'Attribute ' . $this->name . ' is already defined');
        }
    }

    /**
     * @return void
     * @throws GetDataTypeException
     * @covers \pvc\html\element\ElementVoid::addCustomData
     */
    public function testAddCustomDataFailsWhenValueTypeIsUnknown(): void
    {
        $name = 'data-foo';
        $valueType = 'bar';
        $caseSensitive = null;
        $valTester = null;
        self::expectException(GetDataTypeException::class);
        $this->element->addCustomData($name, $valueType, $caseSensitive, $valTester);
    }

    /**
     * @return void
     * @throws InvalidAttributeException
     * @covers \pvc\html\element\ElementVoid::generateOpeningTag
     */
    public function testGenerateOpeningTag(): void
    {
        $value = 'bar';
        $attribute = $this->createMock(AttributeInterface::class);
        $this->htmlFactory->expects($this->once())->method('makeAttribute')->willReturn($attribute);
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->element->setAttribute($this->attr1, $value);
        $attribute->method('render')->willReturn($this->attr1 . '=\'' . $value . '\'');

        $expectedOutput = "<" . $this->name . " " . $this->attr1 . "='" . $value . "'>";
        self::assertEquals($expectedOutput, $this->element->generateOpeningTag());
    }

    /**
     * @return void
     * @throws InvalidAttributeException
     * @covers \pvc\html\element\ElementVoid::removeAttribute
     * @covers \pvc\html\element\ElementVoid::generateOpeningTag
     */
    public function testRemoveAttribute(): void
    {
        $value = 'bar';
        $attribute = $this->createMock(AttributeInterface::class);
        $this->htmlFactory->expects($this->once())->method('makeAttribute')->willReturn($attribute);
        $attribute->expects($this->once())->method('setValue')->with($value);
        $this->element->setAttribute($this->attr1, $value);
        $this->element->removeAttribute($this->attr1);

        $expectedOutput = "<" . $this->name . ">";
        self::assertEquals($expectedOutput, $this->element->generateOpeningTag());
    }
}
