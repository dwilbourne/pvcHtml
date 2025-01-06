<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\frmtr;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\html\frmtr\FrmtrHtml;
use pvc\interfaces\frmtr\msg\FrmtrMsgInterface;
use pvc\interfaces\html\element\ElementInterface;
use pvc\interfaces\html\element\ElementVoidInterface;
use pvc\interfaces\intl\LocaleInterface;
use pvc\interfaces\msg\MsgInterface;

class FrmtrHtmlTest extends TestCase
{
    protected FrmtrMsgInterface|MockObject $frmtrMsg;

    protected FrmtrHtml $frmtrHtml;

    public function setUp(): void
    {
        $this->frmtrMsg = $this->createMock(FrmtrMsgInterface::class);
        $this->frmtrHtml = new FrmtrHtml($this->frmtrMsg);
    }

    /**
     * testConstruct
     * @covers \pvc\html\frmtr\FrmtrHtml::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(FrmtrHtml::class, $this->frmtrHtml);
    }

    /**
     * testSetGetFrmtrMsg
     * @covers \pvc\html\frmtr\FrmtrHtml::setMsgFrmtr
     * @covers \pvc\html\frmtr\FrmtrHtml::getMsgFrmtr
     */
    public function testSetGetFrmtrMsg(): void
    {
        self::assertEquals($this->frmtrMsg, $this->frmtrHtml->getMsgFrmtr());
    }

    /**
     * testSetGetLocale
     * @covers \pvc\html\frmtr\FrmtrHtml::getLocale
     * @covers \pvc\html\frmtr\FrmtrHtml::setLocale
     */
    public function testSetGetLocale(): void
    {
        $locale = $this->createMock(LocaleInterface::class);
        $this->frmtrMsg->expects($this->once())->method('setLocale')->with($locale);
        $this->frmtrHtml->setLocale($locale);
        
        $this->frmtrMsg->expects($this->once())->method('getLocale')->willReturn($locale);
        self::assertEquals($locale, $this->frmtrHtml->getLocale());
    }

    /**
     * testFormatTagVoid
     * @covers \pvc\html\frmtr\FrmtrHtml::format
     */
    public function testFormatTagVoid(): void
    {
        $expectedResult = '<col>';
        $tag = $this->createMock(ElementVoidInterface::class);
        $tag->expects($this->once())->method('generateOpeningTag')->willReturn($expectedResult);
        self::assertEquals($expectedResult, $this->frmtrHtml->format($tag));
    }

    /**
     * testFormatWithNestedTagsAndMsgs
     * @covers \pvc\html\frmtr\FrmtrHtml::format
     * @covers \pvc\html\frmtr\FrmtrHtml::formatInnerHtmlRecurse
     */
    public function testFormatWithNestedTagsMsgsAndStrings(): void
    {
        $tagOpeningString = '<div>';
        $tagClosingString = '</div>';

        $innerTagOpeningString = '<p>';
        $innerTagClosingString = '</p>';

        $innerMsgText = 'text string';

        $innerMsg = $this->createMock(MsgInterface::class);
        $this->frmtrMsg->expects($this->once())->method('format')->with($innerMsg)->willReturn($innerMsgText);

        $literalText = 'this string is not to be translated';

        $innerTag = $this->createMock(ElementInterface::class);
        $innerTag->expects($this->once())->method('getChildren')->willReturn([$innerMsg, $literalText]);
        $innerTag->expects($this->once())->method('generateOpeningTag')->willReturn($innerTagOpeningString);
        $innerTag->expects($this->once())->method('generateClosingTag')->willReturn($innerTagClosingString);

        $tag = $this->createMock(ElementInterface::class);
        $tag->expects($this->once())->method('getChildren')->willReturn([$innerTag]);
        $tag->expects($this->once())->method('generateOpeningTag')->willReturn($tagOpeningString);
        $tag->expects($this->once())->method('generateClosingTag')->willReturn($tagClosingString);

        $expectedResult = $tagOpeningString . $innerTagOpeningString;
        $expectedResult .= $innerMsgText . $literalText;
        $expectedResult .= $innerTagClosingString . $tagClosingString;

        self::assertEquals($expectedResult, $this->frmtrHtml->format($tag));
    }
}
