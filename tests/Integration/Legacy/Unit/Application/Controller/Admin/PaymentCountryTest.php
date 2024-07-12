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
class PaymentCountryTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Payment_Country::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertSame('payment_country', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Payment::class, $aViewData['edit']);
    }

    /**
     * Statistic_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertSame('payment_country', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
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
            $this->assertSame("save", $exception->getMessage(), "Error in Payment_Country::addcountry()");

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
            $this->assertSame("delete", $exception->getMessage(), "Error in Payment_Country::removecountry()");

            return;
        }

        $this->fail("Error in Payment_Country::removecountry()");
    }
}
