<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\abstract\err;

use pvc\err\XDataTestMaster;
use pvc\html\abstract\err\_HtmlXData;
use ReflectionException;

/**
 * Class _HtmlXDataTest
 */
class _HtmlXDataTest extends XDataTestMaster
{
    /**
     * testValidatorXData
     * @throws ReflectionException
     * @covers \pvc\html\abstract\err\_HtmlXData::getLocalXCodes
     * @covers \pvc\html\abstract\err\_HtmlXData::getXMessageTemplates
     * @covers \pvc\html\abstract\err\AttributeNotAllowedException
     * @covers \pvc\html\abstract\err\InvalidAttributeNameException
     * @covers \pvc\html\abstract\err\InvalidAttributeValueException
     * @covers \pvc\html\abstract\err\InvalidCustomDataNameException
     * @covers \pvc\html\abstract\err\InvalidInnerTextException
     * @covers \pvc\html\abstract\err\InvalidSubTagException
     * @covers \pvc\html\abstract\err\UnsetAttributeNameException
     * @covers \pvc\html\abstract\err\UnsetTagNameException
     */
    public function testValidatorXData(): void
    {
        $xData = new _HtmlXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}