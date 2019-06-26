<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use \oxRegistry;
use \oxTestModules;

class ExceptionToDisplayTest extends \OxidTestCase
{
    public function testSetGetStackTrace()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setStackTrace('stack trace');
        $this->assertEquals('stack trace', $oTestObject->getStackTrace());
    }

    public function testSetGetValues()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setValues(array(1 => 'test1', 2 => 'test2'));
        $this->assertEquals('test2', $oTestObject->getValue(2));
    }

    public function testAddGetValues()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setValues(array(1 => 'test1', 2 => 'test2'));
        $oTestObject->addValue(4, 'test4');
        $this->assertEquals('test4', $oTestObject->getValue(4));
    }

    public function testSetGetExceptionType()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setExceptionType('test type');
        $this->assertEquals('test type', $oTestObject->getErrorClassType());
    }

    public function testSetDebug()
    {
        $oTestObject = $this->getProxyClass("oxExceptionToDisplay");
        $oTestObject->setDebug(2);
        //nothing should happen in unittests
        $this->assertEquals(2, $oTestObject->getNonPublicVar('_blDebug'));
    }

    public function testSetGetMessage()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setMessage("TEST_EXCEPTION");
        //nothing should happen in unittests
        $this->assertEquals("TEST_EXCEPTION", $oTestObject->getOxMessage());
    }

    public function testSetGetMessage_withStringArguments()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setMessageArgs(100, "200", "mineralinis");
        $oTestObject->setMessage("TEST %d ERROR %s STRING %s");

        $this->assertEquals("TEST 100 ERROR 200 STRING mineralinis", $oTestObject->getOxMessage());
    }

    public function testSetGetMessageIfDebugOn()
    {
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setMessage("TEST_EXCEPTION");
        $oTestObject->setDebug(1);
        //nothing should happen in unittests
        $this->assertEquals($oTestObject, $oTestObject->getOxMessage());
    }

    public function testToString()
    {
        oxTestModules::addFunction('oxUtilsDate', 'getTime', '{return ' . (time() - 90) . ';}');
        $oTestObject = oxNew('oxExceptionToDisplay');
        $oTestObject->setExceptionType('testType');
        $oTestObject->setStackTrace('testStackTrace');
        $oTestObject->setValues(array(1 => 'test1', 2 => 'test2'));
        $oTestObject->setMessage("TEST_EXCEPTION");
        $sRet = "testType (time: " . date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()) . "): TEST_EXCEPTION \n Stack Trace: testStackTrace\n";
        $sRet .= "1 => test1\n";
        $sRet .= "2 => test2\n";
        //nothing should happen in unittests
        $this->assertEquals($sRet, $oTestObject->__toString());
    }

    public function testSetMessageArgs()
    {
        $oTestObject = $this->getProxyClass("oxExceptionToDisplay");
        $oTestObject->setMessageArgs(100, "200", "testString");

        $this->assertEquals(array(100, "200", "testString"), $oTestObject->getNonPublicVar('_aMessageArgs'));
    }
}
