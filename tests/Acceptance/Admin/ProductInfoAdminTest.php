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

class ProductInfoAdminTest extends AdminTestCase
{
    /**
     * Orders: buying more items than available
     *
     * @group productAdmin
     */
    public function testEuroSignInTitle()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->type("where[oxarticles][oxartnum]", "1002");
        $this->clickAndWaitFrame("submitit", 'list');
        $this->openListItem("link=1002");
        $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->type("editval[oxarticles__oxtitle]", "[DE 2] Test product 2 šÄßüл €");
        $this->clickAndWaitFrame("saveArticle", 'list');
        $this->assertEquals("[DE 2] Test product 2 šÄßüл €", $this->getValue("editval[oxarticles__oxtitle]"));
    }

    /**
     * Product details. Testing price alert
     *
     * @group productAdmin
     */
    public function testFrontendDetailsPriceAlert()
    {
        $aPriceAlarmData['oxemail'] = 'example_test@oxid-esales.dev';
        $aPriceAlarmData['oxprice'] = '99.99';
        $aPriceAlarmData['oxcurrency'] = 'EUR';
        $aPriceAlarmData['oxartid'] = '1001';
        $this->callShopSC('oxPriceAlarm', 'save', null, $aPriceAlarmData);

        $this->loginAdmin("Customer Info", "Price Alert");
        $this->type("where[oxpricealarm][oxemail]", "example_test@oxid-esales.dev");
        $this->clickAndWait("submitit");
        $this->assertEquals("example_test@oxid-esales.dev", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//tr[@id='row.1']/td[5]"));
        $this->assertEquals("99,99 EUR", $this->getText("//tr[@id='row.1']/td[6]"));
        $this->assertEquals("100,00 EUR", $this->getText("//tr[@id='row.1']/td[7]"));
    }

}

