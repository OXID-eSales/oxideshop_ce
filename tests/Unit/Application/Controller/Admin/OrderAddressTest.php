<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Order;

use \Exception;
use \oxTestModules;

/**
 * Tests for Order_Address class
 */
class OrderAddressTest extends \OxidTestCase
{

    /**
     * Order_Address::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Order_Address');
        $this->assertEquals('order_address.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof order);
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
        $oView = oxNew('Order_Address');
        $this->assertEquals('order_address.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Order_Address::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxorder', 'save', '{ throw new Exception( "save" ); }');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Order_Address');
            $oView->save();
        } catch (Exception $oExcp) {
            $this->assertEquals("save", $oExcp->getMessage(), "error in Order_Address::save()");

            return;
        }
        $this->fail("error in Order_Address::save()");
    }

    /**
     * Test case for Order_Address::_processAddress(), specially for #0002440
     *
     * @return null
     */
    public function testProcessAddress()
    {
        $aInData = array(
            "oxorder__oxid"            => "7d090db46a124f48cb7e6836ceef3f66",
            "oxorder__oxbillsal"       => "MR",
            "oxorder__oxbillfname"     => "Marc",
            "oxorder__oxbilllname"     => "Muster",
            "oxorder__oxbillemail"     => "user@oxid-esales.com",
            "oxorder__oxbillcompany"   => "",
            "oxorder__oxbillstreet"    => "Hauptstr.",
            "oxorder__oxbillstreetnr"  => "13",
            "oxorder__oxbillzip"       => "79098",
            "oxorder__oxbillcity"      => "Freiburg",
            "oxorder__oxbillustid"     => "",
            "oxorder__oxbilladdinfo"   => "",
            "oxorder__oxbillstateid"   => "",
            "oxorder__oxbillcountryid" => "a7c40f631fc920687.20179984",
            "oxorder__oxbillfon"       => "",
            "oxorder__oxbillfax"       => "",
            "oxorder__oxdelsal"        => "MR",
            "oxorder__oxdelfname"      => "",
            "oxorder__oxdellname"      => "",
            "oxorder__oxdelcompany"    => "",
            "oxorder__oxdelstreet"     => "",
            "oxorder__oxdelstreetnr"   => "",
            "oxorder__oxdelzip"        => "",
            "oxorder__oxdelcity"       => "",
            "oxorder__oxdeladdinfo"    => "",
            "oxorder__oxdelstateid"    => "",
            "oxorder__oxdelcountryid"  => "",
            "oxorder__oxdelfon"        => "",
            "oxorder__oxdelfax"        => ""
        );

        $aOutData = $aInData;
        $aOutData["oxorder__oxdelsal"] = "";

        $oView = oxNew('Order_Address');
        $aInData = $oView->UNITprocessAddress($aInData, "oxorder__oxdel", array("oxorder__oxdelsal"));

        $this->assertEquals($aOutData, $aInData);
    }
}
