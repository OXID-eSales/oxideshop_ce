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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Tests for Shop_Main class
 */
class Unit_Admin_ShopRDFaTest extends OxidTestCase
{

    /**
     * Shop_RDFa::getContentList() test case
     *
     * @return null
     */
    public function testGetContentList()
    {
        modConfig::setRequestParameter("oxid", oxRegistry::getConfig()->getShopId());

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

        $oConf = modConfig::getInstance();
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
        $oConf = modConfig::getInstance();
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
        modConfig::setRequestParameter('aSubmitUrl', array("url" => "http://www.myshop.com", "email" => "test@email"));
        $aHeaders = array(2 => "Return: True", 3 => "Return message: Success");
        $oView = $this->getMock('Shop_RDFa', array("getHttpResponseCode"));
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
        modConfig::setRequestParameter('aSubmitUrl', null);
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
        modConfig::setRequestParameter('aSubmitUrl', array("url" => "http://www.myshop.com"));
        $aHeaders = array(2 => "Return: False", 3 => "Return message: To many times submited");
        $oView = $this->getMock('Shop_RDFa', array("getHttpResponseCode"));
        $oView->expects($this->any())->method('getHttpResponseCode')->will($this->returnValue($aHeaders));
        $oView->submitUrl();
        $aErr = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertEquals('To many times submited', $oErr->getOxMessage());
    }

}
