<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcExamples\html;

use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use pvc\html\attribute\factory\AttributeFactory;
use pvc\html\tag\basic_tags\TagBody;
use pvc\html\tag\factory\TagFactory;
use pvc\validator\dflt\ValTesterAlwaysTrue;

/**
 * Class TagColForProject
 */
class TagForProject extends TestCase
{
    protected TagFactory $tagFactory;

    protected Container $container;

    public function setUp(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(__DIR__ . '../src/attribute/factory/AttributeDiConfig.php');
        $container = $builder->build();

        $this->tagFactory = $container->get(TagFactory::class);
    }

    public function testTag(): void
    {
        $tagName = 'body';
        $tag = $this->tagFactory->makeTag($tagName);

        $cssClasses = 'myClass1 myClass2';
        $tag->setAttribute('class', $cssClasses);

        $customData = 'someString';
        $customAttributeName = 'specialthing';
        $tester = new ValTesterAlwaysTrue();
        $tag->setCustomData($customAttributeName, $customData, $tester);

        $eventName = 'onload';
        $msg = '"onload event!"';
        $script = 'alert(' . $msg . ');';
        $tag->setEvent($eventName, $script);

        $expectedOutput = "<" . $tagName . " class='myClass1 myClass2' ";
        $expectedOutput .= "data-" . $customAttributeName . "='" . $customData . "' ";
        $expectedOutput .= $eventName . '=\'' . $script . '\'';
        $expectedOutput .= "></" . $tagName . ">";
        self::assertEquals($expectedOutput, $tag->display());
    }
}