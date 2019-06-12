<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class SystemComponentExceptionTest extends \OxidTestCase
{
    public function testSetGetComponent()
    {
        $sComponent = "a Component";
        $oTestObject = oxNew('oxSystemComponentException');
        $this->assertContains('SystemComponentException', get_class($oTestObject));
        $oTestObject->setComponent($sComponent);
        $this->assertEquals($sComponent, $oTestObject->getComponent());
    }

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $sMsg = 'Erik was here..';
        $sComponent = "a Component";
        $oTestObject = oxNew('oxSystemComponentException', $sMsg);
        $oTestObject->setComponent($sComponent);
        $sStringOut = $oTestObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($sMsg, $sStringOut);
        $this->assertContains('SystemComponentException', $sStringOut);
        $this->assertContains($sComponent, $sStringOut);
    }

    public function testGetValues()
    {
        $oTestObject = oxNew('oxSystemComponentException');
        $sComponent = "a Component";
        $oTestObject->setComponent($sComponent);
        $aRes = $oTestObject->getValues();
        $this->assertArrayHasKey('component', $aRes);
        $this->assertTrue($sComponent === $aRes['component']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxSystemComponentException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
