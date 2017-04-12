<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
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

        $oErr = $this->getMock(\OxidEsales\Eshop\Application\Controller\ExceptionErrorController::class, array("_getErrors", 'getViewData'));
        $oErr->expects($this->once())->method('getViewData')->will($this->returnValue(array()));
        $oErr->expects($this->once())->method('_getErrors')->will($this->returnValue($aErrors));

        $oErr->displayExceptionError();

        $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
        $aTplVars = $oSmarty->get_template_vars("Errors");
        $oViewEx = $aTplVars["default"]["aaa"];

        $this->assertEquals("testText", $sEx);
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
