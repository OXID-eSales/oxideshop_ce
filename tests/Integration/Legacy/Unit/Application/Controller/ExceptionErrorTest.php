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
     */
    public function testRender()
    {
        $oErr = oxNew('exceptionError');
        $this->assertEquals('message/exception', $oErr->render());
    }

    /**
     * Test setting errors to view
     */
    public function testDisplayExceptionError()
    {
        $sEx = "testText";
        $aErrors = ["default" => ["aaa" => serialize($sEx)]];

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, ["getErrors"]);
        $oErr->expects($this->once())->method('getErrors')->will($this->returnValue($aErrors));

        $oErr->displayExceptionError();

        $aTplVars = $oErr->getViewDataElement("Errors");
        $oViewEx = $aTplVars["default"]["aaa"];

        $this->assertEquals($sEx, $oViewEx);
    }

    /**
     * Test setting errors to view resets errors in session
     */
    public function testDisplayExceptionError_resetsErrorsInSession()
    {
        $this->getSession()->setVariable("Errors", "testValue");
        $this->assertEquals("testValue", $this->getSession()->getVariable("Errors"));

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, ["getErrors", 'getViewData']);
        $oErr->expects($this->once())->method('getViewData')->will($this->returnValue([]));
        $oErr->expects($this->once())->method('getErrors')->will($this->returnValue([]));

        $oErr->displayExceptionError();

        $this->assertEquals([], $this->getSession()->getVariable("Errors"));
    }

    /**
     * Test getting errors array
     */
    public function testGetErrors()
    {
        $this->getSession()->setVariable("Errors", "testValue");

        $oErr = $this->getProxyClass("exceptionError");
        $this->assertEquals("testValue", $oErr->getErrors());
    }
}
