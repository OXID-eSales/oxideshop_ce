<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link http://www.oxid-esales.com
 * @package tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */


require_once 'acceptance/oxidAdditionalSeleniumFunctions.php';

class AcceptanceEfire_webmilesTestBasic extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

    // ------------------------ eFire modules for eShop ----------------------------------


    /**
     * webmiles in frontend
     * @group webmiles
     */
    public function testWebmiles()
    {
        //testing search
        $this->openShop();
        $this->clickAndWait("test_Lang_Deutsch");
        $this->assertTrue($this->isTextPresent("Sie sammeln"), "Webmiles are not setupped correctly!");
        $this->type("searchparam", "1000");
        $this->clickAndWait("test_searchGo");
        $this->assertEquals("Sie sind hier: / Suche", $this->getText("path"));
        $this->assertEquals("Sie sammeln 1", $this->clearString($this->getText("//div[@id='test_price_Search_1000']/div")));
        $this->type("test_am_Search_1000", "10");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->assertTrue($this->isElementPresent("//td[@id='webmiles_minibasket_youCollectValue_RightBasket']"));
        $this->assertEquals("13", $this->getText("//td[@id='webmiles_minibasket_youCollectValue_RightBasket']"));
        $this->clickAndWait("test_RightBasketOpen");

        $this->assertEquals("Sie können 13 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));

        $this->assertFalse($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));
        $this->type("test_basketAm_1000_1", "1");
        $this->clickAndWait("test_basketUpdate");
        $this->assertTrue($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));

        $this->assertEquals("Sie können 3 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));

        $this->executeSql("DELETE FROM `efi_webmiles` WHERE `efifield` = 'campaign_bonus'");
        $this->clickAndWait("test_basketUpdate");
        $this->assertTrue($this->isTextPresent("Noch für 9,00 € einkaufen und 1 extra sammeln!"));

        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));

        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertTrue($this->isTextPresent("Als Neukunde sammeln Sie 1 extra!"));
        $this->clickAndWait("test_UsrOpt3");
        $this->type("userLoginName", "birute@nfq.lt");
        $this->type("userPassword", "111111");
        $this->type("userPasswordConfirm", "111111");
        $this->type("invadr[oxuser__oxfname]", "Testing account");
        $this->type("invadr[oxuser__oxlname]", "Webmiles");
        $this->type("invadr[oxuser__oxstreet]", "Musterstr.");
        $this->type("invadr[oxuser__oxstreetnr]", "10");
        $this->type("invadr[oxuser__oxzip]", "79098");
        $this->type("invadr[oxuser__oxcity]", "Musterstadt");
        $this->select("invadr[oxuser__oxcountryid]", "label=Deutschland");
        $this->type("order_remark", "Testing webmiles in eShop");
        $this->uncheck("test_newsReg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1_Text");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->assertTrue($this->isElementPresent("invadr[oxuser__oxfname]"), "User entered data is gone!");
        $this->assertEquals("Testing account",$this->getValue("invadr[oxuser__oxfname]"), "User entered data is gone!");
        $this->check("test_newsReg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1_Text");
        //$this->assertEquals("Sie können 3 sammeln.", $this->clearString($this->getText("//div[@id='body']/form/table/tbody/tr[9]/td[2]")));
        $this->assertEquals("Sie können 3 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->uncheck("test_newsReg");
        $this->clickAndWait("test_UserNextStepBottom");
        $this->clickAndWait("test_Step1_Text");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));
        $this->clickAndWait("test_BasketNextStepTop");
        $this->clickAndWait("test_UserNextStepTop");
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->click("test_Payment_oxidpayadvance");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->type("username", "stewerner");
        $this->type("password", "oxid123support");
        $this->clickAndWait("//input[@name='Button' and @value='']");
        $this->assertTrue($this->isTextPresent("Sie sammeln mit Ihrem webmiles Konto \"stewerner\" 2 webmiles."));
        $this->assertEquals("2", $this->clearString($this->getText("webmiles_wbYouCollectValue")));
        $this->clickAndWait("test_orderChangePayment");
        $this->click("test_Payment_oxidinvoice");
        $this->clickAndWait("test_PaymentNextStepBottom");
        $this->assertEquals("2", $this->getText("//div[@id='body']/table/tbody/tr[9]/td[3]"));
        $this->assertEquals("4,90 €", $this->getText("test_orderGrandTotal"));
        $this->clickAndWait("test_OrderSubmitBottom");
        $this->assertTrue($this->isTextPresent("Vielen Dank für Ihre Bestellung im OXID eShop"));
        $this->assertTrue($this->isTextPresent("Sie haben mit Ihrer Bestellung 2 gesammelt."));
        $this->assertTrue($this->isTextPresent("wertvolle webmiles sammeln"));
        $this->assertTrue($this->isTextPresent("und in tolle Prämien einlösen!"));
        $this->clickAndWait("test_HeaderHome");
        $this->type("f.search.param", "1000");
        $this->clickAndWait("test_searchGo");
        $this->clickAndWait("test_toBasket_Search_1000");
        $this->clickAndWait("test_RightBasketOpen");
        $this->assertEquals("11", $this->getText("webmiles_wbYouCollectValue"));
        $this->clickAndWait("test_RightLogout");
        $this->assertEquals("Sie können 2 sammeln.", $this->clearString($this->getText("webmiles_wbYouClouldCollect")));

        //testing in admin, if order is saved correctly
        $this->loginAdmin("Administer Orders", "Orders");
        $this->openTab("link=2");
     //   $this->assertEquals("Order No.: 1 In Folder: New Finished Problems Internal Status: OK Overview: Number Orders Today: 1 Sum Revenue Today: 4,90 EUR Number Order TOTAL: 1 Sum Revenue TOTAL: 4,90 EUR Send e-mail? Shipped on -", $this->clearString($this->getText("//div[2]/table/tbody/tr/td[3]")));
        $this->frame("list");
        $this->assertTrue($this->isElementPresent("del.1"));
        $this->assertTrue($this->isElementPresent("where[oxorder][oxordernr]"));
        $this->clickAndConfirm("del.1");
        $this->assertTrue($this->isElementPresent("where[oxorder][oxordernr]"));
        $this->assertFalse($this->isElementPresent("del.1"));
    }

}
