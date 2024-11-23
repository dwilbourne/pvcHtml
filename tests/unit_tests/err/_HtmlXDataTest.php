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
     * @covers \pvc\html\err\AttributeNotAllowedException
     * @covers \pvc\html\err\InvalidDefinitionsFileException
     * @covers \pvc\html\err\InvalidAttributeException
     * @covers \pvc\html\err\InvalidAttributeIdNameException
     * @covers \pvc\html\err\InvalidAttributeValueException
     * @covers \pvc\html\err\InvalidCustomDataNameException
     * @covers \pvc\html\err\InvalidEventNameException
     * @covers \pvc\html\err\InvalidNumberOfParametersException
     * @covers \pvc\html\err\ChildElementNotAllowedException
     * @covers \pvc\html\err\InvalidTagNameException
     * @covers \pvc\html\err\MakeDefinitionException
     * @covers \pvc\html\err\InvalidAttributeValueTesterNameException
     * @covers \pvc\html\err\DuplicateDefinitionIdException
     * @covers \pvc\html\err\InvalidDefinitionIdException
     * @covers \pvc\html\err\DTOExtraPropertyException
     * @covers \pvc\html\err\DTOMissingPropertyException
     * @covers \pvc\html\err\DTOInvalidPropertyValueException
     */
    public function testValidatorXData(): void
    {
        $xData = new _HtmlXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}