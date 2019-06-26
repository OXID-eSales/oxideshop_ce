<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \stdClass;
use \SimpleXMLElement;

/**
 * Testing oxXml class.
 */
class SimpleXmlTest extends \OxidTestCase
{
    public function testObjectToXml()
    {
        $oXml = oxNew('oxSimpleXml');

        $oTestObject = oxNew('StdClass');
        $oTestObject->title = "TestTitle";
        $oTestObject->keys = oxNew('StdClass');
        $oTestObject->keys->key = array("testKey1", "testKey2");

        $sTestResult = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $sTestResult .= "<testXml>";
        $sTestResult .= "<title>TestTitle</title>";
        $sTestResult .= "<keys><key>testKey1</key><key>testKey2</key></keys>";
        $sTestResult .= "</testXml>\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml($oTestObject, "testXml"));
    }

    public function testObjectToXmlWithObjectsInArray()
    {
        $oXml = oxNew('oxSimpleXml');

        $oModule1 = new stdClass();
        $oModule1->id = "id1";
        $oModule1->active = true;

        $oModule2 = new stdClass();
        $oModule2->id = "id2";
        $oModule2->active = false;

        $oTestObject = oxNew('StdClass');
        $oTestObject->title = "TestTitle";
        $oTestObject->modules = oxNew('StdClass');
        $oTestObject->modules->module = array($oModule1, $oModule2);

        $oExpectedXml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"utf-8\"?><testXml/>");
        $oExpectedXml->addChild("title", "TestTitle");
        $modules = $oExpectedXml->addChild("modules");

        $module = $modules->addChild("module");
        $module->addChild('id', 'id1');
        $module->addChild('active', '1');

        $module = $modules->addChild("module");
        $module->addChild('id', 'id2');
        $module->addChild('active', '');

        $this->assertEquals($oExpectedXml->asXML(), $oXml->objectToXml($oTestObject, "testXml"));
    }

    public function testXmlToObject()
    {
        $oXml = oxNew('oxSimpleXml');

        $sTestXml = '<?xml version="1.0"?>';
        $sTestXml .= '<testXml>';
        $sTestXml .= '<title>TestTitle</title>';
        $sTestXml .= '<keys><key>testKey1</key><key>testKey2</key></keys>';
        $sTestXml .= '</testXml>';

        $oRes = $oXml->xmlToObject($sTestXml);

        $this->assertEquals((string) $oRes->title, "TestTitle");
        $this->assertEquals((string) $oRes->keys->key[0], "testKey1");
        $this->assertEquals((string) $oRes->keys->key[1], "testKey2");
    }

    public function testObjectToXmlWithElementsAndAttributes()
    {
        $oXml = oxNew('oxSimpleXml');

        $oElement1 = new stdClass();
        $oElement1->id = 'id1';
        $oElement1->active = true;

        $oElement2 = new stdClass();
        $oElement2->id = array('attributes' => array('attr1' => 'value1', 'attr2' => 'value2'), 'value' => 'id2');
        $oElement2->active = true;

        $oTestObject = new stdClass();
        $oTestObject->elements = new stdClass();
        $oTestObject->elements->element = array(array('attributes' => array('attr3' => 'value3'), 'value' => $oElement1), $oElement2);

        $sTestResult = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $sTestResult .= '<testXml>';
        $sTestResult .= '<elements>';
        $sTestResult .= '<element attr3="value3"><id>id1</id><active>1</active></element>';
        $sTestResult .= '<element><id attr1="value1" attr2="value2">id2</id><active>1</active></element>';
        $sTestResult .= '</elements>';
        $sTestResult .= '</testXml>' . "\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml($oTestObject, "testXml"));
    }

    public function testObjectToXmlWithElementsWithAttributesKey()
    {
        $oXml = oxNew('oxSimpleXml');

        $oTestObject = new stdClass();
        $oTestObject->attributes = new stdClass();
        $oTestObject->attributes->attribute = array('attrValue1', 'attrValue2');

        $sTestResult = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        $sTestResult .= '<testXml>';
        $sTestResult .= '<attributes>';
        $sTestResult .= '<attribute>attrValue1</attribute>';
        $sTestResult .= '<attribute>attrValue2</attribute>';
        $sTestResult .= '</attributes>';
        $sTestResult .= '</testXml>' . "\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml($oTestObject, "testXml"));
    }

    public function testObjectToXmlWithAssocArrayKeys()
    {
        $oXml = oxNew('oxSimpleXml');

        $oTestObject = oxNew('StdClass');
        $oTestObject->elements = array('element' => array(
            array('key1' => 'value1', 'key2' => 'value2'),
            array('key1' => 'value1', 'key2' => 'value2')
        ));

        $sTestResult = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
        $sTestResult .= "<testXml>";
        $sTestResult .= "<elements>";
        $sTestResult .= "<element><key1>value1</key1><key2>value2</key2></element>";
        $sTestResult .= "<element><key1>value1</key1><key2>value2</key2></element>";
        $sTestResult .= "</elements>";
        $sTestResult .= "</testXml>\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml($oTestObject, "testXml"));
    }
}
