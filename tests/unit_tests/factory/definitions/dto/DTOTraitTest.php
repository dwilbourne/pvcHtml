<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions\dto;

use PHPUnit\Framework\TestCase;
use pvc\html\err\DTOExtraPropertyException;
use pvc\html\err\DTOInvalidPropertyValueException;
use pvc\html\err\DTOMissingPropertyException;

class DTOTraitTest extends TestCase
{
    protected $dto;

    public function setUp(): void
    {
        $this->dto = new AttributeDTO();
    }

    /**
     * testMissingProperties
     * @throws DTOMissingPropertyException
     * @throws \pvc\html\err\DTOExtraPropertyException
     * @throws \pvc\html\err\DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::hydrateFromArray
     */
    public function testMissingProperties(): void
    {
        $properties = ['defId' => 'foo', 'deftype' => 'AttributeVoid'];
        self::expectException(DTOMissingPropertyException::class);
        $this->dto->hydrateFromArray($properties);
    }

    /**
     * testPermitExtraPropertiesIsFalse
     * @throws DTOExtraPropertyException
     * @throws DTOMissingPropertyException
     * @throws \pvc\html\err\DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::hydrateFromArray
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::extraPropertiesPermitted
     */
    public function testPermitExtraPropertiesIsFalse(): void
    {
        $properties = [
            'defId' => 'foo',
            'defType' => 'bar',
            'concrete' => 'AttributeVoid',
            'name' => 'accept',
            'valTester' => 'pvc\html\val_tester\MediaTypeTester',
            'caseSensitive' => false,
            'global' => false,
            'quux' => 'will throw an exception in this test'
        ];
        self::assertFalse($this->dto->extraPropertiesPermitted());
        self::expectException(DTOExtraPropertyException::class);
        $this->dto->hydrateFromArray($properties);
    }

    /**
     * testPermitExtraPropertiesIsTrue
     * @throws DTOExtraPropertyException
     * @throws DTOMissingPropertyException
     * @throws \pvc\html\err\DTOInvalidPropertyValueException
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::hydrateFromArray
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::permitExtraProperties
     */
    public function testPermitExtraPropertiesIsTrue(): void
    {
        $properties = [
            'defId' => 'foo',
            'defType' => 'bar',
            'concrete' => 'AttributeVoid',
            'name' => 'accept',
            'valTester' => 'pvc\html\val_tester\MediaTypeTester',
            'caseSensitive' => false,
            'global' => false,
            'quux' => 'will not throw an exception in this test'
        ];
        $this->dto->permitExtraProperties();
        $this->dto->hydrateFromArray($properties);
        self::assertTrue(true);
    }

    /**
     * testBadPropertyValueTypeThrowsException
     * @throws DTOExtraPropertyException
     * @throws DTOInvalidPropertyValueException
     * @throws DTOMissingPropertyException
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::hydrateFromArray
     */
    public function testBadPropertyValueTypeThrowsException(): void
    {
        $properties = [
            'defId' => 'foo',
            'defType' => 'bar',
            'concrete' => 'AttributeVoid',
            'name' => 'accept',
            'valTester' => 'pvc\html\val_tester\MediaTypeTester',
            'caseSensitive' => 9,
            'global' => [1, 2, 3],
        ];
        self::expectException(DTOInvalidPropertyValueException::class);
        $this->dto->hydrateFromArray($properties);
    }

    /**
     * testToArray
     * @throws DTOExtraPropertyException
     * @throws DTOInvalidPropertyValueException
     * @throws DTOMissingPropertyException
     * @covers \pvc\html\factory\definitions\dto\DTOTrait::toArray
     */
    public function testToArray(): void
    {
        $properties = [
            'defId' => 'foo',
            'defType' => 'bar',
            'concrete' => 'AttributeVoid',
            'name' => 'accept',
            'valTester' => 'pvc\html\val_tester\MediaTypeTester',
            'caseSensitive' => false,
            'global' => false,
        ];
        $this->dto->hydrateFromArray($properties);
        self::assertEqualsCanonicalizing($properties, $this->dto->toArray());
    }
}
