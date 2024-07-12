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
class ExceptionErrorTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test view render.
     */
    public function testRender()
    {
        $oErr = oxNew('exceptionError');
        $this->assertSame('message/exception', $oErr->render());
    }

    /**
     * Test setting errors to view
     */
    public function testDisplayExceptionError()
    {
        $sEx = "testText";
        $aErrors = ["default" => ["aaa" => serialize($sEx)]];

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, ["getErrors"]);
        $oErr->expects($this->once())->method('getErrors')->willReturn($aErrors);

        $oErr->displayExceptionError();

        $aTplVars = $oErr->getViewDataElement("Errors");
        $oViewEx = $aTplVars["default"]["aaa"];

        $this->assertSame($sEx, $oViewEx);
    }

    /**
     * Test setting errors to view resets errors in session
     */
    public function testDisplayExceptionError_resetsErrorsInSession()
    {
        $this->getSession()->setVariable("Errors", "testValue");
        $this->assertSame("testValue", $this->getSession()->getVariable("Errors"));

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, ["getErrors", 'getViewData']);
        $oErr->expects($this->once())->method('getViewData')->willReturn([]);
        $oErr->expects($this->once())->method('getErrors')->willReturn([]);

        $oErr->displayExceptionError();

        $this->assertSame([], $this->getSession()->getVariable("Errors"));
    }

    /**
     * Test getting errors array
     */
    public function testGetErrors()
    {
        $this->getSession()->setVariable("Errors", "testValue");

        $oErr = $this->getProxyClass("exceptionError");
        $this->assertSame("testValue", $oErr->getErrors());
    }
}
