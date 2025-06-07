<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\html\unit_tests\element;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\content_model\ContentModel;
use pvc\html\element\Element;
use pvc\html\err\ChildElementNotAllowedException;
use pvc\html\factory\HtmlFactory;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\msg\MsgInterface;

/**
 * @covers \pvc\html\element\Element
 */
class ElementTest extends TestCase
{
    /**
     * @var string
     */
    protected string $elementName;
    /**
     * @var Element
     */
    protected Element $element;


    protected HtmlFactory|MockObject $htmlFactory;

    protected ContentModel|MockObject $contentModel;

    public function setUp(): void
    {
        $this->elementName = 'foo';
        $allowedAttributes = [];
        $this->htmlFactory = $this->createMock(HtmlFactory::class);
        $this->contentModel = $this->createMock(ContentModel::class);

        $this->element = new Element(
            $this->elementName,
            $allowedAttributes,
            $this->htmlFactory,
            $this->contentModel);
    }

    /**
     * testSetGetChild
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setChild
     * @covers \pvc\html\element\Element::getNodes
     */
    public function testSetChildGetNodes(): void
    {
        $node = $this->createMock(ElementInterface::class);
        $this->element->setChild($node);
        $nodes = $this->element->getNodes();
        self::assertEquals($node, $nodes[0]);
    }

    /**
     * testSetChildMakesNewChildIfPassedAStringArgument
     * @throws ChildElementNotAllowedException
     * @covers \pvc\html\element\Element::setChild
     * @covers \pvc\html\element\Element::getNodes
     */
    public function testSetChildWithStringMakesNewElement(): void
    {
        $childName = 'bar';
        $child = $this->createMock(Element::class);
        $this->htmlFactory->expects($this->once())->method('makeElement')->with($childName)->willReturn($child);
        $this->element->setChild($childName);
        $nodes = $this->element->getNodes();
        self::assertEquals($child, $nodes[0]);
    }

    /**
     * testAddGetInnerHtml
     * @covers \pvc\html\element\Element::setInnerText
     */
    public function testAddGetInnerText(): void
    {
        $msg = $this->createMock(MsgInterface::class);
        $this->element->setInnerText($msg);
        $nodes = $this->element->getNodes();
        self::assertEquals($msg, $nodes[0]);

        $text = 'this is some text';
        $this->element->setInnerText($text);
        $nodes = $this->element->getNodes();
        self::assertEquals($text, $nodes[1]);
    }

    /**
     * testGenerateClosingTag
     * @covers \pvc\html\element\Element::generateClosingTag
     */
    public function testGenerateClosingTag(): void
    {
        $expectedResult = '</' . $this->elementName . '>';
        self::assertEquals($expectedResult, $this->element->generateClosingTag());
    }
}
