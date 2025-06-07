<?php

namespace pvc\htmlbuilder;

$projectRoot = 'I:/www/pvcHtml/';
$builderDir = $projectRoot . 'builder/';

include $projectRoot . 'vendor/autoload.php';

use pvc\html\factory\HtmlFactory;
use pvc\htmlbuilder\config\BaseConfig;
use pvc\htmlbuilder\config\BuilderConfig;
use pvc\htmlbuilder\dicontainer\LeagueDefinitionBuilder;
use pvc\htmlbuilder\html\AttributeBuilder;
use pvc\htmlbuilder\html\ElementBuilder;
use pvc\htmlbuilder\html\EventBuilder;

/**
 * location of json data used to build attributes and elements
 */
$jsonDefDirectory = $builderDir.'definitions/json/';
$srcDir = $projectRoot . 'src/';
$srcNamespace = 'pvc\\html\\';
$exceptionDir = 'err';
$containerDefsDir = 'factory';
$containerDefsFileName = 'LeagueDefinitions.php';

$baseConfig = new BaseConfig(
    $jsonDefDirectory, $srcDir, $srcNamespace, $containerDefsDir, $containerDefsFileName, $exceptionDir,
);

$jsonDefsAttributes = 'AttributeDefs.json';
$targetAttributeDirectory = 'attributes';
$baseAttributeNamespace = 'attribute';

$jsonDefsEvents = 'EventDefs.json';
$targetEventDirectory = 'events';
$baseEventNamespace = 'event';

$jsonDefsElements = 'ElementDefs.json';
$targetElementDirectory = 'elements';
$baseElementNamespace = 'element';

/**
 * these two support the testing of values that are assigned to attributes.
 * The only thing that builder writes is the definitions that construct
 * these things in the di container so we don't need a full config object, all
 * we need is the json definitions
 */
$jsonDefsAttributeValueTesters = $jsonDefDirectory . '/' . 'AttributeValueTesterDefs.json';
$jsonDefsOthers = $jsonDefDirectory . '/' . 'OtherDefs.json';

/**
 * write out the source code for all the attributes
 */
$attributeConfig = new BuilderConfig(
    $baseConfig,
    $jsonDefsAttributes,
    $targetAttributeDirectory,
    $baseAttributeNamespace,
);
$builder = new AttributeBuilder($baseConfig, $attributeConfig);
$builder->makeTraits();

/**
 * events
 */

$eventConfig = new BuilderConfig(
    $baseConfig,
    $jsonDefsEvents,
    $targetEventDirectory,
    $baseEventNamespace,
);
$builder = new EventBuilder($baseConfig, $eventConfig);
$builder->makeTraits();

/**
 * elements
 */

$elementConfig = new BuilderConfig(
    $baseConfig,
    $jsonDefsElements,
    $targetElementDirectory,
    $baseElementNamespace,
);
/**
 * building elements also requires attribute definitions and a reference to
 * the HtmlFactory class itself so there are two additional
 * parameters to the construction of the element builder class
 */
$builder = new ElementBuilder(
    $baseConfig,
    $elementConfig,
    $attributeConfig,
    $eventConfig,
    HtmlFactory::class,
);
$builder->makeElementClasses();

/**
 * create definitions for the di container which creates all html objects
 */
$builder = new LeagueDefinitionBuilder(
    $baseConfig,
    $attributeConfig,
    $jsonDefsAttributeValueTesters,
    $jsonDefsOthers,
    $eventConfig,
    $elementConfig,
);
$builder->makeLeagueDefinitions();

