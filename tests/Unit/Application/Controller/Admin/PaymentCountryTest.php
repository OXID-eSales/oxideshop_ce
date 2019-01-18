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
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertEquals('payment_country.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof payment);
    }

    /**
     * Statistic_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Payment_Country');
        $this->assertEquals('payment_country.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Payment_Country::Addcountry() test case
     *
     * @return null
     */
    public function testAddcountry()
    {
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("allcountries", array("testCountryId"));
        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception( "save" ); }');

        // testing..
        try {
            $oView = oxNew('Payment_Country');
            $oView->addcountry();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "Error in Payment_Country::addcountry()");

            return;
        }
        $this->fail("Error in Payment_Country::addcountry()");
    }

    /**
     * Payment_Country::Removecountry() test case
     *
     * @return null
     */
    public function testRemovecountry()
    {
        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("countries", array("testCountryId"));
        oxTestModules::addFunction('oxbase', 'delete', '{ throw new Exception( "delete" ); }');

        // testing..
        try {
            $oView = oxNew('Payment_Country');
            $oView->removecountry();
        } catch (Exception $oExcp) {
            $this->assertEquals("delete", $oExcp->getMessage(), "Error in Payment_Country::removecountry()");

            return;
        }
        $this->fail("Error in Payment_Country::removecountry()");
    }
}
