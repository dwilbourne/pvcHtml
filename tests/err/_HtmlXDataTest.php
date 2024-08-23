<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\err;

use pvc\err\XDataTestMaster;
use pvc\html\err\_HtmlXData;

/**
 * Class _HtmlXDataTest
 */
class _HtmlXDataTest extends XDataTestMaster
{
    /**
     * testValidatorXData
     * @throws \ReflectionException
     * @covers \pvc\html\err\_HtmlXData::getLocalXCodes
     * @covers \pvc\html\err\_HtmlXData::getXMessageTemplates
     * @covers \pvc\html\err\InnerTextNotAllowedException
     * @covers \pvc\html\err\InvalidAreaShapeException
     * @covers \pvc\html\err\InvalidAttributeEventNameException
     * @covers \pvc\html\err\InvalidAttributeEventNameException
     * @covers \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\err\InvalidSubTagException
     * @covers \pvc\html\err\InvalidTagException
     * @covers \pvc\html\err\UnsetAttributeNameException
     */
    public function testValidatorXData(): void
    {
        $xData = new _HtmlXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}