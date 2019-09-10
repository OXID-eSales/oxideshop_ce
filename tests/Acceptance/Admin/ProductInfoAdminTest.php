<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/**
 * Class ProductInfoAdminTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance\Admin
 */
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
     * Test showing activation check if from/to date was set
     *
     * @group productAdmin
     */
    public function testActivationCheckIsShown()
    {
        $fromDate = strval(intval(date("Y")) - 2) . '-01-01 00:00:00';
        $toDate = strval(intval(date("Y")) + 2) . '-01-01 00:00:00';
        $query = "UPDATE `oxarticles` SET `oxactive` = 0, `oxactivefrom` = ?, `oxactiveto` = ? WHERE `oxartnum` = '1401'";
        DatabaseProvider::getDb()->execute($query, [$fromDate, $toDate]);

        Registry::getConfig()->saveShopConfVar("bool", "blUseTimeCheck", true);

        $this->loginAdmin("Administer Products", "Products");
        $this->type("where[oxarticles][oxartnum]", "1401");
        $this->clickAndWaitFrame("submitit", 'list');
        $this->openListItem("link=1401");

        $this->frame('list');
        $activationCheckElement = $this->getElementLazy($this->translate("//*[@id='row.1']/td[1]"));
        $this->assertTrue($activationCheckElement->hasClass('active'));
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
