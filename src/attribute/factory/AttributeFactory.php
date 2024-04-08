<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\html\attribute\factory;

use Psr\Container\ContainerInterface;
use pvc\html\attribute\abstract\Attribute;
use pvc\html\attribute\abstract\AttributeCustomData;
use pvc\html\attribute\abstract\Event;
use pvc\html\config\HtmlConfig;
use pvc\html\err\InvalidAttributeException;
use pvc\html\err\InvalidAttributeNameException;
use pvc\html\err\InvalidCustomDataNameException;
use pvc\html\err\InvalidEventScriptException;
use pvc\interfaces\html\attribute\AttributeCustomDataInterface;
use pvc\interfaces\html\attribute\AttributeInterface;
use pvc\interfaces\html\attribute\EventInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class AttributeFactory
 */
class AttributeFactory
{
    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $defaultValTester;

    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $customDataNameTester;

    /**
     * @var ContainerInterface $valtesterContainer
     */
    protected ContainerInterface $valtesterContainer;

    /**
     * @param ValTesterInterface<string> $defaultValTester
     * @param ValTesterInterface<string> $customDataNameTester
     * @param ContainerInterface $valtesterContainer
     */
    public function __construct(
        ValTesterInterface $defaultValTester,
        ValTesterInterface $customDataNameTester,
        ContainerInterface $valtesterContainer
    ) {
        $this->defaultValTester = $defaultValTester;
        $this->customDataNameTester = $customDataNameTester;
        $this->valtesterContainer = $valtesterContainer;
    }

    /**
     * makeAttribute
     * @param string $attributeName
     * @return Attribute
     * @throws InvalidAttributeNameException
     *
     * note that you can make events with makeAttribute as well!
     */
    public function makeAttribute(string $attributeName): AttributeInterface
    {
        if (!HtmlConfig::isValidAttributeName($attributeName)) {
            throw new InvalidAttributeException($attributeName);
        }
        $attributeType = HtmlConfig::getAttributeType($attributeName);
        $tester = (
        $this->valtesterContainer->has($attributeName) ?
            $this->valtesterContainer->get($attributeName) :
            $this->defaultValTester
        );
        $attribute = new $attributeType($tester);
        /** @var Attribute $attribute */
        $attribute->setName($attributeName);
        return $attribute;
    }

    /**
     * makeCustomDataAttribute
     * @param string $name
     * @param ValTesterInterface<string> $valTester
     * @return AttributeCustomDataInterface
     * @throws InvalidAttributeNameException
     * @throws InvalidCustomDataNameException
     */
    public function makeCustomDataAttribute(string $name, ValTesterInterface $valTester): AttributeCustomDataInterface
    {
        $attribute = new AttributeCustomData($this->customDataNameTester, $valTester);
        $attribute->setName($name);
        return $attribute;
    }

    /**
     * makeEvent
     * @param string $eventName
     * @return Event
     * @throws \pvc\html\err\InvalidEventNameException
     */
    public function makeEvent(string $eventName): EventInterface
    {
        $event = new Event();
        $event->setName($eventName);
        return $event;
    }
}
