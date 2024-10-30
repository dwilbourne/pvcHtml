<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\unit_tests\err;

use pvc\err\XDataTestMaster;
use pvc\html\err\_HtmlXData;
use ReflectionException;

/**
 * Class _HtmlXDataTest
 */
class _HtmlXDataTest extends XDataTestMaster
{
    /**
     * testValidatorXData
     * @throws ReflectionException
     * @covers \pvc\html\err\_HtmlXData::getLocalXCodes
     * @covers \pvc\html\err\_HtmlXData::getXMessageTemplates
     * @covers \pvc\html\err\AmbiguousMethodCallException
     * @covers \pvc\html\err\AttributeNotAllowedException
     * @covers \pvc\html\err\DefinitionsFileException
     * @covers \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\err\InvalidEventNameException
     * @covers \pvc\html\err\InvalidInnerTextException
     * @covers \pvc\html\err\InvalidMethodCallException
     * @covers \pvc\html\err\InvalidNumberOfParametersException
     * @covers \pvc\html\err\InvalidSubTagException
     * @covers \pvc\html\err\InvalidTagNameException
     * @covers \pvc\html\err\UnsetAttributeNameException
     * @covers \pvc\html\err\UnsetTagNameException
     */
    public function testValidatorXData(): void
    {
        $xData = new _HtmlXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}