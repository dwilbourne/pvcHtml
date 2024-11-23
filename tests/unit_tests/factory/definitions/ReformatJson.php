<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\html\unit_tests\factory\definitions;

use PHPUnit\Framework\TestCase;

/**
 * Class ReformatJson
 */
class ReformatJson extends TestCase
{
    protected string $dirName = 'I:/www/pvcHtml/src/factory/definitions/json/';

    public function testReformatAttributeDefs(): void
    {
        $srcFile = 'AttributeDefs.json';
        $targetFile = 'AttributeDefs.json';

        $defsArray = json_decode(file_get_contents($this->dirName . $srcFile), true);
        $newDefsArray = [];
        foreach($defsArray as $def) {
            $newDef['defId'] = $def['defId'];
            $newDef['defType'] = 'Attribute';
            $newDef['concrete'] = $def['concrete'];
            $newDef['name'] = $def['name'];
            $newDef['valTester'] = $def['valTester'];
            $newDef['caseSensitive'] = $def['caseSensitive'];
            $newDef['global'] = $def['global'];
            $newDefsArray[] = $newDef;
        }
        file_put_contents($this->dirName . $targetFile, json_encode($newDefsArray));
    }

    public function testReformatAttributeValueTesterDefs(): void
    {
        $srcFile = 'AttributeValueTesterDefs.json';
        $targetFile = 'AttributeValueTesterDefs.json';

        $defsArray = json_decode(file_get_contents($this->dirName . $srcFile), true);
        $newDefsArray = [];
        foreach($defsArray as $def) {
            $newDef['defId'] = $def['defId'];
            $newDef['defType'] = 'AttributeValueTester';
            $newDef['concrete'] = $def['concrete'];
            $newDef['arg'] = $def['arg'];
            $newDefsArray[] = $newDef;
        }
        file_put_contents($this->dirName . $targetFile, json_encode($newDefsArray));
    }

    public function testReformatElementDefs(): void
    {
        $srcFile = 'ElementDefs.json';
        $targetFile = 'ElementDefs.json';

        $defsArray = json_decode(file_get_contents($this->dirName . $srcFile), true);
        $newDefsArray = [];
        foreach($defsArray as $def) {
            $newDef['defId'] = $def['defId'];
            $newDef['defType'] = 'Element';
            $newDef['concrete'] = $def['concrete'];
            $newDef['comment'] = $def['comment'];
            $newDef['name'] = $def['name'];
            $newDef['allowedAttributeDefIds'] = $def['allowedAttributeDefIds'];
            $newDef['allowedChildDefIds'] = $def['allowedChildDefIds'];
            $newDefsArray[] = $newDef;
        }
        file_put_contents($this->dirName . $targetFile, json_encode($newDefsArray));
    }

    public function testReformatEventDefs(): void
    {
        $srcFile = 'EventDefs.json';
        $targetFile = 'EventDefs.json';

        $defsArray = json_decode(file_get_contents($this->dirName . $srcFile), true);
        $newDefsArray = [];
        foreach($defsArray as $def) {
            $newDef['defId'] = $def['defId'];
            $newDef['defType'] = 'Event';
            $newDef['name'] = $def['name'];
            $newDef['concrete'] = $def['concrete'];
            $newDefsArray[] = $newDef;
        }
        file_put_contents($this->dirName . $targetFile, json_encode($newDefsArray));
    }

    public function testReformatOtherDefs(): void
    {
        $srcFile = 'OtherDefs.json';
        $targetFile = 'OtherDefs.json';

        $defsArray = json_decode(file_get_contents($this->dirName . $srcFile), true);
        $newDefsArray = [];
        foreach($defsArray as $def) {
            $newDef['defId'] = $def['defId'];
            $newDef['defType'] = 'Other';
            $newDef['concrete'] = $def['concrete'];
            $newDef['arg'] = $def['arg'];
            $newDef['shared'] = $def['shared'];
            $newDefsArray[] = $newDef;
        }
        file_put_contents($this->dirName . $targetFile, json_encode($newDefsArray));
    }

    public function testConsolidateDefinitions(): void
    {
        $final = '';
        $final .= file_get_contents($this->dirName . 'AttributeDefs.json');
        $final .= file_get_contents($this->dirName . 'AttributeValueTesterDefs.json');
        $final .= file_get_contents($this->dirName . 'ElementDefs.json');
        $final .= file_get_contents($this->dirName . 'EventDefs.json');
        $final .= file_get_contents($this->dirName . 'OtherDefs.json');
        /**
         * gluing these together puts arrays "back to back", so take out the ending/beginning array brackets and it
         * becomes a single array
         */
        $final = str_replace('][', ',', $final);
        file_put_contents($this->dirName . '/../Definitions.json', $final);
    }

}