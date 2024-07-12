<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
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
        $aCustomers = ["Enduser"           => 1, "Reseller"          => 1, "Business"          => 0, "PublicInstitution" => 1];

        $oConf = $this->getConfig();
        $oConf->setConfigParam('aRDFaCustomers', ['Enduser', 'Reseller', 'PublicInstitution']);

        $oView = $this->getProxyClass('Shop_RDFA');
        Registry::set(Config::class, $oConf);
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
        Registry::set(Config::class, $oConf);
        $this->assertEquals([], $oView->getCustomers());
    }
}
