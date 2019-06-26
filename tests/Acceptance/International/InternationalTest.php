<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\International;

use OxidEsales\EshopCommunity\Core\Edition\EditionPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionRootPathProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\EshopCommunity\Tests\Acceptance\AcceptanceTestCase;

/** Selenium tests for UTF-8 shop version. */
class InternationalTest extends AcceptanceTestCase
{
    /** @var string Language id. In international edition English ID is 0. */
    protected $translateLanguageId = 0;

    public function setUp()
    {
        $this->markTestSkipped('This functionality was removed and should be cleaned up with the story OXDEV-1595');
        parent::setUp();
    }
    /* -------------------------- Admin side only functions ------------------------ */

    /**
     * Login to admin with default admin pass and opens needed menu.
     * Internal tests use only one-main shop in most cases.
     *
     * @param string $menuLink1     Menu link (e.g. master settings, shop settings).
     * @param string $menuLink2     Sub menu link (e.g. administer products, discounts, vat).
     * @param bool   $forceMainShop Force main shop.
     * @param string $user          Shop admin username.
     * @param string $pass          Shop admin password.
     * @param string $language      Shop admin language.
     */
    public function loginAdmin(
        $menuLink1 = null,
        $menuLink2 = null,
        $forceMainShop = true,
        $user = "admin@myoxideshop.com",
        $pass = "admin0303",
        $language = "English"
    ) {
        parent::loginAdmin($menuLink1, $menuLink2, $forceMainShop, $user, $pass, $language);
    }

    /**
     * simple user account opening
     * @group international
     */
    public function testStandardUserRegistrationInternational()
    {
        //creating user
        $this->openShop();
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='Register']");
        $this->type("userLoginName", "example01@oxid-esales.dev");
        $this->type("userPassword", "user11");
        $this->type("userPasswordConfirm", "user11");
        $this->assertEquals("off", $this->getValue("//input[@name='blnewssubscribed' and @value='1']"));
        $this->type("invadr[oxuser__oxfname]", "user1 name_šųößлы");
        $this->type("invadr[oxuser__oxlname]", "user1 last name_šųößлы");
        $this->type("invadr[oxuser__oxcompany]", "user1 company_šųößлы");
        $this->type("invadr[oxuser__oxstreet]", "user1 street_šųößлы");
        $this->type("invadr[oxuser__oxstreetnr]", "1");
        $this->type("invadr[oxuser__oxzip]", "11");
        $this->type("invadr[oxuser__oxcity]", "user1 city_šųößлы");
        $this->type("invadr[oxuser__oxustid]", "");
        $this->type("invadr[oxuser__oxaddinfo]", "user1 additional info_šųößлы");
        $this->select("invadr[oxuser__oxcountryid]", "label=Germany");
        $this->type("invadr[oxuser__oxfon]", "111-111");
        $this->type("invadr[oxuser__oxfax]", "111-111-111");
        $this->type("invadr[oxuser__oxmobfon]", "111-111111");
        $this->type("invadr[oxuser__oxprivfon]", "111111111");
        $this->type("invadr[oxuser__oxbirthdate][day]", "11");
        $this->type("invadr[oxuser__oxbirthdate][month]", "11");
        $this->type("invadr[oxuser__oxbirthdate][year]", "1981");
        $this->clickAndWait("accUserSaveTop");
        $this->assertTextPresent("%PAGE_TITLE_REGISTER%");
        $this->assertEquals("user1 name_šųößлы user1 last name_šųößлы", $this->getText("//ul[@id='topMenu']/li/a"));
        $this->assertEquals("You are here: / Register", $this->getText("breadCrumb"));

        $this->loginAdmin("Administer Users", "Users");
        $this->type("where[oxuser][oxlname]", "user1");
        $this->clickAndWait("submitit");
        $this->assertEquals("user1 last name_šųößлы user1 name_šųößлы", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->openListItem("link=user1 last name_šųößлы user1 name_šųößлы");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("user1 name_šųößлы", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("user1 last name_šųößлы", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("user1 company_šųößлы", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("user1 street_šųößлы", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("user1 city_šųößлы", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("user1 additional info_šųößлы", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("111-111", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("111-111-111", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1981", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertTextPresent("Yes");
        $this->frame("list");
        $this->openTab("Extended");
        $this->assertEquals("111111111", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("111-111111", $this->getValue("editval[oxuser__oxmobfon]"));
    }

    /**
     * Search in frontend and Top Menu Navigation
     * @group international
     */
    public function testFrontendSearchInternational()
    {
        $this->openShop();
        //searching for 1 product (using product search field value)
        $this->searchFor("šųößлы1000");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertElementPresent("rssSearchProducts");
        $this->assertEquals("1 Hits for \"šųößлы1000\"", $this->getHeadingText("//h1"));
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("Test product 0 short desc [EN] šųößлы", $this->clearString($this->getText("//ul[@id='searchList']/li[1]//div[2]/div[2]")));

        //checking if all product links in relusts are working

        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("You are here: / Search result for \"šųößлы1000\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šųößлы", $this->getText("//h1"));

        //special chars search
        $this->searchFor("[EN] šųößлы");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertEquals("4 Hits for \"[EN] šųößлы\"", $this->getHeadingText("//h1"));
    }


    /**
     * My Account navigation: changing password
     * @group international
     */
    public function testFrontendMyAccountPassInternational()
    {
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->clickAndWait("//ul[@id='topMenu']/li/a");
        //changing password
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% - example_test@oxid-esales.dev", $this->getText("breadCrumb"));
        $this->assertEquals("UserNameįÄк UserSurnameįÄк", $this->clearString($this->getText("//ul[@id='topMenu']/li/a")));
        $this->clickAndWait("//aside[@id='sidebar']//li/a[text()='%CHANGE_PASSWORD%']");
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_PASSWORD%", $this->getText("//h1"));

        //entered diff new passwords
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user1user");
        $this->type("password_new_confirm", "useruser");
        $this->click("savePass");
        $this->waitForItemAppear('//section[@id="content"]//li[3]//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]');

        //new pass is too short
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user");
        $this->type("password_new_confirm", "user");
        $this->click("savePass");
        $this->waitForItemAppear("//section[@id='content']//li[3]//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']");

        //correct new pass
        $this->type("passwordOld", "useruser");
        $this->type("password_new", "user1userįÄк");
        $this->type("password_new_confirm", "user1userįÄк");
        $this->clickAndWait("savePass");
        $this->assertFalse($this->isVisible("//span[text()='%ERROR_MESSAGE_PASSWORD_TOO_SHORT%']"));
        $this->assertFalse($this->isVisible('//span[text()="%ERROR_MESSAGE_PASSWORD_DO_NOT_MATCH%"]'));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_PASSWORD%", $this->getText("//h1"));
        $this->assertTextPresent("%MESSAGE_PASSWORD_CHANGED%");
        $this->clickAndWait("//ul[@id='topMenu']//a[text()='%LOGOUT%']");

        $this->assertTextNotPresent("%ERROR_MESSAGE_USER_NOVALIDLOGIN%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser", false);
        $this->assertTextPresent("%ERROR_MESSAGE_USER_NOVALIDLOGIN%");
        $this->loginInFrontend("example_test@oxid-esales.dev", "user1userįÄк");
        $this->assertEquals("UserNameįÄк UserSurnameįÄк", $this->clearString($this->getText("//ul[@id='topMenu']/li[1]/a[1]")));
    }

    /**
     * Order steps: Step1
     * @group international
     */
    public function testFrontendOrderStep1International()
    {
        $this->openShop();
        //adding products to the basket
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar3 [EN] įÄк -2,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šųößлы", "var2 [EN] šųößлы");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->type("amountToBasket", "6");
        $this->clickAndWait("toBasket");
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//button");

        $this->openBasket();
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        $this->assertTextPresent("Your Coupon \"222222\" couldn't be accepted. Reason: The coupon is not valid for your user group! ");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->type("voucherNr", "111111");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        //Order Step1
        $this->assertEquals("Test product 1 [EN] šųößлы", $this->getText("//tr[@id='cartItem_1']/td[3]//a"));
        $this->assertEquals("Test product 2 [EN] šųößлы, var2 [EN] šųößлы", $this->getText("//tr[@id='cartItem_2']/td[3]//a"));
        $this->assertEquals("Test product 3 [EN] šųößлы", $this->getText("//tr[@id='cartItem_3']/td[3]//a"));
        $this->assertEquals("Test product 0 [EN] šųößлы", $this->getText("//tr[@id='cartItem_4']/td[3]//a"));
        $this->assertEquals("%PRODUCT_NO%: 1001", $this->getText("//tr[@id='cartItem_1']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1002-2", $this->getText("//tr[@id='cartItem_2']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1003", $this->getText("//tr[@id='cartItem_3']/td[3]/div[2]"));
        $this->assertEquals("%PRODUCT_NO%: 1000", $this->getText("//tr[@id='cartItem_4']/td[3]/div[2]"));
        //testing product with selection list
        $this->assertEquals("selvar3 [EN] įÄк -2,00 €", $this->getText("//div[@id='cartItemSelections_1']//span"));
        $this->assertEquals("93,00 € \n98,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("93,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar2 [EN] įÄк");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("95,00 € \n100,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("95,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar4 [EN] įÄк +2%");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("97,00 € \n102,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("97,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        $this->selectVariant("cartItemSelections_1", 1, "selvar1 [EN] įÄк +1,00 €");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->clickAndWait("basketUpdate");
        $this->assertEquals("96,00 € \n101,00 €", $this->getText("//tr[@id='cartItem_1']/td[6]"), "price with discount not shown in basket");
        $this->assertEquals("10%", $this->getText("//tr[@id='cartItem_1']/td[7]"));
        $this->assertEquals("96,00 €", $this->getText("//tr[@id='cartItem_1']/td[8]"));
        //testing product with staffelpreis
        if (!isSUBSHOP) { //staffelpreis is not inherited to subshop, so it is tested only in main shop
            $this->assertEquals("54,00 € \n60,00 €", $this->getText("//tr[@id='cartItem_3']/td[6]"), "price with discount not shown in basket");
            $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
            $this->assertEquals("324,00 €", $this->getText("//tr[@id='cartItem_3']/td[8]"));
            $this->assertEquals("6", $this->getValue("am_3"));
            $this->type("am_3", "1");
            $this->clickAndWait("basketUpdate");
            $this->assertEquals("67,50 € \n75,00 €", $this->getText("//tr[@id='cartItem_3']/td[6]"), "price with discount not shown in basket");
            $this->assertEquals("19%", $this->getText("//tr[@id='cartItem_3']/td[7]"));
            $this->assertEquals("67,50 €", $this->getText("//tr[@id='cartItem_3']/td[8]"));
            $this->type("am_3", "6");
            $this->clickAndWait("basketUpdate");
        }

        //discounts
        $this->assertEquals("%COUPON% (%NUMBER_2% 111111) %REMOVE%", $this->getText("//div[@id='basketSummary']//tr[2]/th"));
        // delivery
        $this->assertEquals("1,50 €", $this->getText("//div[@id='basketSummary']//tr[7]/td"));
        if (!isSUBSHOP) { //calculation for main shop
            $this->assertEquals("444,45 €", $this->getText("basketTotalNetto"), "Neto price changed or did't displayed");
            $this->assertEquals("8,56 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 10% changed ");
            $this->assertEquals("60,19 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"), "VAT 19% changed ");
            $this->assertEquals("525,30 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or did't displayed");
            $this->assertEquals("1,50 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or did't displayed");
            $this->assertEquals("516,80 €", $this->getText("basketGrandTotal"), "Garnd total price chenged or did't displayed");
        } else { //calculation for subshop
            $this->assertEquals("512,54 €", $this->getText("basketTotalNetto"), "Neto price changed or did't displayed");
            $this->assertEquals("8,58 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 10% changed ");
            $this->assertEquals("73,07 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"), "VAT 19% changed ");
            $this->assertEquals("2,11 €", $this->getText("//div[@id='basketSummary']//tr[6]/td"), "VAT 5% changed ");
            $this->assertEquals("606,30 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or did't displayed");
            $this->assertEquals("1,50 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or did't displayed");
            $this->assertEquals("597,80 €", $this->getText("basketGrandTotal"), "Garnd total price chenged or did't displayed");
        }

        $this->clickAndWait("//div[@id='basketSummary']//tr[2]/th/a");
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");

        //removing few articles
        $this->check("//tr[@id='cartItem_4']/td[1]//input");
        $this->check("//tr[@id='cartItem_3']/td[1]//input");
        $this->clickAndWait("basketRemove");

        //basket calculation
        $this->assertEquals("136,40 €", $this->getText("basketTotalNetto"), "Neto price changed or did't displayed");
        $this->assertEquals("8,29 €", $this->getText("//div[@id='basketSummary']//tr[4]/td"), "VAT 10% changed ");
        $this->assertEquals("10,16 €", $this->getText("//div[@id='basketSummary']//tr[5]/td"), "VAT 19% changed ");
        $this->assertEquals("163,00 €", $this->getText("basketTotalProductsGross"), "Bruto price changed  or did't displayed");
        $this->assertEquals("1,50 €", $this->getText("basketDeliveryGross"), "Shipping price changed  or did't displayed");
        $this->assertEquals("156,35 €", $this->getText("basketGrandTotal"), "Garnd total price chenged or did't displayed");
        $this->assertEquals("-8,15 €", $this->clearString($this->getText("//div[@id='basketSummary']//tr[2]/td")));
    }

    /**
     * Order steps (without any special checking for discounts, various VATs and user registration)
     * @group international
     */
    public function testFrontendOrderSteps4And5International()
    {
        $this->openShop();
        $this->searchFor("100");
        $this->selectVariant("selectlistsselector_searchList_2", 1, "selvar1 [EN] įÄк +1,00 €");
        $this->clickAndWait("toBasket");
        $this->clickAndWait("linkNextArticle");
        $this->selectVariant("variants", 1, "var2 [EN] šųößлы", "var2 [EN] šųößлы");
        $this->clickAndWait("toBasket");

        $this->openBasket();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->type("voucherNr", "222222");
        $this->clickAndWait("//button[text()='%SUBMIT_COUPON%']");
        $this->_continueToNextStep(2);
        $this->assertEquals("Test S&H set [EN] šųößлы", $this->getSelectedLabel("sShipSet"));
        $this->selectAndWait("sShipSet", "label=Standard");
        $this->_continueToNextStep();
        //Order Step4
        //rights of withdrawal
        $this->assertElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Terms and Conditions']");
        $this->assertElementPresent("//form[@id='orderConfirmAgbTop']//a[text()='Right of Withdrawal']");
        //testing links to products
        $this->clickAndWait("//tr[@id='cartItem_1']/td/a");
        $this->assertEquals("Test product 1 [EN] šųößлы", $this->getText("//h1"));
        $this->openBasket();
        $this->_continueToNextStep(3);

        $this->clickAndWait("//tr[@id='cartItem_2']/td[2]//a");
        $this->assertEquals("Test product 2 [EN] šųößлы var2 [EN] šųößлы", $this->getText("//h1"));
        $this->openBasket();
        $this->_continueToNextStep(3);
        //submit without checkbox
        $this->click("//form[@id='orderConfirmAgbTop']//button");
        $this->waitForText("%READ_AND_CONFIRM_TERMS%");
        //successful submit
        $this->check("//form[@id='orderConfirmAgbTop']//input[@name='ord_agb' and @value='1']");
        $this->clickAndWait("//form[@id='orderConfirmAgbTop']//button");
        //testing info in 5th page
        $this->assertEquals("%YOU_ARE_HERE%: / %ORDER_COMPLETED%", $this->getText("breadCrumb"));
        $this->assertTextPresent("We registered your order with number 12");
        $this->assertElementPresent("backToShop");
        $this->assertEquals("%BACK_TO_START_PAGE%", $this->getText("backToShop"));
        $this->clickAndWait("orderHistory");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %ORDER_HISTORY%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_ORDER%", $this->getText("//h1"));
        $this->assertEquals("Test product 1 [EN] šųößлы test selection list [EN] šųößлы : selvar1 [EN] įÄк +1,00 € - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_1']/td")));
        $this->assertEquals("Test product 2 [EN] šųößлы var2 [EN] šųößлы - 1 qty.", $this->clearString($this->getText("//tr[@id='accOrderAmount_12_2']/td")));
    }

    /**
     * Checking Top Menu Navigation
     * @group international
     */
    public function testFrontendTopMenuInternational()
    {
        $this->openShop();
        $this->assertTrue($this->isVisible("navigation"));
        $this->assertEquals("Home", $this->clearString($this->getText("//ul[@id='navigation']/li[1]")));
        $this->assertEquals("Test category 0 [EN] šųößлы »", $this->clearString($this->getText("//ul[@id='navigation']/li[3]/a")));
        $this->assertElementNotPresent("//ul[@id='tree']/li");
        $this->clickAndWait("//ul[@id='navigation']/li[3]/a");

        $this->assertEquals("Test category 0 [EN] šųößлы", $this->getHeadingText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šųößлы", $this->getText("breadCrumb"));
        $this->assertElementPresent("//ul[@id='tree']/li");
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->selectDropDown("viewOptions", "Line");

        $this->clickAndWait("//ul[@id='tree']/li/ul/li/a");
        $this->assertElementPresent("//ul[@id='tree']/li");
        $this->assertEquals("Test category 0 [EN] šųößлы", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getHeadingText("//h1"));
        $this->assertEquals("Test category 1 [EN] šųößлы", $this->getHeadingText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šųößлы / Test category 1 [EN] šųößлы", $this->getText("breadCrumb"));
    }

    /**
     * Shop migration from International to Germany locale (#1707 from Mantis)
     * @group international
     */
    public function testMigrationInternationalToGermany()
    {
        if (!isSUBSHOP) {
            $this->clearCache();
            $this->loginAdmin("Master Settings", "Core Settings");
            $this->frame("navigation");
            $this->assertElementNotPresent("link=E-Commerce Services");
            $this->assertElementNotPresent("link=Shop connector");
            $this->assertElementPresent("link=History");
            $this->waitForElement("link=Master Settings");
            $this->checkForErrors();
            $this->click("link=Master Settings");
            $this->clickAndWaitFrame("link=Core Settings", "edit");
            //testing edit frame for errors
            $this->frame("edit");
            //testing list frame for errors
            $this->frame("list");
            $this->openTab("Settings");
            $this->click("link=Administration");

            if ($this->getTestConfig()->getShopEdition() === 'CE') {
                $this->check("//input[@name='confbools[blSendTechnicalInformationToOxid]' and @value='true']");
            }

            $this->clickAndWaitFrame("save", "list");
            $this->frame("header");
            $this->assertElementPresent("link=Logout");
            $this->logoutAdmin("link=Logout");

            $this->type("user", "admin@myoxideshop.com");
            $this->type("pwd", "admin0303");
            $this->select("chlanguage", "label=English");
            $this->select("profile", "label=Standard");
            $this->clickAndWait("//input[@type='submit']");

            $this->frame("basefrm");
            $this->waitForText("Welcome to the OXID eShop Admin");
            $this->checkForErrors();
            $this->frame("navigation");
            $this->checkForErrors();
            $this->assertElementPresent("link=Master Settings");
            $this->assertElementPresent("link=Shop Settings");
            //checking if there are no errors in frontend
            $this->openShop();
        }
    }

    /**
     * Clicks Continue to Next Step given amount of times.
     *
     * @param int $iSteps
     */
    private function _continueToNextStep($iSteps = 1)
    {
        for ($i=1; $i <= $iSteps; $i++) {
            $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");
        }
    }
}
