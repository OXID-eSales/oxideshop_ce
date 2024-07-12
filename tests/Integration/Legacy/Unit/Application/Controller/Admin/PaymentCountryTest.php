<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Payment;
use \Exception;
use \oxTestModules;

/**
 * Tests for Payment_Country class
 */
class PaymentCountryTest extends \OxidTestCase
{

    /**
     * Payment_Country::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertEquals('payment_country', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof payment);
    }

    /**
     * Statistic_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertEquals('payment_country', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Payment_Country::Addcountry() test case
     */
    public function testAddcountry()
    {
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("allcountries", ["testCountryId"]);
        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Payment_Country');
            $oView->addcountry();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "Error in Payment_Country::addcountry()");

            return;
        }

        $this->fail("Error in Payment_Country::addcountry()");
    }

    /**
     * Payment_Country::Removecountry() test case
     */
    public function testRemovecountry()
    {
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("countries", ["testCountryId"]);
        oxTestModules::addFunction('oxbase', 'delete', '{ throw new Exception( "delete" ); }');

        // testing..
        try {
            $oView = oxNew('Payment_Country');
            $oView->removecountry();
        } catch (Exception $exception) {
            $this->assertEquals("delete", $exception->getMessage(), "Error in Payment_Country::removecountry()");

            return;
        }

        $this->fail("Error in Payment_Country::removecountry()");
    }
}
