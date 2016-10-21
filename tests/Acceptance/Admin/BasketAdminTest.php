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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Tests related creating of orders in frontend. */
class BasketAdminTest extends AdminTestCase
{
    /**
     * PersParam functionality in frontend
     * PersParam functionality in admin
     * testing option 'Product can be customized' from Administer products -> Extend tab
     *
     * @group main
     */
    public function testFrontendPersParamSaveBasket()
    {
        // Active option (Product can be customized) for product with ID 1000
        $this->_saveArticle("1000", array("oxisconfigurable" => 1));

        // Active config option (Don't save Shopping Carts of registered Users)
        $this->_setShopParam("blPerfNoBasketSaving", '');

        $aOrderParams1 = $this->_getNewTestOrderParams();

        $sOrderId = $this->callShopSC("oxOrder", "save", null, $aOrderParams1);

        $aOrderArticleParams1 = $this->_getOrderArticle1($sOrderId);
        $aOrderArticleParams2 = $this->_getOrderArticle2($sOrderId);
        $aOrderArticleParams3 = $this->_getOrderArticle3($sOrderId);
        $aOrderArticleParams4 = $this->_getOrderArticle4($sOrderId);

        $this->callShopSC("oxOrderArticle", "save", null, $aOrderArticleParams1);
        $this->callShopSC("oxOrderArticle", "save", null, $aOrderArticleParams2);
        $this->callShopSC("oxOrderArticle", "save", null, $aOrderArticleParams3);
        $this->callShopSC("oxOrderArticle", "save", null, $aOrderArticleParams4);

        //checking in Admin
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openListItem("link=12");
        $this->assertTextPresent("Label: test label šÄßüл 1");

        $firstArticle  = ['2 *', '1000', 'Test product 0 [EN]', '', '90,00 EUR'];
        $secondArticle = ['2 *', '1000', 'Test product 0 [EN]', '', '90,00 EUR'];
        $thirdArticle  = ['1 *', '1001', 'Test product 1 [EN]', 'test selection list [EN] šÄßüл : selvar3 [EN] šÄßüл -2,00 €', '93,00 EUR'];
        $fourthArticle = ['1 *', '1001', 'Test product 1 [EN]', 'test selection list [EN] šÄßüл : selvar4 [EN] šÄßüл +2%', '97,00 EUR'];

        $matrix = [];
        $counter = null;
        for ($i=1;$i<5; $i++) {
            for ($j=1;$j<=6; $j++) {
                $identifier = "//table[2]/tbody/tr[$i]/td[$j]";
                if ($this->isElementPresent($identifier)) {
                    $matrix[$i-1][$j-1] = $this->getText($identifier);
                    if (6 == $j) {
                        $counter = $i;
                    }
                }
            }
        }
        $this->assertTrue(in_array($firstArticle, $matrix));
        $this->assertTrue(in_array($secondArticle, $matrix));
        $this->assertTrue(in_array($thirdArticle, $matrix));
        $this->assertTrue(in_array($fourthArticle, $matrix));

        $this->openTab("Products");
        $this->assertEquals("2", $this->getValue("//tr[@id='art.{$counter}']/td[1]/input"));
        $this->assertEquals("Label: test label šÄßüл 1", $this->getText("//tr[@id='art.{$counter}']/td[5]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.{$counter}']/td[7]"));
        $this->assertEquals("90,00 EUR", $this->getText("//tr[@id='art.{$counter}']/td[8]"));
        $this->type("//tr[@id='art.{$counter}']/td[1]/input", "1");
        $this->clickAndWait("//input[@value='Update']");
        $this->assertEquals("Label: test label šÄßüл 1", $this->getText("//tr[@id='art.{$counter}']/td[5]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.{$counter}']/td[7]"));
        $this->assertEquals("45,00 EUR", $this->getText("//tr[@id='art.{$counter}']/td[8]"));

        //After recalculation fix sum total should be:
        $this->assertTextPresent('336,42');
    }

    /**
     * @param string $sArticleId
     * @param array  $aArticleParams
     * @param null   $iShopId
     */
    protected function _saveArticle($sArticleId, $aArticleParams, $iShopId = null)
    {
        $this->callShopSC("oxArticle", "save", $sArticleId, $aArticleParams, null, $iShopId);
    }

    /**
     * @param string $sParamName
     * @param string $sParamValue
     * @param null $sModule  optional
     */
    protected function _setShopParam($sParamName, $sParamValue, $sModule = null)
    {
        $aParams = array("type" => "bool", "value" => $sParamValue);

        if (!is_null($sModule)){
            $aParams = array_merge($aParams, array("module" => $sModule));
        }

        $this->callShopSC("oxConfig", null, null, array($sParamName => $aParams));
    }

    /**
     * @return array
     */
    protected function _getNewTestOrderParams()
    {
        $aOrderParams1 = array(
            'OXID' => 'e2a96db880623b02ff69617de634ba5f',
            'OXSHOPID' => 1,
            'OXUSERID' => 'testuser',
            'OXORDERDATE' => '2014-03-07 08:27:13',
            'OXORDERNR' => 12,
            'OXBILLCOMPANY' => 'UserCompany šÄßüл',
            'OXBILLEMAIL' => 'example_test@oxid-esales.dev',
            'OXBILLFNAME' => 'UserNamešÄßüл',
            'OXBILLLNAME' => 'UserSurnamešÄßüл',
            'OXBILLSTREET' => 'Musterstr.šÄßüл',
            'OXBILLSTREETNR' => '1',
            'OXBILLADDINFO' => 'User additional info šÄßüл',
            'OXBILLUSTIDSTATUS' => 1,
            'OXBILLCITY' => 'Musterstadt šÄßüл',
            'OXBILLCOUNTRYID' => 'a7c40f631fc920687.20179984',
            'OXBILLZIP' => '79098',
            'OXBILLFON' => '0800 111111',
            'OXBILLFAX' => '0800 111112',
            'OXBILLSAL' => 'MR',
            'OXPAYMENTID' => 'c0f454b6af0db05c839bf186a03709da',
            'OXPAYMENTTYPE' => 'oxidcashondel',
            'OXTOTALNETSUM' => 344.16,
            'OXTOTALBRUTSUM' => 370,
            'OXTOTALORDERSUM' => 379.4,
            'OXARTVAT1' => 5,
            'OXARTVATPRICE1' => 8.57,
            'OXARTVAT2' => 10,
            'OXARTVATPRICE2' => 17.27,
            'OXDELCOST' => 1.9,
            'OXDELVAT' => 10,
            'OXPAYCOST' => 7.5,
            'OXPAYVAT' => 10,
            'OXVOUCHERDISCOUNT' => 0,
            'OXCURRENCY' => 'EUR',
            'OXCURRATE' => 1,
            'OXFOLDER' => 'ORDERFOLDER_NEW',
            'OXTRANSSTATUS' => 'OK',
            'OXLANG' => 1,
            'OXDELTYPE' => 'testdelset',
            'OXTIMESTAMP' => '2014-03-07 09:27:13',
            'OXISNETTOMODE' => '0',
        );

        return $aOrderParams1;
    }

    protected function _getOrderArticle1($sOrderId)
    {
        return array(
            'OXID' => '4caac47d5f4a819c0853dd5d3b90287e',
            'OXORDERID' => $sOrderId,
            'OXAMOUNT' => '2',
            'OXARTID' => '1000',
            'OXARTNUM' => '1000',
            'OXTITLE' => 'Test product 0 [EN] šÄßüл',
            'OXSHORTDESC' => 'Test product 0 short desc [EN] šÄßüл',
            'OXSELVARIANT' => '',
            'OXNETPRICE' => '85.71',
            'OXBRUTPRICE' => '90',
            'OXVATPRICE' => '4.29',
            'OXVAT' => '5',
            'OXPERSPARAM' => '',
            'OXPRICE' => '50',
            'OXBPRICE' => '45',
            'OXNPRICE' => '42.86',
            'OXWEIGHT' => '2',
            'OXSTOCK' => '15',
            'OXDELIVERY' => '0000-00-00',
            'OXINSERT' => '2008-02-04',
            'OXTIMESTAMP' => '2014-03-07 09:27:12',
            'OXLENGTH' => '1',
            'OXWIDTH' => '2',
            'OXHEIGHT' => '2',
            'OXSEARCHKEYS' => 'šÄßüл1000',
            'OXISSEARCH' => '1',
            'OXSUBCLASS' => 'oxarticle',
            'OXSTORNO' => '0',
            'OXORDERSHOPID' => '1',
            'OXISBUNDLE' => '0',
        );
    }

    protected function _getOrderArticle2($sOrderId)
    {
        return array(
            'OXID' => '3b61dc80172cd600af584b5abb5a6d4a',
            'OXORDERID' => $sOrderId,
            'OXAMOUNT' => '2',
            'OXARTID' => '1000',
            'OXARTNUM' => '1000',
            'OXTITLE' => 'Test product 0 [EN] šÄßüл',
            'OXSHORTDESC' => 'Test product 0 short desc [EN] šÄßüл',
            'OXSELVARIANT' => '',
            'OXNETPRICE' => '85.71',
            'OXBRUTPRICE' => '90',
            'OXVATPRICE' => '4.29',
            'OXVAT' => '5',
            'OXPERSPARAM' => 'a:1:{s:7:"details";s:23:"test label šÄßüл 1";}',
            'OXPRICE' => '50',
            'OXBPRICE' => '45',
            'OXNPRICE' => '42.86',
            'OXWEIGHT' => '2',
            'OXSTOCK' => '15',
            'OXDELIVERY' => '0000-00-00',
            'OXINSERT' => '2008-02-04',
            'OXTIMESTAMP' => '2014-03-07 09:27:12',
            'OXLENGTH' => '1',
            'OXWIDTH' => '2',
            'OXHEIGHT' => '2',
            'OXSEARCHKEYS' => 'šÄßüл1000',
            'OXISSEARCH' => '1',
            'OXSUBCLASS' => 'oxarticle',
            'OXSTORNO' => '0',
            'OXORDERSHOPID' => '1',
            'OXISBUNDLE' => '0',
        );
    }

    protected function _getOrderArticle3($sOrderId)
    {
        return array(
            'OXID' => '1e5c3234fdc11195ddaf0face31c2998',
            'OXORDERID' => $sOrderId,
            'OXAMOUNT' => '1',
            'OXARTID' => '1001',
            'OXARTNUM' => '1001',
            'OXTITLE' => 'Test product 1 [EN] šÄßüл',
            'OXSHORTDESC' => 'Test product 1 short desc [EN] šÄßüл',
            'OXSELVARIANT' => 'test selection list [EN] šÄßüл : selvar3 [EN] šÄßüл -2,00 €',
            'OXNETPRICE' => '84.55',
            'OXBRUTPRICE' => '93',
            'OXVATPRICE' => '8.45',
            'OXVAT' => '10',
            'OXPERSPARAM' => '',
            'OXPRICE' => '100',
            'OXBPRICE' => '93',
            'OXNPRICE' => '84.55',
            'OXWEIGHT' => '0',
            'OXSTOCK' => '0',
            'OXDELIVERY' => '2008-01-01',
            'OXINSERT' => '2008-02-04',
            'OXTIMESTAMP' => '2014-03-07 09:27:12',
            'OXLENGTH' => '0',
            'OXWIDTH' => '0',
            'OXHEIGHT' => '0',
            'OXSEARCHKEYS' => 'šÄßüл1001',
            'OXISSEARCH' => '1',
            'OXSUBCLASS' => 'oxarticle',
            'OXSTORNO' => '0',
            'OXORDERSHOPID' => '1',
            'OXISBUNDLE' => '0',
        );
    }

    protected function _getOrderArticle4($sOrderId)
    {
        return array(
            'OXID' => '13453ad523bfcd0c1783fc225c534df1',
            'OXORDERID' => $sOrderId,
            'OXAMOUNT' => '1',
            'OXARTID' => '1001',
            'OXARTNUM' => '1001',
            'OXTITLE' => 'Test product 1 [EN] šÄßüл',
            'OXSHORTDESC' => 'Test product 1 short desc [EN] šÄßüл',
            'OXSELVARIANT' => 'test selection list [EN] šÄßüл : selvar4 [EN] šÄßüл +2%',
            'OXNETPRICE' => '88.18',
            'OXBRUTPRICE' => '97',
            'OXVATPRICE' => '8.82',
            'OXVAT' => '10',
            'OXPERSPARAM' => '',
            'OXPRICE' => '100',
            'OXBPRICE' => '97',
            'OXNPRICE' => '88.18',
            'OXWEIGHT' => '0',
            'OXSTOCK' => '0',
            'OXDELIVERY' => '2008-01-01',
            'OXINSERT' => '2008-02-04',
            'OXTIMESTAMP' => '2014-03-07 09:27:13',
            'OXLENGTH' => '0',
            'OXWIDTH' => '0',
            'OXHEIGHT' => '0',
            'OXSEARCHKEYS' => 'šÄßüл1001',
            'OXISSEARCH' => '1',
            'OXSUBCLASS' => 'oxarticle',
            'OXSTORNO' => '0',
            'OXORDERSHOPID' => '1',
            'OXISBUNDLE' => '0',
        );
    }
}
