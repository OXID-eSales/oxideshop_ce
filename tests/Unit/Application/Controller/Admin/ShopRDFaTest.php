<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxRegistry;

/**
 * Tests for Shop_Main class
 */
class ShopRDFaTest extends \OxidTestCase
{

    /**
     * Shop_RDFa::getContentList() test case
     *
     * @return null
     */
    public function testGetContentList()
    {
        $this->setRequestParameter("oxid", $this->getConfig()->getShopId());

        $oView = oxNew("Shop_RDFA");
        $this->assertEquals(4, $oView->getContentList()->count());
    }

    /**
     * Shop_RDFa::getCustomers() test case
     *
     * @return null
     */
    public function testGetCustomers()
    {
        $aCustomers = array("Enduser"           => 1,
                            "Reseller"          => 1,
                            "Business"          => 0,
                            "PublicInstitution" => 1);

        $oConf = $this->getConfig();
        $oConf->setConfigParam('aRDFaCustomers', array('Enduser', 'Reseller', 'PublicInstitution'));

        $oView = $this->getProxyClass('Shop_RDFA');
        $oView->setConfig($oConf);
        $this->assertEquals($aCustomers, $oView->getCustomers());
    }

    /**
     * Shop_RDFa::getCustomers() no params test case
     *
     * @return null
     */
    public function testGetCustomers_noparams()
    {
        $oConf = $this->getConfig();
        $oConf->setConfigParam('aRDFaCustomers', null);

        $oView = $this->getProxyClass('Shop_RDFA');
        $oView->setConfig($oConf);
        $this->assertEquals(array(), $oView->getCustomers());
    }

    /**
     * Shop_RDFa::submitUrl()
     *
     * @return null
     */
    public function testSubmitUrl()
    {
        $this->setRequestParameter('aSubmitUrl', array("url" => "http://www.myshop.com", "email" => "test@email"));
        $aHeaders = array(2 => "Return: True", 3 => "Return message: Success");
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::class, array("getHttpResponseCode"));
        $oView->expects($this->any())->method('getHttpResponseCode')->will($this->returnValue($aHeaders));
        $oView->submitUrl();
        $aViewData = $oView->getViewData();
        $this->assertEquals('SHOP_RDFA_SUBMITED_SUCCESSFULLY', $aViewData["submitMessage"]);
    }

    /**
     * Shop_RDFa::submitUrl()
     *
     * @return null
     */
    public function testSubmitUrlNoEntry()
    {
        $this->setRequestParameter('aSubmitUrl', null);
        $oView = $this->getProxyClass('Shop_RDFA');
        $oView->submitUrl();
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertNotNull($oErr->getOxMessage());
    }

    /**
     * Shop_RDFa::submitUrl()
     *
     * @return null
     */
    public function testSubmitUrlReturnFalse()
    {
        $this->setRequestParameter('aSubmitUrl', array("url" => "http://www.myshop.com"));
        $aHeaders = array(2 => "Return: False", 3 => "Return message: To many times submited");
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ShopRdfa::class, array("getHttpResponseCode"));
        $oView->expects($this->any())->method('getHttpResponseCode')->will($this->returnValue($aHeaders));
        $oView->submitUrl();
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('To many times submited', $oErr->getOxMessage());
    }
}
