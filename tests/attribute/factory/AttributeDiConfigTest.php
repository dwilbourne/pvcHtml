<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\html\attribute\factory;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\attribute\abstract\AttributeMultiValue;
use pvc\html\attribute\abstract\AttributeSingleValue;
use pvc\html\attribute\abstract\AttributeVoid;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeDiConfigTest
 * @coversNothing
 */
class AttributeDiConfigTest extends TestCase
{
    protected ContainerInterface $container;

    public function setUp(): void
    {
        $builder = new ContainerBuilder();
        $src = __DIR__ . '/../../../src';
        $builder->addDefinitions($src . '/attribute/factory/AttributeDiConfig.php');
        $this->container = $builder->build();
    }

    /**
     * testMakeAccessKey
     */
    public function testMakeAccessKey(): void
    {
        $this->testSingleValueAttribute($this->container->get('accesskey'));
    }

    protected function testSingleValueAttribute(Attribute $attribute): void
    {
        self::assertInstanceOf(AttributeSingleValue::class, $attribute);
        $this->testAttributeTester($attribute);
    }

    protected function testAttributeTester(Attribute $attribute): void
    {
        self::assertInstanceOf(ValTesterInterface::class, $attribute->getTester());
    }

    /**
     * testMakeCssClass
     */
    public function testMakeCssClass(): void
    {
        $this->testMultiValueAttribute($this->container->get('class'));
    }

    protected function testMultiValueAttribute(Attribute $attribute): void
    {
        self::assertInstanceOf(AttributeMultiValue::class, $attribute);
        $this->testAttributeTester($attribute);
    }

    /**
     * testMakeContentEditable
     */
    public function testMakeContentEditable(): void
    {
        $this->testSingleValueAttribute($this->container->get('contenteditable'));
    }

    /**
     * testMakeTextDirection
     */
    public function testMakeTextDirection(): void
    {
        $this->testSingleValueAttribute($this->container->get('dir'));
    }

    /**
     * testMakeDraggable
     */
    public function testMakeDraggable(): void
    {
        $this->testSingleValueAttribute($this->container->get('draggable'));
    }

    /**
     * testMakeHidden
     */
    public function testMakeHidden(): void
    {
        $this->testVoidAttribute($this->container->get('hidden'));
    }

    protected function testVoidAttribute(Attribute $attribute)
    {
        self::assertInstanceOf(AttributeVoid::class, $attribute);
    }

    /**
     * testMakeId
     */
    public function testMakeId(): void
    {
        $this->testSingleValueAttribute($this->container->get('id'));
    }

    /**
     * testMakeLang
     */
    public function testMakeLang(): void
    {
        $this->testSingleValueAttribute($this->container->get('lang'));
    }

    /**
     * testMakeSpellcheck
     */
    public function testMakeSpellcheck(): void
    {
        $this->testSingleValueAttribute($this->container->get('spellcheck'));
    }

    /**
     * testMakeStyle
     */
    public function testMakeStyle(): void
    {
        $this->testSingleValueAttribute($this->container->get('style'));
    }

    /**
     * testMakeTabIndex
     */
    public function testMakeTabIndex(): void
    {
        $this->testSingleValueAttribute($this->container->get('tabindex'));
    }

    /**
     * testMakeTitle
     */
    public function testMakeTitle(): void
    {
        $this->testSingleValueAttribute($this->container->get('title'));
    }

    /**
     * testMakeTranslate
     */
    public function testMakeTranslate(): void
    {
        $this->testSingleValueAttribute($this->container->get('translate'));
    }

    /**
     * testMakeHref
     */
    public function testMakeHref(): void
    {
        $this->testSingleValueAttribute($this->container->get('href'));
    }

    /**
     * testMakeHrefLang
     */
    public function testMakeHrefLang(): void
    {
        $this->testSingleValueAttribute($this->container->get('hreflang'));
    }

    /**
     * testMakeTarget
     */
    public function testMakeTarget(): void
    {
        $this->testSingleValueAttribute($this->container->get('target'));
    }

    /**
     * testMakeDownload
     */
    public function testMakeDownload(): void
    {
        $this->testSingleValueAttribute($this->container->get('download'));
    }

    /**
     * testMakeMedia
     */
    public function testMakeMedia(): void
    {
        $this->testSingleValueAttribute($this->container->get('media'));
    }

    /**
     * testMakePing
     */
    public function testMakePing(): void
    {
        $this->testSingleValueAttribute($this->container->get('ping'));
    }

    /**
     * testMakeReferrerPolicy
     */
    public function testMakeReferrerPolicy(): void
    {
        $this->testSingleValueAttribute($this->container->get('referrerpolicy'));
    }

    /**
     * testMakeRel
     */
    public function testMakeRel(): void
    {
        $this->testSingleValueAttribute($this->container->get('rel'));
    }

    /**
     * testMakeType
     */
    public function testMakeType(): void
    {
        $this->testSingleValueAttribute($this->container->get('type'));
    }

    /**
     * testMakeAlt
     */
    public function testMakeAlt(): void
    {
        $this->testSingleValueAttribute($this->container->get('alt'));
    }

    /**
     * testMakeXmlns
     */
    public function testMakeXmlns(): void
    {
        $this->testSingleValueAttribute($this->container->get('xmlns'));
    }

    /**
     * testMakeCrossOrigin
     */
    public function testMakeCrossOrigin(): void
    {
        $this->testSingleValueAttribute($this->container->get('crossorigin'));
    }

    /**
     * testMakeSizes
     */
    public function testMakeSizes(): void
    {
        $this->testSingleValueAttribute($this->container->get('sizes'));
    }
}
