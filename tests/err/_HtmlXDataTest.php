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
     * @covers \pvc\html\err\InvalidAttributeException
     * @covers \pvc\html\err\InvalidAttributeNameException
     * @covers \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\err\InvalidEventNameException
     * @covers \pvc\html\err\InvalidEventScriptException
     * @covers \pvc\html\err\MissingTagAttributesException
     */
    public function testValidatorXData(): void
    {
        $xData = new _HtmlXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}