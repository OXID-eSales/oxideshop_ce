<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Frontend;

use oxDb;
use OxidEsales\EshopCommunity\Tests\Acceptance\FrontendTestCase;

/** Selenium tests for new layout. */
class NavigationFrontendTest extends FrontendTestCase
{
    /**
     * Switching languages in frontend
     *
     * @group frontend
     */
    public function testFrontendLanguages()
    {
        $this->openShop();
        $this->assertElementPresent("//p[@id='languageTrigger']//*[text()='English']");
        $this->assertFalse($this->isVisible("languages"));
        $this->assertTextPresent("%JUST_ARRIVED%");
        $this->assertElementPresent("link=About Us");
        $this->assertElementNotPresent("link=%IMPRESSUM%");
        $this->assertElementNotPresent("link=Test category 0 [DE] šÄßüл");

        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementPresent("link=Test product 0 [EN] šÄßüл");

        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");
        $this->assertElementPresent("link=Test product 1 [EN] šÄßüл");

        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->assertElementPresent("//ul[@id='languages']/li[@class='active']//*[text()='English']");
        $this->assertElementPresent("//ul[@id='languages']/li[2]//*[text()='Deutsch']");
        $this->assertElementPresent("//ul[@id='languages']/li[3]//*[text()='English']");

        $this->clickAndWait("//ul[@id='languages']/li[2]/a");
        $this->getTranslator()->setLanguage(0);
        $this->assertFalse($this->isVisible("//ul[@id='languages']"));
        $this->assertElementPresent("//p[@id='languageTrigger']//*[text()='Deutsch']");

        $this->clickAndWait("//ul[@id='languages']/li[2]/a");
        $this->assertElementPresent("//p[@id='languageTrigger']//*[text()='Deutsch']");
        $this->assertElementPresent("link=[DE 1] Test product 1 šÄßüл");

        $this->clickAndWait("link=Test category 0 [DE] šÄßüл");
        $this->assertElementPresent("link=[DE 4] Test product 0 šÄßüл");

        $this->clickAndWait("link=Manufacturer [DE] šÄßüл");
        $this->assertElementPresent("link=[DE 1] Test product 1 šÄßüл");
        $this->assertElementPresent("link=%IMPRESSUM%");

        $this->clickAndWait("link=%HOME%");
        $this->assertTextPresent("%JUST_ARRIVED%");
        $this->assertElementNotPresent("link=About Us");
        $this->assertElementNotPresent("link=Test category 0 [EN] šÄßüл");
    }

    /**
     * switching currencies in frontend
     *
     * @group main
     */
    public function testFrontendCurrencies()
    {
        $this->openShop();

        //currency checking
        $this->assertElementPresent("//p[@id='currencyTrigger']/a");
        $this->click("//p[@id='currencyTrigger']/a");
        $this->waitForItemAppear("//ul[@id='currencies']");
        $this->assertElementPresent("//ul[@id='currencies']/li[@class='active']//*[text()='EUR']");
        $this->assertElementPresent("//ul[@id='currencies']/li[2]//*[text()='EUR']");
        $this->assertElementPresent("//ul[@id='currencies']/li[3]//*[text()='GBP']");
        $this->assertElementPresent("//ul[@id='currencies']/li[4]//*[text()='CHF']");
        $this->assertElementPresent("//ul[@id='currencies']/li[5]//*[text()='USD']");
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->clickAndWait("//ul[@id='currencies']/li[3]/a");
        $this->assertElementNotVisible("currencies");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='GBP']");
        $this->assertEquals("42.83 £ *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->switchCurrency("CHF");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='CHF']");
        $this->assertEquals("71,63 CHF *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->switchCurrency("USD");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='USD']");
        $this->assertEquals("64.97 $ *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->switchCurrency("EUR");
        $this->assertElementPresent("//p[@id='currencyTrigger']//*[text()='EUR']");
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));

        // #1739 currency switch in vendors
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//a[@id='productList_1']/span"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));

        $this->switchCurrency("GBP");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//a[@id='productList_1']/span"));
        $this->assertEquals("42.83 £ *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));

        $this->switchCurrency("EUR");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//a[@id='productList_1']/span"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));
    }

    /**
     * site footer
     *
     * @group frontend
     */
    public function testFrontendFooter()
    {
        $this->openShop();
        $this->assertElementPresent("panel");
        //checking if delivery note is displayed
        $this->assertTextPresent("* %PLUS_SHIPPING%%PLUS_SHIPPING2%");

        //checking if newsletter fields exist. functionality is checked in other test
        $this->assertElementPresent("//div[@id='panel']/div[1]//label[text()='%NEWSLETTER%']");
        $this->assertElementPresent("//div[@id='panel']/div[1]//input[@name='editval[oxuser__oxusername]']");
        $this->assertElementPresent("//div[@id='panel']/div[1]//button[text()='%SUBSCRIBE%']");
        //exit;
        //SERVICE links
        $this->assertElementPresent("footerServices");
        //there are fixed amount of links in here
        $this->assertElementPresent("//dl[@id='footerServices']//dd[9]");
        $this->assertElementNotPresent("//dl[@id='footerServices']//dd[10]");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%PAGE_TITLE_CONTACT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_CONTACT%", $this->getText("breadCrumb"));
        $this->assertEquals("Your Company Name", $this->getText("//h1"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%HELP%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %HELP% - Main", $this->getText("breadCrumb"));
        $this->assertEquals("%HELP% - Main", $this->getText("//h1"));
        $this->assertTextPresent("Here, you can insert additional information, further links, user manual etc");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%LINKS%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %LINKS%", $this->getText("breadCrumb"));
        $this->assertEquals("%LINKS%", $this->getText("//h1"));
        $this->assertTextPresent("Demo link description [EN] šÄßüл");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %LOGIN%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT%", $this->getText("//h1"));

        $this->assertElementPresent("//section[@id='content']//input[@name='lgn_usr']");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%WISH_LIST%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_WISH_LIST%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_NOTICELIST%", $this->getText("//h1"));
        $this->assertElementPresent("//section[@id='content']//input[@name='lgn_usr']");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%MY_GIFT_REGISTRY%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %MY_ACCOUNT% / %MY_GIFT_REGISTRY%", $this->getText("breadCrumb"));
        $this->assertEquals("%PAGE_TITLE_ACCOUNT_WISHLIST%", $this->getText("//h1"));
        $this->assertElementPresent("//section[@id='content']//input[@name='lgn_usr']");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%PUBLIC_GIFT_REGISTRIES%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PUBLIC_GIFT_REGISTRIES%", $this->getText("breadCrumb"));
        $this->assertEquals("%PUBLIC_GIFT_REGISTRIES%", $this->getText("//h1"));
        $this->assertTextPresent("%SEARCH_GIFT_REGISTRY%");
        $this->isElementPresent("search");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%PAGE_TITLE_BASKET%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->assertTextPresent("%BASKET_EMPTY%");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("%YOU_ARE_HERE%: / About Us", $this->getText("breadCrumb"));
        $this->assertEquals("About Us", $this->getText("//h1"));
        $this->assertTextPresent("Add provider identification here.");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Terms and Conditions']");
        $this->assertEquals("%YOU_ARE_HERE%: / Terms and Conditions", $this->getText("breadCrumb"));
        $this->assertEquals("Terms and Conditions", $this->getText("//h1"));
        $this->assertTextPresent("Insert your terms and conditions here.");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Privacy Policy']");
        $this->assertEquals("%YOU_ARE_HERE%: / Privacy Policy", $this->getText("breadCrumb"));
        $this->assertEquals("Privacy Policy", $this->getText("//h1"));
        $this->assertTextPresent("Enter your privacy policy here.");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");
        $this->assertEquals("%YOU_ARE_HERE%: / Shipping and Charges", $this->getText("breadCrumb"));
        $this->assertEquals("Shipping and Charges", $this->getText("//h1"));
        $this->assertTextPresent("Add your shipping information and costs here.");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Right of Withdrawal']");
        $this->assertEquals("%YOU_ARE_HERE%: / Right of Withdrawal", $this->getText("breadCrumb"));
        $this->assertEquals("Right of Withdrawal", $this->getText("//h1"));
        $this->assertTextPresent("Insert here the Right of Withdrawal policy");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='How to order?']");
        $this->assertEquals("%YOU_ARE_HERE%: / How to order?", $this->getText("breadCrumb"));
        $this->assertEquals("How to order?", $this->getText("//h1"));
        $this->assertTextPresent("Text Example");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Credits']");
        $this->assertEquals("%YOU_ARE_HERE%: / Credits", $this->getText("breadCrumb"));
        $this->assertEquals("Credits", $this->getText("//h1"));
        $this->assertTextPresent("What is OXID eShop?");

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='%NEWSLETTER%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
        $this->assertEquals("%STAY_INFORMED%", $this->getText("//h1"));
        $this->assertTextPresent("You can unsubscribe any time from the newsletter.");

        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("breadCrumb");
        $this->assertTextPresent("%JUST_ARRIVED%");

        //MANUFACTURERS links
        $this->assertElementPresent("footerManufacturers");
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_MANUFACTURER% / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("//ul[@id='productList']/li[1]//a[@id='productList_1']");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertElementPresent("//ul[@id='productList']/li[4]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[5]");

        //CATEGORIES links
        $this->assertElementPresent("footerCategories");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//dl[@id='footerCategories']//dd[2]")));
        $this->clickAndWait("//dl[@id='footerCategories']//dd[2]/a");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertElementPresent("//ul[@id='productList']/li[1]//a[@id='productList_1']");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
    }

    /**
     * Newsletter ordering
     *
     * @group frontend
     */
    public function testFrontendNewsletter()
    {
        $this->openShop();
        //subscribing by entering email
        $this->_typeInfoForSubscription();
        $this->check("newsletterSubscribeOff");
        $this->assertEquals("0", $this->getValue("subscribeStatus"));

        $this->check("newsletterSubscribeOn");
        $this->assertEquals("1", $this->getValue("subscribeStatus"));

        //skipping newsletter username
        $this->type("newsletterUserName", "");
        $this->fireEvent("newsletterUserName", "blur");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOTALLFIELDS%");

        //incorrect user name
        $this->type("newsletterUserName", "aaa");
        $this->fireEvent("newsletterUserName", "blur");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOVALIDEMAIL%");

        //correct user name
        $this->type("newsletterUserName", "example01@oxid-esales.dev");
        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
        $this->assertTextPresent("%MESSAGE_THANKYOU_FOR_SUBSCRIBING_NEWSLETTERS%");
        $this->assertTextPresent("%MESSAGE_SENT_CONFIRMATION_EMAIL%");

        // Check if user subscribed
        $aSubscribedUserData = $this->_userData(2);
        $oValidator = $this->getObjectValidator();
        $this->assertTrue($oValidator->validate('oxNewsSubscribed', $aSubscribedUserData), $oValidator->getErrorMessage());

        //unsubscribing fake email
        $this->clearCache();
        $this->openShop();
        $this->_unsubscribeByEmail("fake@email.com");
        $this->assertTextPresent("%NEWSLETTER_EMAIL_NOT_EXIST%");

        //unsubscibing newsletter
        $this->_unsubscribeByEmail("example01@oxid-esales.dev");
        $this->assertTextPresent("%SUCCESS%");
        $this->assertTextPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_CANCELED%");

        // Check if user unsubscribed
        $aUnsubscribedUserData = $this->_userData(0);
        $oValidator = $this->getObjectValidator();
        $this->assertTrue($oValidator->validate('oxNewsSubscribed', $aUnsubscribedUserData), $oValidator->getErrorMessage());
    }

    /**
     * Newsletter ordering. Double-opt-in is off, so no email confirmation will be sent
     *
     * @group frontend
     */
    public function testFrontendNewsletterDoubleOptInOff()
    {
        //disabling option (Activate Double Opt-In if Users register for Newsletter)
        $this->callShopSC("oxConfig", null, null, array("blOrderOptInEmail" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']");

        //subscribing by entering email
        $this->_typeInfoForSubscription();
        $this->clickAndWait("newsLetterSubmit");

        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
        $this->assertElementPresent("//section[@id='content']//h1");
        $this->assertTextPresent("%MESSAGE_NEWSLETTER_SUBSCRIPTION_ACTIVATED%");

        // Check if user subscribed and receives newsletters
        $aSubscribedUserData = $this->_userData(1);
        $oValidator = $this->getObjectValidator();
        $this->assertTrue($oValidator->validate('oxNewsSubscribed', $aSubscribedUserData), $oValidator->getErrorMessage());
    }

    /**
     * News small box in main page and news page
     *
     * @group frontend
     */
    public function testFrontendNewsBox()
    {
        $this->openShop();
        //there are news visible for not logged in users
        $this->assertElementPresent("newsBox");
        $this->assertEquals("The latest news... %MORE%%ELLIPSIS%", $this->clearString($this->getText("//div[@id='newsBox']//li[1]")));
        $this->assertElementNotPresent("//div[@id='newsBox']//li[2]");
        //Delete new from database where short description is News
        $sOxid = (oxDb::getDb()->getOne("SELECT oxid FROM oxnews WHERE OXSHORTDESC = 'News'"));
        $this->assertFalse(is_null($sOxid), "Oxid was not found in database for OXSHORTDESC='News'");
        $this->callShopSC("oxNews", "delete", $sOxid, null, null, 1);
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("newsBox");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertElementPresent("newsBox");
        $this->assertEquals("Test news text 2 [EN] šÄßüл %MORE%%ELLIPSIS%", $this->clearString($this->getText("//div[@id='newsBox']//li[1]")));
        $this->assertElementPresent("//div[@id='newsBox']//li[1]/a[text()='%MORE%%ELLIPSIS%']");
        $this->clickAndWait("//div[@id='newsBox']//li[1]/a[text()='%MORE%%ELLIPSIS%']");
        //going to news page by clicking continue link

        $aShop = $this->callShopSC("oxShop", null, oxSHOPID, array("oxname"));
        $this->assertEquals("%YOU_ARE_HERE%: / %LATEST_NEWS_AND_UPDATES_AT%" . ' '. $aShop['oxname'], trim($this->getText("breadCrumb")));
        $this->assertTextPresent("%LATEST_NEWS_AND_UPDATES_AT%");
        $this->assertTextPresent("02.01.2008 - Test news 2 [EN] šÄßüл");
        $this->assertTextPresent("Test news text 2 [EN] šÄßüл");
        $this->assertTextPresent("01.01.2008 - Test news 1 [EN] šÄßüл");
        $this->assertTextPresent("Test news text 1 [EN] šÄßüл");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementNotPresent("newsBox");

        //loading news not only in start page
        //Option (Load News only on Start Page) is OFF
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadNewsOnlyStart" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("newsBox");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertElementPresent("newsBox");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementPresent("newsBox");

        // Option (Load News) are disabled
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadNews" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("newsBox");
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->assertElementNotPresent("newsBox");
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementNotPresent("newsBox");
    }

    /**
     * Promotions in frontend. Newest products (just arrived)
     *
     * @group frontend
     */
    public function testFrontendPromotionsNewestProducts()
    {
        $this->openShop();
        //Just arrived!
        $this->assertElementPresent("//section[@id='content']/h2/a[@id='rssNewestProducts']");
        $this->assertElementPresent("//ul[@id='newItems']/li[1]//img");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("newItems_1")));
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("//ul[@id='newItems']/li[1]//span[@class='price']")));
        $this->assertTrue($this->isVisible("//ul[@id='newItems']/li[1]//button[text()='%TO_CART%']"));
        $this->assertTrue($this->isVisible("incVatMessage"));
        $this->assertEquals("* %PLUS_SHIPPING%%PLUS_SHIPPING2%", $this->clearString($this->getText("incVatMessage")));

        $this->assertElementPresent("//ul[@id='newItems']/li[2]//img");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("newItems_2")));
        $this->assertEquals("100,00 €", $this->clearString($this->getText("//ul[@id='newItems']/li[2]//span[@class='price']")));
        $this->assertTrue($this->isVisible("//ul[@id='newItems']/li[2]//a[text()='%MORE_INFO%']"));

        $this->assertEquals("%REDUCED_FROM_2% 150,00 €", $this->clearString($this->getText("//ul[@id='newItems']/li[2]/form//span[@class='oldPrice']")));

        //link on img
        $this->clickAndWait("//ul[@id='newItems']/li[1]//a[1]");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%HOME%");

        //link on title
        $this->clickAndWait("newItems_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->clickAndWait("link=%HOME%");

        //click toCart
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
    }


    /**
     * Promotions in frontend. Top of the shop (right side)
     *
     * @group frontend
     */
    public function testFrontendPromotionsTopOfTheShop()
    {
        $this->openShop();
        $this->assertElementPresent("topBox");
        $this->assertEquals("%TOP_OF_THE_SHOP%", $this->getHeadingText("//div[@id='topBox']/h3"));
        $this->assertElementPresent("//div[@id='topBox']/h3/a[@id='rssTopProducts']");
        $this->assertElementPresent("//div[@id='topBox']/ul/li[1]//img[@alt='Test product 0 [EN] šÄßüл ']");
        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 € *", $this->clearString($this->getText("//div[@id='topBox']//li[2]")));
        $this->clickAndWait("//div[@id='topBox']//li[2]/a");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));

        //testing top of the shop in category page
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertElementNotPresent("topBox");
    }

    /**
     * Checking Top Menu Navigation
     *
     * @group frontend
     */
    public function testFrontendTopMenu()
    {
        $this->openShop(false, true);
        $this->assertTrue($this->isVisible("navigation"));
        $this->assertEquals("%HOME%", $this->clearString($this->getText("//ul[@id='navigation']/li[1]")));
        $this->assertEquals("Test category 0 [EN] šÄßüл »", $this->clearString($this->getText("//ul[@id='navigation']/li[3]/a")));
        $this->assertElementNotPresent("//ul[@id='tree']/li");
        $this->clickAndWait("//ul[@id='navigation']/li[3]/a");

        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertElementPresent("//ul[@id='tree']/li");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("productList_1");
        $this->assertElementPresent("productList_2");
        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");

        $this->clickAndWait("//ul[@id='tree']/li/ul/li/a");
        $this->assertElementPresent("//ul[@id='tree']/li");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertElementPresent("productList_1");
        $this->assertElementPresent("productList_2");

        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");

        //more
        $this->clickAndWait("//ul[@id='navigation']/li[4]/a");
        $this->assertElementPresent("//ul[@id='navigation']/li[4]");
        $this->assertElementPresent("//ul[@id='navigation']/li[5]");
        //exit;
        $this->assertEquals("%MORE% »", $this->getText("//ul[@id='navigation']/li[5]/a"));
        $this->assertElementNotPresent("//ul[@id='navigation']/li[6]");
        //checking on option (Amount of categories that is displayed at top) if is used value = 4
        $this->callShopSC("oxConfig", null, null, array("iTopNaviCatCount" => array("type" => "str", "value" => '5', "module" => "theme:azure")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("//ul[@id='navigation']/li[6]");
        $this->assertEquals("%MORE% »", $this->getText("//ul[@id='navigation']/li[7]/a"));
        $this->assertElementNotPresent("//ul[@id='navigation']/li[8]");
    }

    /**
     * Category navigation and all elements checking
     *
     * @group frontend
     */
    public function testFrontendCategoryFilters()
    {
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");

        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->selectDropDown("itemsPerPage", "1");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        //checking if paging with filters works correctly
        $this->selectDropDown("attributeFilter[testattribute1]", "attr value 1 [EN] šÄßüл");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: attr value 1 [EN] šÄßüл", $this->clearString($this->getText("//div[@id='attributeFilter[testattribute1]']/p")));
        $this->assertElementNotPresent("itemsPager");
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("productList_1");
        $this->selectDropDown("attributeFilter[testattribute1]", "%PLEASE_CHOOSE%");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='1']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2']");
        $this->assertElementPresent("//ul[@id='productList']/li[1]//input[@name='aid' and @value='1000']");

        $this->selectDropDown("attributeFilter[testattribute2]", "attr value 12 [EN] šÄßüл");
        $this->assertElementPresent("productList_1");

        $this->assertElementNotPresent("itemsPager");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: attr value 12 [EN] šÄßüл %PLEASE_CHOOSE% attr value 12 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));

        $this->selectDropDown("attributeFilter[testattribute2]", "%PLEASE_CHOOSE%");
        $this->selectDropDown("attributeFilter[testattribute3]", "attr value 3 [EN] šÄßüл");
        $this->assertElementPresent("//ul[@id='productList']/li[1]//input[@name='aid' and @value='1000']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='1']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2']");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: attr value 3 [EN] šÄßüл %PLEASE_CHOOSE% attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
    }


    /**
     * Category navigation testing
     *
     * @group frontend
     */
    public function testFrontendCategoryNavigation()
    {
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->selectDropDown("sortItems", "", "li[2]");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: %PLEASE_CHOOSE% attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        $this->assertElementPresent("//a[@id='rssActiveCategory']");
        $this->assertEquals("Test category 0 desc [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertEquals("Category 0 long desc [EN] šÄßüл", $this->getText("catLongDesc"));
        $this->assertElementPresent("//a[@id='moreSubCat_1']/@title", "attribute title is gone from link. in 450 it was for categories names, that were shortened");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_1']/@title")));

        //checking items per page
        $this->assertElementNotPresent("itemsPager");
        $this->selectDropDown("itemsPerPage", "1");
        $this->selectDropDown("viewOptions", "%line%");
        $this->_checkNavigation('itemsPager', true, false);

        //pagination on top
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->_checkNavigation('itemsPager', false, true);

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='%PREVIOUS%']");
        $this->_checkNavigation('itemsPager', true, false);

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='%NEXT%']");
        $this->_checkNavigation('itemsPager', false, true);

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='1']");
        $this->_checkNavigation('itemsPager', true, false);

        //testing bottom pagination
        $this->clickAndWait("//div[@id='itemsPagerbottom']/a[text()='2']");
        $this->_checkNavigation('itemsPagerbottom', false, true);

        $this->clickAndWait("//div[@id='itemsPagerbottom']/a[text()='%PREVIOUS%']");
        $this->_checkNavigation('itemsPagerbottom', true, false);

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='%NEXT%']");
        $this->_checkNavigation('itemsPagerbottom', false, true);

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='1']");
        $this->_checkNavigation('itemsPagerbottom', true, false);

        $this->_checkIfPossibleToBuyItemFromList(' %TO_CART%');

        $this->selectDropDown("viewOptions", "%infogrid%");
        $this->_checkIfPossibleToBuyItemFromList('%TO_CART%');

        $this->selectDropDown("viewOptions", "%grid%");
        $this->_checkIfPossibleToBuyItemFromList();

        $this->clickAndWait("moreSubCat_1");
        $this->assertEquals("%YOU_ARE_HERE%: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));
    }


    /**
     * sorting, paging and navigation in lists. Sorting is not available for lists
     *
     * @group frontend
     */
    public function testFrontendCategorySorting()
    {
        //sorting is enabled
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));

        $this->selectDropDown("viewOptions", "%line%");
        $this->selectDropDown("sortItems", "", "li[3]");
        $this->assertElementPresent("productList_1");
        $this->assertElementPresent("productList_2");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");

        $this->selectDropDown("sortItems", "", "li[2]");
        $this->assertElementPresent("productList_1");
        $this->assertElementPresent("productList_2");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");

        //disabling option (Users can sort Product Lists)
        $this->callShopSC("oxConfig", null, null, array("blShowSorting" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertElementNotPresent("sortItems");
    }


    /**
     * price category testing. Note, there are no functionality which would allow to use filters in price category.
     *
     * @group frontend
     */
    public function testFrontendPriceCategory()
    {
        $this->openShop();
        $this->clickAndWait("link=price [EN] šÄßüл");
        $this->assertEquals("price [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("price category [EN] šÄßüл", $this->getText("catDesc"));

        $this->assertElementNotPresent("itemsPager");
        $this->selectDropDown("itemsPerPage", "2");
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->assertElementPresent("//div[@id='itemsPager']/a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//div[@id='itemsPager']/a[text()='%NEXT%']");

        $this->selectDropDown("sortItems", "", "li[4]"); //price asc
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->selectDropDown("sortItems", "", "li[2]"); //title desc
        $this->assertElementNotPresent("//div[@id='itemsPager']/a[text()='%PREVIOUS%']");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertElementPresent("//ul[@id='productList']/li[2]//input[@name='aid' and @value='1000']");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");

        $this->selectDropDown("itemsPerPage", "10");
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
        $this->assertElementPresent("//ul[@id='productList']/li[6]");
        $this->assertElementNotPresent("itemsPager");
        $this->assertElementNotPresent("//ul[@id='productList']/li[7]");
    }

    /**
     * Search in frontend
     *
     * @group frontend
     */
    public function testFrontendSearchNavigation()
    {
        $this->openShop();
        //searching for 1 product (using product search field value)
        $this->searchFor("šÄßüл1000");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertElementPresent("rssSearchProducts");
        $this->assertEquals("1 %HITS_FOR% \"šÄßüл1000\"", $this->getHeadingText("//h1"));
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='searchList']/li[1]//div[2]/div[2]")));
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("productPrice_searchList_1")));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_searchList_1"));

        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("toBasket_searchList_1");
        $this->waitForElementText("3", "//div[@id='miniBasket']/span");

        $this->clickAndWait("//ul[@id='searchList']/li[1]//a"); //link on img
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"šÄßüл1000\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("šÄßüл1000", $this->getValue("searchParam"));

        //navigation between search results
        $this->searchFor("100");
        $this->assertTextPresent("4 %HITS_FOR% \"100\"");
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->assertEquals("%DELIVERYTIME_DELIVERYTIME%: 1 day", $this->getText("productDeliveryTime"));
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("%DELIVERYTIME_DELIVERYTIME%: 1 week", $this->getText("productDeliveryTime"));
        $this->clickAndWait("linkNextArticle");
        //if product is not buyable, no delivery time should be shown
        $this->assertTextNotPresent("%DELIVERYTIME_DELIVERYTIME%: 1 month", "This is parent product. It is not buyable, so no delivery time should be shown. works ok in basic templates");
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("%DELIVERYTIME_DELIVERYTIME%: 4 - 9 days", $this->getText("productDeliveryTime"));
    }

    /**
     * Search in frontend
     *
     * @group frontend
     */
    public function testFrontendSearchSpecialCases()
    {
        $this->openShop();
        //not existing search
        $this->searchFor("notExisting");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertTextPresent("%NO_ITEMS_FOUND%");
        $this->assertEquals("0 %HITS_FOR% \"notExisting\"", $this->getHeadingText("//h1"));
        //special chars search
        $this->searchFor("[EN] šÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertEquals("4 %HITS_FOR% \"[EN] šÄßüл\"", $this->getHeadingText("//h1"));
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("searchList_1");
        $this->assertElementPresent("searchList_2");
        $this->assertElementPresent("searchList_3");
        $this->assertElementPresent("searchList_4");
        $this->assertElementNotPresent("//ul[@id='searchList']/li[5]");

        //testing #1582
        $aCategoryParams = array("oxactive" => 0, "oxactive_1" => 0);
        $this->callShopSC("oxCategory", "save", "testcategory0", $aCategoryParams, array(), 1);

        //category is inactive
        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("link=Test category 0 [EN] šÄßüл");
        $this->searchFor("1002");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"1002\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Search in frontend. OR and AND separators
     *
     * @group frontend
     */
    public function testFrontendSearchOrAnd()
    {
        //AND is used for search keys
        // Checking option ((If serveral Search Terms are entered, all Search Terms have to be found in Search Results (AND). (If this Setting is unchecked, only one Search Term has to be found (OR)) is ON
        $this->callShopSC("oxConfig", null, null, array("blSearchUseAND" => array("type" => "bool", "value" => 'true')));
        $this->clearCache();
        $this->openShop();
        $this->searchFor("1000 1001");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertTextPresent("%NO_ITEMS_FOUND%");
        $this->assertEquals("0 %HITS_FOR% \"1000 1001\"", $this->getHeadingText("//h1"));

        //OR is used for search keys
        //Checking option ((If serveral Search Terms are entered, all Search Terms have to be found in Search Results (AND). (If this Setting is unchecked, only one Search Term has to be found (OR)) is OFF
        $this->callShopSC("oxConfig", null, null, array("blSearchUseAND" => array("type" => "bool", "value" => "false")));
        $this->clearTemp();
        $this->searchFor("1000 1001");
        $this->assertEquals("2 %HITS_FOR% \"1000 1001\"", $this->getHeadingText("//h1"));
    }

    /**
     * Search in frontend. Checking option: Fields to be considered in Search
     *
     * @group frontend
     */
    public function testFrontendSearchConsideredFields()
    {
        //art num is not considered in search
        $this->callShopSC("oxConfig", null, null, array("aSearchCols" => array("type" => "arr", "value" => array("oxtitle", "oxshortdesc"))));
        $this->clearCache();
        $this->openShop();
        $this->searchFor("100");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertTextPresent("0 %HITS_FOR% \"100\"");

        //art num is considered in search
        $this->callShopSC("oxConfig", null, null, array("aSearchCols" => array("type" => "arr", "value" => array("oxtitle", "oxshortdesc", "oxsearchkeys", "oxartnum"))));
        $this->clearTemp();
        $this->searchFor("100");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertTextPresent("4 %HITS_FOR% \"100\"");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("searchList_2")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("searchList_3")));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("searchList_4")));
        $this->assertElementNotPresent("searchList_5");

        $this->clickAndWait("searchList_3");
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "var2 [EN] šÄßüл");
        $this->assertEquals("%YOU_ARE_HERE%: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertTextPresent("4 %HITS_FOR% \"100\"");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("searchList_2")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("searchList_3")));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("searchList_4")));
    }

    /**
     * Manufacturer navigation and all elements checking
     *
     * @group frontend
     */
    public function testFrontendManufacturer()
    {
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_MANUFACTURER% / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("catDesc"));
        $this->_checkArticleList();

        //going to vendor root by path link (you are here)
        $this->clickAndWait("//nav[@id='breadCrumb']/a[1]");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_MANUFACTURER%", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("//h1");
        //going to vendor via menu link
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_8']/@title")));

        $this->clickAndWait("moreSubCat_8");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_MANUFACTURER% / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("catDesc"));
        $this->_checkArticleList();

        //manufacturers tree is disabled
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadManufacturerTree" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("footerManufacturers");
    }

    /**
     * Distributors navigation and all elements checking
     *
     * @group frontend
     */
    public function testFrontendDistributors()
    {
        $this->openShop();
        $sShopId = oxSHOPID;
        $this->open(shopURL."index.php?cl=vendorlist&cnid=root&shp={$sShopId}");
        $this->clickAndWait("moreSubCat_1");

        $this->assertEquals("%YOU_ARE_HERE%: / %BY_VENDOR% / Distributor [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("catDesc"));
        $this->_checkArticleList();

        //going to vendor root by path link (you are here)
        $this->clickAndWait("//nav[@id='breadCrumb']/a[1]");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_VENDOR%", $this->getText("breadCrumb"));
        //going to vendor via menu link
        $this->assertElementPresent("//a[@id='moreSubCat_1']/@title", "attribute title is gone from link. in 450 it was for distributors names, that were shortened");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_1']/@title")));
        $this->clickAndWait("moreSubCat_1");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_VENDOR% / Distributor [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("catDesc"));
        $this->_checkArticleList();

        //disabling vendor tree
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadVendorTree" => array("type" => "bool", "value" => "false")));
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("footerVendors");
    }

    /**
     * sorting, paging and navigation in manufacturers
     *
     * @group frontend
     */
    public function testFrontendPagingAndNavigationManufacturers()
    {
        // Articles with ID's 1001 and 1002 have MultiDimensional variants so they shouldn't have the input[@name='aid']
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("%YOU_ARE_HERE%: / %BY_MANUFACTURER% / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("itemsPager");

        //top navigation
        $this->selectDropDown("sortItems", "", "li[4]"); //price asc
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("productList_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_4"));

        $this->selectDropDown("itemsPerPage", "2");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='1' and @class='page active']");
        $this->assertElementNotPresent("//div[@id='itemsPager']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2']");
        $this->assertElementNotPresent("//div[@id='itemsPager']//a[text()='3']");
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='2']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='%PREVIOUS%']");
        $this->assertElementNotPresent("//div[@id='itemsPager']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='1']");
        $this->assertElementNotPresent("//div[@id='itemsPager']//a[text()='3']");
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
        $this->assertElementPresent("//ul[@id='productList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("productList_1");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='%NEXT%']");
        $this->assertElementPresent("productList_1");
        //bottom navigation
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='2' and @class='page active']");
        $this->assertElementNotPresent("//div[@id='itemsPagerbottom']//a[text()='1' and @class='page active']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");
        $this->assertElementNotPresent("//div[@id='itemsPagerbottom']//a[text()='%NEXT%']");

        $this->assertElementPresent("productList_1");
        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='1' and @class='page active']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='%NEXT%']");
        $this->assertElementNotPresent("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("productList_1");

        $this->assertElementNotPresent("//ul[@id='productList']/li[3]");
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='%NEXT%']");

        $this->assertElementNotPresent("productList_3");
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");

        $this->assertElementPresent("productList_1");
    }

    /**
     * sorting, paging and navigation in search
     *
     * @group frontend
     */
    public function testFrontendSortingSearch()
    {
        $this->openShop();
        //testing navigation in search
        $this->searchFor("100");
        $this->assertElementNotPresent("itemsPager");

        //top navigation testing
        $this->selectDropDown("sortItems", "", "li[4]");
        $this->_checkSortedListEn();
        $this->assertElementNotPresent("//ul[@id='searchList']/li[5]");

        $this->selectDropDown("sortItems", "", "li[2]"); //title desc
        $this->_checkSortedListEn('titleDesc');

        //adding additional column for sorting
        $this->callShopSC("oxConfig", null, null, array("aSortCols" => array("type" => "arr", "value" => serialize(array("oxtitle", "oxvarminprice", "oxartnum")))));

        //DE lang
        $this->switchLanguage("Deutsch");

        $this->selectDropDown("sortItems", "", "li[3]"); //title asc
        $this->_checkSortedListDe('titleAsc');

        $this->selectDropDown("sortItems", "", "li[5]"); //artnum asc
        $this->_checkSortedListDe();
    }

    /**
     * sorting, paging and navigation in search
     *
     * @group frontend
     */
    public function testFrontendPagingAndNavigationSearch()
    {
        $this->openShop();
        //testing navigation in search
        $this->searchFor("100");
        $this->assertEquals("%YOU_ARE_HERE%: / %SEARCH%", $this->getText("breadCrumb"));
        $this->assertElementNotPresent("itemsPager");

        $this->selectDropDown("itemsPerPage", "2");
        $this->_checkFilter();
        $this->assertElementPresent("itemsPager");
        $this->assertElementPresent("//ul[@id='searchList']/li[1]");
        $this->assertElementPresent("//ul[@id='searchList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='searchList']/li[3]");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='1']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']");
        $this->assertElementNotPresent("//div[@id='itemsPager']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPager']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//ul[@id='searchList']/li[1]");
        $this->assertElementPresent("//ul[@id='searchList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='searchList']/li[3]");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_2"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1000']");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='2']");
        $this->assertElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1003']");
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='1']");

        //bottom navigation
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='2' and @class='page active']");
        $this->assertElementNotPresent("//div[@id='itemsPagerbottom']//a[text()='%NEXT%']");
        $this->assertElementPresent("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//ul[@id='searchList']/li[1]");
        $this->assertElementPresent("//ul[@id='searchList']/li[2]");
        $this->assertElementNotPresent("//ul[@id='searchList']/li[3]");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_2"));

        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='%PREVIOUS%']");
        $this->assertElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1000']");
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='2']");
        $this->assertElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1003']");
    }

    /**
     * Checking Performance options
     * option: Load Selection Lists in Product Lists
     * option: Support Price Modifications by Selection Lists
     * option: Load Selection Lists
     *
     * @group frontend
     */
    public function testFrontendPerfOptionsSelectionLists()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("selectlistsselector_searchList_1");
        //page details. selection lists are with prices
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл +1,00 € selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectlistsselector_searchList_1")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл +1,00 € selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectlistsselector_productList_2")));

        //option (Support Price Modifications by Selection Lists) is OFF
        $this->callShopSC("oxConfig", null, null, array("bl_perfUseSelectlistPrice" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->searchFor("1001");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectlistsselector_searchList_1")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectlistsselector_productList_2")));

        // loading selection lists in product lists is OFF
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadSelectListsInAList" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementNotPresent("selectlistsselector_productList_2");
        $this->clickAndWait("//ul[@id='productList']/li[2]//a");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("productSelections")));

        //loading selection lists is OFF
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadSelectLists" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementNotPresent("selectlistsselector_productList_2");
        $this->clickAndWait("//ul[@id='productList']/li[2]//a");
        $this->assertElementNotPresent("productSelections");
    }



    /**
     * Listmania is disabled via performance options
     *
     * @group frontend
     */
    public function testFrontendDisabledListmania()
    {
        //Listmania is disabled
        $this->callShopSC("oxConfig", null, null, array("bl_showListmania" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));

        $this->clearCache();
        $this->openShop();
        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");
        $this->searchFor("100");
        $this->assertElementNotPresent("//article[@id='recommendationsBox']/h3");
        $this->assertElementNotPresent("//article[@id='recommendationsBox']//ul");
        $this->assertElementNotPresent("searchRecomm");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertElementNotPresent("//article[@id='recommendationsBox']/h3");
        $this->assertElementNotPresent("//article[@id='recommendationsBox']//ul");
        $this->assertElementNotPresent("searchRecomm");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertElementPresent("linkToWishList");
        $this->assertElementPresent("linkToNoticeList");
        $this->assertElementNotPresent("recommList");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->assertElementPresent("//aside[@id='sidebar']//a[text()='%MY_WISH_LIST%']");
        $this->assertElementPresent("//aside[@id='sidebar']//a[text()='%MY_GIFT_REGISTRY%']");
        $this->assertElementNotPresent("//aside[@id='sidebar']//a[text()='%MY_LISTMANIA%']");
    }



    /**
     * Checking contact sending
     *
     * @group frontend
     */
    public function testFrontendContact()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", null, null, array("iUseGDVersion" => array("type" => "str", "value" => 2)));

        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%PAGE_TITLE_CONTACT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_CONTACT%", $this->getText("breadCrumb"));
        $this->assertEquals("Your Company Name", $this->getText("//h1"));
        $this->assertEquals("%MR% %MRS%", $this->clearString($this->getText("editval[oxuser__oxsal]")));
        $this->select("editval[oxuser__oxsal]", "label=%MRS%");
        $this->type("editval[oxuser__oxfname]", "first name");
        $this->type("editval[oxuser__oxlname]", "");
        $this->type("contactEmail", "example_test@oxid-esales.dev");
        $this->type("c_subject", "subject");
        $this->type("c_message", "message text");
        $this->click("//button[text()='%SEND%']");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOTALLFIELDS%");

        $this->assertEquals("%MRS%", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("first name", $this->getValue("editval[oxuser__oxfname]"));
        $this->type("editval[oxuser__oxfname]", "");
        $this->type("editval[oxuser__oxlname]", "last name");
        $this->assertEquals("example_test@oxid-esales.dev", $this->getValue("contactEmail"));
        $this->assertEquals("subject", $this->getValue("c_subject"));
        $this->assertEquals("message text", $this->getValue("c_message"));
        $this->click("//button[text()='%SEND%']");
        $this->waitForText("%ERROR_MESSAGE_INPUT_NOTALLFIELDS%");

        $this->assertEquals("%MRS%", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->type("editval[oxuser__oxfname]", "first name");
        $this->assertEquals("last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("example_test@oxid-esales.dev", $this->getValue("contactEmail"));
        $this->assertEquals("subject", $this->getValue("c_subject"));
        $this->assertEquals("message text", $this->getValue("c_message"));
        $this->clickAndWait("//button[text()='%SEND%']");
        $this->assertTextPresent("%THANK_YOU%");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_CONTACT%", $this->getText("breadCrumb"));
    }



    /**
     * Checking option 'Display Message when Product is added to Cart ' from Core settings -> System
     *
     * @group frontend
     */
    public function testFrontendMessageWhenProductIsAddedToCart()
    {
        $this->openShop();
        $this->assertElementNotPresent("newItemMsg");
        $this->addToBasket("1000");
        $this->assertElementNotPresent("newItemMsg");

        //displaying message, when product is added to basket
        $this->callShopSC("oxConfig", null, null, array("iNewBasketItemMessage" => array("type" => "select", "value" => '1', "module" => "theme:azure")));

        $this->clearCache();
        $this->addToBasket("1000");
        $this->waitForItemAppear("newItemMsg");
        $this->assertEquals("%NEW_BASKET_ITEM_MSG%", $this->clearString($this->getText("newItemMsg")));
        $this->waitForTextDisappear("%NEW_BASKET_ITEM_MSG%");
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");

        //display popup when product is added to basket
        $this->callShopSC("oxConfig", null, null, array("iNewBasketItemMessage" => array("type" => "select", "value" => '2', "module" => "theme:azure")));

        $this->clearCache();
        $this->addToBasket("1000");
        $this->waitForItemAppear("modalbasketFlyout");

        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[1]"));
        $this->assertEquals("%TOTAL% 50,00 €", $this->clearString($this->getText("//div[@id='modalbasketFlyout']//p[2]")));
        $this->clickAndWait("//div[@id='modalbasketFlyout']//a[text()='%DISPLAY_BASKET%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %PAGE_TITLE_BASKET%", $this->getText("breadCrumb"));
        $this->waitForElementText("1", "//div[@id='miniBasket']/span");
        $this->assertEquals("Test product 0 [EN] šÄßüл %PRODUCT_NO%: 1000", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]")));

        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("modalbasketFlyout");

        $this->addToBasket("1003");
        $this->waitForItemAppear("modalbasketFlyout");
        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[1]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[2]"));
        $this->assertEquals("%TOTAL% 125,00 €", $this->clearString($this->getText("//div[@id='modalbasketFlyout']//p[2]")));

        $this->clickAndWait("//div[@id='modalbasketFlyout']//a[text()='%CHECKOUT%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %ADDRESS%", $this->getText("breadCrumb"));
        $this->waitForElementText("2", "//div[@id='miniBasket']/span");

        $this->clickAndWait("link=%HOME%");
        $this->assertElementNotPresent("modalbasketFlyout");

        $this->addToBasket("1000");
        $this->waitForItemAppear("modalbasketFlyout");

        $this->click("//div[@id='modalbasketFlyout']//p[1]/img");
        $this->waitForItemDisappear("modalbasketFlyout");
        $this->waitForElementText("3", "//div[@id='miniBasket']/span");

        $this->addToBasket("1000");
        $this->waitForItemAppear("modalbasketFlyout");
        $this->clickAndWait("//div[@id='modalbasketFlyout']//ul/li[2]/a");
        $this->waitForElementText("4", "//div[@id='miniBasket']/span");
        $this->assertElementNotPresent("modalbasketFlyout");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));
    }



    /**
     * Checking CMS pages marked as categories
     *
     * @group frontend
     */
    public function testFrontendCmsAsCategories()
    {
        //activating CMS pages as categories
        //TODO: Selenium refactor to remove SQL's executions
        $this->executeSql("UPDATE `oxcontents` SET `OXACTIVE`=1, `OXACTIVE_1`=1 WHERE `OXID` = 'testcontent1' OR `OXID` = 'testcontent2' OR `OXID` = 'oxsubshopcontent1' OR `OXID` = 'oxsubshopcontent2'");

        //cms as root category
        $this->clearCache();
        $this->openShop();
        $this->assertEquals("[last] [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='navigation']/li[3]")));
        $this->clickAndWait("//ul[@id='navigation']/li[3]//a");
        $this->assertEquals("%YOU_ARE_HERE%: / [last] [EN] content šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("//h1"));
        $this->assertTextPresent("content [EN] 1 šÄßüл");

        //cms as subcategory
        $this->assertEquals("[last] [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='navigation']/li[3]")));
        $this->clickAndWait("//ul[@id='navigation']/li[5]/a");
        $this->assertEquals("%CATEGORY_OVERVIEW%", $this->getHeadingText("//h1"));
        $this->clickAndWait("moreSubCat_2");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li[1]")));
        $this->assertElementPresent("//a[@id='moreSubCms_1_1']/@title", "attribute title is gone from link. in 450 it was for category names, that were shortened");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getAttribute("//a[@id='moreSubCms_1_1']/@title"), "bug from Mantis #495");
        $this->clickAndWait("moreSubCms_1_1");
        $this->assertEquals("%YOU_ARE_HERE%: / 3 [EN] content šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//h1"));
        $this->assertTextPresent("content [EN] last šÄßüл");
        $this->assertEquals("3 [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li[1]")));
    }

    /**
     * Promotions in frontend. Categories
     *
     * @group frontend
     */
    public function testFrontendPromotionsCategories()
    {
        if (isSUBSHOP) {
            $this->markTestSkipped('This test case is only actual when SubShops are available.');
        }
        $this->clearCache();
        $this->openShop();
        //Categories
        $this->assertElementPresent("//div[@id='specCatBox']/h2");
        $this->assertEquals("Wakeboards", $this->getText("//div[@id='specCatBox']/h2"));
        //fix it in future: mouseOver effect is implemented via css. selenium does not support it yet
        $this->clickAndWait("//div[@id='specCatBox']/a");
        $this->assertEquals("%YOU_ARE_HERE%: / Wakeboarding / Wakeboards", $this->getText("breadCrumb"));
        $this->assertEquals("Wakeboards", $this->getHeadingText("//h1"));
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
    }

    /**
     * Checking Performance options
     *
     * @group frontend
     */
    public function testFrontendPerfOptions1()
    {
        $this->openShop();
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));
        $this->assertEquals("50,00 € *", $this->getText("//div[@id='topBox']/ul/li[2]//strong"));

        // option -> performance-> "Display Number of contained Products behind Category Names"
        $this->callShopSC("oxConfig", null, null, array("bl_perfShowActionCatArticleCnt" => array("type" => "bool", "value" => true)));
        // option -> performance-> "Calculate Shipping Costs"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadDelivery" => array("type" => "bool", "value" => false)));
        // option ->performance -> " Show Prices in "Top of the Shop" and "Just arrived!" "
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadPriceForAddList" => array("type" => "bool", "value" => false)));

        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("//ul[@id='newItems']/li[1]//strong[text()='50,00 € *']");
        $this->assertElementNotPresent("//div[@id='topBox']/ul/li[1]//strong[text()='50,00 €']");
        $this->assertEquals("Test category 0 [EN] šÄßüл (2) »", $this->getText("//ul[@id='navigation']/li[3]"));

        $this->clickAndWait("//ul[@id='navigation']/li[3]/a");
        $this->assertEquals("Test category 0 [EN] šÄßüл (2)", $this->clearString($this->getText("//ul[@id='tree']/li[1]/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл (2)", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getAttribute("//a[@id='moreSubCat_1']@title"));
        $this->assertEquals("(2)", substr($this->getText("moreSubCat_1"), -3));

        $this->loginInFrontend("example_test@oxid-esales.dev", "useruser");

        $this->addToBasket('1001', 1, 'user');

        $this->clickAndWait("//button[text()='%CONTINUE_TO_NEXT_STEP%']");

        $this->selectAndWait("sShipSet", "label=Example Set1: UPS 48 hours");
        $this->assertElementNotPresent("shipSetCost");

        //option -> performance->"Activate user Reviews and Ratings"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadReviews" => array("type" => "bool", "value" => "false")));
        //option -> performance->"Calculate Product Price"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadPrice" => array("type" => "bool", "value" => "false")));
        //option -> performance->"Load similar Products"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadSimilar" => array("type" => "bool", "value" => "false")));
        //option -> performance->" 	Load Crossselling "
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadCrossselling" => array("type" => "bool", "value" => "false")));
        //option -> performance->"Load Accessories "
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadAccessoires" => array("type" => "bool", "value" => "false")));
        //theme option-> "Use compare list"
        $this->callShopSC("oxConfig", null, null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));

        $this->openArticle(1002);
        $this->assertTextNotPresent("review for parent product šÄßüл");
        $this->openArticle(1000);

        $this->assertElementNotPresent("productPrice");
        $this->assertElementNotPresent("similar");
        $this->assertElementNotPresent("cross");
        $this->assertElementNotPresent("accessories");

        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertElementNotPresent("addToCompare");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='%ACCOUNT%']");
        $this->assertElementNotPresent("link=%MY_PRODUCT_COMPARISON%");

        // option -> performance->"Load Promotions"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadAktion" => array("type" => "bool", "value" => "false")));
        //option -> performance->" Display Currencies"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadCurrency" => array("type" => "bool", "value" => "false")));
        //option -> performance->" Display Languages"
        $this->callShopSC("oxConfig", null, null, array("bl_perfLoadLanguages" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("titleBargain_1");
        $this->assertElementNotPresent("//div[@id='specCatBox']/h2");
        $this->assertElementNotPresent("topBox");
        $this->assertElementNotPresent("newItems");
        $this->assertElementNotPresent("currencyTrigger");
        $this->assertElementNotPresent("languageTrigger");
    }

    /**
     * Promotions in frontend. week's special
     *
     * @group frontend
     */
    public function testFrontendPromotionsWeekSpecial()
    {
        if (isSUBSHOP) {
            $this->callShopSC("oxActions", "save", 'oxbargain', array('oxshopid' => oxSHOPID), null, 1);
        }

        $aWeeksSpecialArticleParameters = array(
            'oxshopid' => oxSHOPID,
            'oxactionid' => 'oxbargain',
            'oxartid' => '1000',
            'oxsort' => '-1'
        );
        $this->callShopSC("oxBase", "save", 'oxactions2article', $aWeeksSpecialArticleParameters);

        $this->openShop();
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//div[@id='specBox']/div/a"));
        $this->clickAndWait("//div[@id='specBox']/div/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=%HOME%");
        $this->assertEquals("50,00 € *", $this->getText("//div[@id='priceBargain_1']//span"));
        $this->assertEquals("%TO_CART%", $this->clearString($this->getText("//div[@id='priceBargain_1']//a")));
        $this->clickAndWait("//div[@id='priceBargain_1']//a");
        $this->openBasket();
        $this->assertElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']");

        // Remove from week's special added article
        $this->callShopSC('oxActions', 'removeArticle', 'oxbargain', null, array('1000'));
        $this->clearCache();
        $this->openShop();
        $this->clickAndWait("link=%HOME%");
        $this->assertEquals("%WEEK_SPECIAL%", $this->getHeadingText("//div[@id='specBox']//h3"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//div[@id='specBox']/div/a"));
        $this->clickAndWait("//div[@id='specBox']/div/a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=%HOME%");
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet
        $this->assertEquals("%REDUCED_FROM_2% 150,00 €", $this->getText("//div[@id='priceBargain_1']//span"));
        $this->assertEquals("100,00 €", $this->getText("//div[@id='priceBargain_1']//span[2]"));
        $this->assertEquals("%MORE_INFO%", $this->clearString($this->getText("//div[@id='priceBargain_1']//a")));
        $this->clickAndWait("//div[@id='priceBargain_1']//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * checking if variants are displayed correctly in list
     *
     * @group frontend
     */
    public function testVariantsInLists()
    {
        $this->openShop();
        $this->searchFor("3570 1002");
        $this->assertEquals("2 %HITS_FOR% \"3570 1002\"", $this->getHeadingText("//h1"));
        $this->assertElementPresent("searchList_1");
        $this->assertElementPresent("searchList_2");
        $this->assertElementNotPresent("searchList_3");
        //double grid view
        $this->assertElementPresent("//form[@name='tobasketsearchList_1']//a[text()='%MORE_INFO%']");
        $this->assertElementPresent("//form[@name='tobasketsearchList_2']//a[text()='%MORE_INFO%']");
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//a[text()='%MORE_INFO%']");

        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->waitForElement("searchList");
        $this->assertEquals("2 %HITS_FOR% \"3570 1002\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("//form[@name='tobasketsearchList_2']//a[text()='%MORE_INFO%']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

        $this->searchFor("3570 1002");
        $this->assertEquals("2 %HITS_FOR% \"3570 1002\"", $this->getHeadingText("//h1"));
        //line view
        $this->selectDropDown("viewOptions", "%line%");
        $this->assertElementPresent("//ul[@id='searchList']/li[1]//a[text()='%MORE_INFO%']");
        $this->assertElementPresent("//ul[@id='searchList']/li[2]//a[text()='%MORE_INFO%']");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a[text()='%MORE_INFO%']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

        $this->searchFor("3570 1002");
        $this->assertEquals("2 %HITS_FOR% \"3570 1002\"", $this->getHeadingText("//h1"));
        //grid view
        $this->selectDropDown("viewOptions", "%grid%");
        $this->assertElementPresent("//ul[@id='searchList']/li[1]//img");
        //fix it in future: mouseOver effect is is implemented via css. Selenium does not support it yet
        //$this->mouseOverAndClick("//ul[@id='searchList']/li[1]//img", "//ul[@id='searchList']/li[1]//a[text()='more Info']");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a[text()='%MORE_INFO%']");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->waitForElement("searchList");
        $this->assertEquals("2 %HITS_FOR% \"3570 1002\"", $this->getHeadingText("//h1"));
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet.
        //$this->mouseOverAndClick("//ul[@id='searchList']/li[2]//img", "//ul[@id='searchList']/li[2]//a[text()='more Info']");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a[text()='%MORE_INFO%']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

        //Check functionality if "Load Variants in Lists" is disabled in admin area
        $this->callShopSC("oxConfig", null, null, array("blLoadVariants" => array("type" => "bool", "value" => "false")));

        $this->clearCache();
        $this->openShop();
        $this->searchFor("3570");
        $this->assertElementPresent("link=Kuyichi Jeans ANNA");
        $this->assertElementPresent("link=%CHOOSE_VARIANT%");
        $this->clickAndWait("link=%CHOOSE_VARIANT% ");
        $this->assertTextPresent("%DETAILS_CHOOSEVARIANT%");
    }

    /**
    * Testing Cookie solution. Is Message appears in frontend about cookies saving
     *
    * @group frontend
    */
    public function testCookieSettingsInFrontend()
    {
        // Check if cookie option is off
        $this->clearCache();
        $this->openShop();
        $this->assertElementNotPresent("cookieNote");

        // Enable cookienotes
        $this->callShopSC('oxConfig', null, null, array('blShowCookiesNotification' => array('type' => 'bool', 'value'=>true)));

        // Check cookie message in frontend
        $this->clearCache();
        $this->openShop();
        $this->assertElementPresent("cookieNote");
        // navigate to link where all cookie is deleted
        $this->clickAndWait("link=%COOKIE_NOTE_DISAGREE%");
        //CMS page not inherited, so for subshop we check just title
        if (!isSUBSHOP) {
            $this->assertTextPresent("You have decided to not accept cookies from our online shop. The cookies have been removed. You can deactivate the usage of cookies in the settings of your browser and visit the online shop with some functional limitations. You can also return to the shop without changing the browser settings and enjoy the full functionality.");
        }
        $this->assertTextPresent("%INFO_ABOUT_COOKIES%");
        // do not turn off browser cookie settings and check in frontend is the message still appears
        $this->clickAndWait("link=%HOME%");
        $this->assertElementPresent("cookieNote");
        // change language in DE and check cookie message
        $this->switchLanguage("Deutsch");
        $this->assertTextNotPresent("%INFO_ABOUT_COOKIES%");
        $this->assertElementNotPresent("cookieNote");
    }

    /**
     * @param $iStatus
     * @return array
     */
    protected function _userData($iStatus)
    {
        $aSubscribedUserData = array(
            'OXSAL' => 'MRS',
            'OXFNAME' => 'name_šÄßüл',
            'OXLNAME' => 'surname_šÄßüл',
            'OXEMAIL' => 'example01@oxid-esales.dev',
            'OXDBOPTIN' => (string)$iStatus
        );

        return $aSubscribedUserData;
    }

    /**
     * Check article list element.
     */
    private function _checkArticleList()
    {
        $this->_checkFilter();
        $this->assertElementPresent("//ul[@id='productList']/li[1]");
        $this->assertElementPresent("//ul[@id='productList']/li[4]");
        $this->assertElementNotPresent("//ul[@id='productList']/li[5]");
    }

    /**
     * Checks filter.
     */
    private function _checkFilter()
    {
        $this->assertElementPresent("viewOptions");
        $this->assertElementPresent("itemsPerPage");
        $this->assertElementPresent("sortItems");
    }

    /**
     * Checks navigation elements.
     *
     * @param $sNavigationId
     * @param $blButtonNextIsVisible
     * @param $blButtonPreviousIsVisible
     */
    private function _checkNavigation($sNavigationId, $blButtonNextIsVisible, $blButtonPreviousIsVisible)
    {
        $this->assertElementPresent("//div[@id='$sNavigationId']//a[text()='1']");
        $this->assertElementPresent("//div[@id='$sNavigationId']//a[text()='2']");
        $this->_checkNextPreviousButtons($sNavigationId, $blButtonNextIsVisible, '%NEXT%');
        $this->_checkNextPreviousButtons($sNavigationId, $blButtonPreviousIsVisible, '%PREVIOUS%');
        $this->assertElementPresent("productList_1");
        $this->assertElementNotPresent("//ul[@id='productList']/li[2]");
    }

    /**
     * Checks if button visible or not.
     *
     * @param $sNavigationId
     * @param $blButtonVisible
     * @param $sButtonName
     */
    private function _checkNextPreviousButtons($sNavigationId, $blButtonVisible, $sButtonName)
    {
        if ($blButtonVisible) {
            $this->assertElementPresent("//div[@id='$sNavigationId']//a[text()='$sButtonName']");
        } else {
            $this->assertElementNotPresent("//div[@id='$sNavigationId']//a[text()='$sButtonName']");
        }
    }

    /**
     * Checks if article in list has buying button.
     *
     * @param null $sButtonText
     */
    private function _checkIfPossibleToBuyItemFromList($sButtonText = null)
    {
        $this->assertElementNotPresent("amountToBasket_productList_1");
        $this->assertElementNotPresent("//form[@name='tobasket.productList_1']//input[@name='aid' and @value='1000']");
        if (is_null($sButtonText)) {
            $this->assertElementNotPresent("//form[@name='tobasket.productList_1']//button");
        } else {
            $this->assertElementNotPresent("//form[@name='tobasket.productList_1']//button[text()='$sButtonText']");
        }
    }

    /**
     * Unsubscribes given email.
     *
     * @param $sEmail
     */
    private function _unsubscribeByEmail($sEmail)
    {
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='Subscribe']");
        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
        $this->assertEquals("%STAY_INFORMED%", $this->getText("//h1"));
        $this->assertEquals("", $this->getValue("newsletterFname"));
        $this->assertEquals("", $this->getValue("newsletterLname"));
        $this->assertEquals("", $this->getValue("newsletterUserName"));
        $this->type("newsletterUserName", $sEmail);
        $this->check("newsletterSubscribeOff");
        $this->assertEquals("0", $this->getValue("subscribeStatus"));
        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
    }

    /**
     * Adds subscription info to fields and asserts.
     */
    private function _typeInfoForSubscription()
    {
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "example01@oxid-esales.dev");
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='%SUBSCRIBE%']");
        $this->assertEquals("%YOU_ARE_HERE%: / %STAY_INFORMED%", $this->getText("breadCrumb"));
        $this->assertEquals("%STAY_INFORMED%", $this->getText("//h1"));
        $this->select("editval[oxuser__oxsal]", "label=%MRS%");
        $this->type("editval[oxuser__oxfname]", "name_šÄßüл");
        $this->type("editval[oxuser__oxlname]", "surname_šÄßüл");
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("editval[oxuser__oxusername]"));
    }

    /**
     * Checks if was sorted correctly in EN language.
     *
     * @param null $sSortType
     */
    private function _checkSortedListEn($sSortType = null)
    {
        $aArticlesTitles = array(
            "Test product 1 [EN] šÄßüл",
            "Test product 3 [EN] šÄßüл",
            "Test product 2 [EN] šÄßüл",
            "Test product 0 [EN] šÄßüл"
        );
        if ($sSortType == 'titleDesc') {
            rsort($aArticlesTitles);
        }

        $this->_runSortAsserts($aArticlesTitles);
    }

    /**
     * Checks if was sorted correctly in DE language.
     *
     * @param null $sSortType
     */
    private function _checkSortedListDe($sSortType = null)
    {
        $aArticlesTitles = array(
            "[DE 4] Test product 0 šÄßüл",
            "[DE 2] Test product 2 šÄßüл",
            "[DE 3] Test product 3 šÄßüл",
            "[DE 1] Test product 1 šÄßüл"
        );
        if ($sSortType == 'titleAsc') {
            sort($aArticlesTitles);
        }

        $this->_runSortAsserts($aArticlesTitles);
    }

    /**
     * Run asserts for given array of articles.
     *
     * @param $aArticlesTitles
     */
    private function _runSortAsserts($aArticlesTitles)
    {
        $this->assertEquals($aArticlesTitles[0], $this->getText("searchList_1"));
        $this->assertEquals($aArticlesTitles[1], $this->getText("searchList_2"));
        $this->assertEquals($aArticlesTitles[2], $this->getText("searchList_3"));
        $this->assertEquals($aArticlesTitles[3], $this->getText("searchList_4"));
    }
}
