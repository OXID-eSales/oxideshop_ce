<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxRegistry;

/**
 * Tests for contact class
 */
class ExceptionErrorTest extends \OxidTestCase
{

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $oErr = oxNew('exceptionError');
        $this->assertEquals('message/exception.tpl', $oErr->render());
    }

    /**
     * Test setting errors to view
     *
     * @return null
     */
    public function testDisplayExceptionError()
    {
        $sEx = "testText";
        $aErrors = array("default" => array("aaa" => serialize($sEx)));

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, array("_getErrors"));
        $oErr->expects($this->once())->method('_getErrors')->will($this->returnValue($aErrors));

        $oErr->displayExceptionError();

        $aTplVars = $oErr->getViewDataElement("Errors");
        $oViewEx = $aTplVars["default"]["aaa"];

        $this->assertEquals($sEx, $oViewEx);
    }

    /**
     * Test setting errors to view resets errors in session
     *
     * @return null
     */
    public function testDisplayExceptionError_resetsErrorsInSession()
    {
        $this->getSession()->setVariable("Errors", "testValue");
        $this->assertEquals("testValue", $this->getSession()->getVariable("Errors"));

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, array("_getErrors", 'getViewData'));
        $oErr->expects($this->once())->method('getViewData')->will($this->returnValue(array()));
        $oErr->expects($this->once())->method('_getErrors')->will($this->returnValue(array()));

        $oErr->displayExceptionError();

        $this->assertEquals(array(), $this->getSession()->getVariable("Errors"));
    }

    /**
     * Test getting errors array
     *
     * @return null
     */
    public function testGetErrors()
    {
        $this->getSession()->setVariable("Errors", "testValue");

        $oErr = $this->getProxyClass("exceptionError");
        $this->assertEquals("testValue", $oErr->UNITgetErrors());
    }
}
