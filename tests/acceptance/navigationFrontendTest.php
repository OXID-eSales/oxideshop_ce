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


require_once 'oxidAdditionalSeleniumFunctions.php';

class Acceptance_navigationFrontendTest extends oxidAdditionalSeleniumFunctions
{

    protected function setUp($skipDemoData=false)
    {
        parent::setUp(false);
    }

// ----------------------------- SELENIUM TESTS FOR NEW LAYOUT ----------------------------------

    /**
     * switching languages in frontend
     *
     * @group navigation
     * @group frontend
     */
    public function testFrontendLanguages()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//p[@id='languageTrigger']//*[text()='English']"));
        $this->assertFalse($this->isVisible("languages"));
        $this->assertTrue($this->isTextPresent("Just arrived!"));
        $this->assertTrue($this->isElementPresent("link=About Us"));
        $this->assertFalse($this->isElementPresent("link=Impressum"));
        $this->assertFalse($this->isElementPresent("link=Test category 0 [DE] šÄßüл"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=Test product 0 [EN] šÄßüл"));
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=Test product 1 [EN] šÄßüл"));

        $this->click("languageTrigger");
        $this->waitForItemAppear("languages");
        $this->assertTrue($this->isElementPresent("//ul[@id='languages']/li[@class='active']//*[text()='English']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='languages']/li[2]//*[text()='Deutsch']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='languages']/li[3]//*[text()='English']"));
        $this->click("//ul[@id='languages']/li[2]/a");
        $this->waitForItemDisappear("languages");
        $this->assertTrue($this->isElementPresent("//p[@id='languageTrigger']//*[text()='Deutsch']"));

        $this->assertTrue($this->isElementPresent("link=[DE 1] Test product 1 šÄßüл"));
        $this->clickAndWait("link=Test category 0 [DE] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=[DE 4] Test product 0 šÄßüл"));
        $this->clickAndWait("link=Manufacturer [DE] šÄßüл");
        $this->assertTrue($this->isElementPresent("link=[DE 1] Test product 1 šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=Impressum"));
        $this->clickAndWait("link=Startseite");
        $this->assertTrue($this->isTextPresent("Frisch eingetroffen!"));
        $this->assertFalse($this->isElementPresent("link=About Us"));
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл"));
    }

    /**
     * switching currencies in frontend
     * @group navigation
     * @group main
     */
    public function testFrontendCurrencies()
    {
        $this->openShop();

        //currency checking
        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->assertTrue($this->isElementPresent("//ul[@id='currencies']/li[@class='active']//*[text()='EUR']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='currencies']/li[2]//*[text()='EUR']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='currencies']/li[3]//*[text()='GBP']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='currencies']/li[4]//*[text()='CHF']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='currencies']/li[5]//*[text()='USD']"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));
        $this->click("//ul[@id='currencies']/li[3]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='GBP']"));
        $this->assertEquals("42.83 £ *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[4]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='CHF']"));
        $this->assertEquals("71,63 CHF *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[5]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='USD']"));
        $this->assertEquals("64.97 $ *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));

        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[2]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertTrue($this->isElementPresent("//p[@id='currencyTrigger']//*[text()='EUR']"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));
         #1739
        //currency switch in vendors
        $this->clickAndWait("link=Manufacturer [EN] šÄßüл");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//ul[@id='productList']/li[1]/form/div/div/a"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));
        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[3]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//ul[@id='productList']/li[1]/form/div/div/a"));
        $this->assertEquals("42.83 £ *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));
        $this->click("currencyTrigger");
        $this->waitForItemAppear("currencies");
        $this->click("//ul[@id='currencies']/li[2]/a");
        $this->waitForItemDisappear("currencies");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//ul[@id='productList']/li[1]/form/div/div/a"));
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='productList']/li[1]//span[@class='price']"));
    }

    /**
     * site footer
     * @group navigation
     * @group frontend
     */
    public function testFrontendFooter()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("panel"));
        //checking if delivery note is displayed
        $this->assertTrue($this->isTextPresent("exact:* incl. VAT, plus shipping"));

        //checking if newsletter fields exist. functionality is checked in other test
        $this->assertTrue($this->isElementPresent("//div[@id='panel']/div[1]//label[text()='Newsletter']"));
        $this->assertTrue($this->isElementPresent("//div[@id='panel']/div[1]//input[@name='editval[oxuser__oxusername]']"));
        $this->assertTrue($this->isElementPresent("//div[@id='panel']/div[1]//button[text()='Subscribe']"));

        //SERVICE links
        $this->assertTrue($this->isElementPresent("footerServices"));
        //there are fixed amount of links in here
        $this->assertTrue($this->isElementPresent("//dl[@id='footerServices']//li[10]"));
        $this->assertFalse($this->isElementPresent("//dl[@id='footerServices']//li[11]"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Contact']");
        $this->assertEquals("You are here: / Contact", $this->getText("breadCrumb"));
        $this->assertEquals("Your Company Name", $this->getText("//h1"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Help']");
        $this->assertEquals("You are here: / Help - Main", $this->getText("breadCrumb"));
        $this->assertEquals("Help - Main", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Here, you can insert additional information, further links, user manual etc"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Guestbook']");
        $this->assertEquals("You are here: / Guestbook", $this->getText("breadCrumb"));
        $this->assertEquals("Guestbook", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("link=You have to be logged in to write a guestbook entry."));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Links']");
        $this->assertEquals("You are here: / Links", $this->getText("breadCrumb"));
        $this->assertEquals("Links", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Demo link description [EN] šÄßüл"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->assertEquals("You are here: / Login", $this->getText("breadCrumb"));
        $this->assertEquals("Login", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//input[@name='lgn_usr']"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='NoticeList']");
        $this->assertEquals("You are here: / My Account / My Wish List", $this->getText("breadCrumb"));
        $this->assertEquals("Login", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//input[@name='lgn_usr']"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='My Gift Registry']");
        $this->assertEquals("You are here: / My Account / My Gift Registry", $this->getText("breadCrumb"));
        $this->assertEquals("Login", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("//div[@id='content']//input[@name='lgn_usr']"));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Public Gift Registries']");
        $this->assertEquals("You are here: / Public Gift Registries", $this->getText("breadCrumb"));
        $this->assertEquals("Public Gift Registries", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Search Gift Registry"));
        $this->isElementPresent("search");

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Cart']");
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("The Shopping Cart is empty."));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='About Us']");
        $this->assertEquals("You are here: / About Us", $this->getText("breadCrumb"));
        $this->assertEquals("About Us", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Add provider identification here."));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Terms and Conditions']");
        $this->assertEquals("You are here: / Terms and Conditions", $this->getText("breadCrumb"));
        $this->assertEquals("Terms and Conditions", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Insert your terms and conditions here."));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Privacy Policy']");
        $this->assertEquals("You are here: / Privacy Policy", $this->getText("breadCrumb"));
        $this->assertEquals("Privacy Policy", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Enter your privacy policy here."));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Shipping and Charges']");
        $this->assertEquals("You are here: / Shipping and Charges", $this->getText("breadCrumb"));
        $this->assertEquals("Shipping and Charges", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Add your shipping information and costs here."));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Right of Withdrawal']");
        $this->assertEquals("You are here: / Right of Withdrawal", $this->getText("breadCrumb"));
        $this->assertEquals("Right of Withdrawal", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Insert here the Right of Withdrawal policy"));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='How to order?']");
        $this->assertEquals("You are here: / How to order?", $this->getText("breadCrumb"));
        $this->assertEquals("How to order?", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("Text Example"));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Credits']");
        $this->assertEquals("You are here: / Credits", $this->getText("breadCrumb"));
        $this->assertEquals("Credits", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("What is OXID eShop?"));

        $this->clickAndWait("//dl[@id='footerInformation']//a[text()='Newsletter']");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertEquals("Stay informed!", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("You can unsubscribe any time from the newsletter."));

        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Just arrived!"));

        //MANUFACTURERS links
        $this->assertTrue($this->isElementPresent("footerManufacturers"));
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("You are here: / By Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]//a[@id='productList_1']"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //CATEGORIES links
        $this->assertTrue($this->isElementPresent("footerCategories"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//dl[@id='footerCategories']//li[2]")));
        $this->clickAndWait("//dl[@id='footerCategories']//li[2]/a");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]//a[@id='productList_1']"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
    }

    /**
     * Newsletter ordering
     * @group navigation
     * @group user
     * @group frontend
     */
    public function testFrontendNewsletter()
    {
        $this->openShop();
        //subscribing by entering email
        $this->type("//div[@id='panel']/div[1]//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='Subscribe']");

        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertEquals("Stay informed!", $this->getText("//h1"));

        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("newsletterFname", "name_šÄßüл");
        $this->type("newsletterLname", "surname_šÄßüл");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("newsletterUserName"));
        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("newsletterSubscribeOn"));
        //skipping newsletter username
        $this->type("newsletterUserName", "");
        $this->fireEvent("newsletterUserName", "blur");
        $this->waitForText("Specify a value for this required field");

        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("newsletterSubscribeOn"));

        //incorrect user name
        $this->type("newsletterUserName", "aaa");
        $this->fireEvent("newsletterUserName", "blur");
        $this->waitForText("Please enter a valid e-mail address");
        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("newsletterSubscribeOn"));

        //correct user name
        $this->type("newsletterUserName", "birute01@nfq.lt");
        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("thank you for subscribing to our newsletter."));
        $this->assertTrue($this->isTextPresent("You have just been sent a confirmation e-mail, with which you can activate your subscription."));

        //checking if user was created
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        //because user did not confirm newsletter by email, it is off. setting it on for testing unsubscribe option
        $this->assertEquals("off", $this->getValue("//input[@name='editnews' and @value='1']"));
        $this->check("//input[@name='editnews' and @value='1']");
        $this->clickAndWait("save");
        $this->assertEquals("on", $this->getValue("//input[@name='editnews' and @value='1']"));

        //unsubscribing fake email
        $this->openShop();
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='Subscribe']");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertEquals("Stay informed!", $this->getText("//h1"));
        $this->assertEquals("", $this->getValue("newsletterFname"));
        $this->assertEquals("", $this->getValue("newsletterLname"));
        $this->assertEquals("", $this->getValue("newsletterUserName"));

        $this->type("newsletterUserName", "fake@email.com");
        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->check("newsletterSubscribeOff");

        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Unknown e-mail address!"));

        //unsubscibing newsletter
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='Subscribe']");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertEquals("Stay informed!", $this->getText("//h1"));
        $this->assertEquals("", $this->getValue("newsletterFname"));
        $this->assertEquals("", $this->getValue("newsletterLname"));
        $this->assertEquals("", $this->getValue("newsletterUserName"));

        $this->type("newsletterUserName", "birute01@nfq.lt");
        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->check("newsletterSubscribeOff");

        $this->clickAndWait("newsLetterSubmit");
        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Success!"));
        $this->assertTrue($this->isTextPresent("Your subscription to our Newsletter has been canceled."));

        //checking if newsletter was canceled
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        $this->assertEquals("off", $this->getValue("//input[@name='editnews' and @value='1']"));

    }

    /**
     * Newsletter ordering. Double-opt-in is off, so no email confirmation will be sent
     * @group navigation
     * @group user
     * @group frontend
     */
    public function testFrontendNewsletterDoubleOptInOff()
    {
        //disabling option (Activate Double Opt-In if Users register for Newsletter)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blOrderOptInEmail" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']"));

        //subscribing by entering email
        $this->type("//div[@id='panel']//input[@name='editval[oxuser__oxusername]']", "birute01@nfq.lt");
        $this->clickAndWait("//div[@id='panel']/div[1]//button[text()='Subscribe']");

        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertEquals("Stay informed!", $this->getText("//h1"));
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("editval[oxuser__oxfname]", "name_šÄßüл");
        $this->type("editval[oxuser__oxlname]", "surname_šÄßüл");
        $this->assertEquals("birute01@nfq.lt", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("off", $this->getValue("newsletterSubscribeOff"));
        $this->assertEquals("on", $this->getValue("newsletterSubscribeOn"));
        $this->clickAndWait("newsLetterSubmit");

        $this->assertEquals("You are here: / Stay informed!", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Congratulations and Welcome!"));
        $this->assertTrue($this->isTextPresent("Your subscription to our Newsletter has now been activated."));
        //checking if user was created
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWait("link=Cust No.");
        $this->clickAndWait("nav.last");
        $this->clickAndWaitFrame("link=birute01@nfq.lt", "edit");
        $this->openTab("link=Extended");
        //because double-opt-in is off, setting is set to ON
        $this->assertEquals("on", $this->getValue("//input[@name='editnews' and @value='1']"));
    }


    /**
     * Checking Tags functionality
     * @group navigation
     * @group frontend
     */
    public function testFrontendTags()
    {
        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isElementPresent("tagBox"));
        $this->assertEquals("Tags", $this->getText("//div[@id='tagBox']/h3"));
        $this->assertTrue($this->isElementPresent("//div[@id='tagBox']//a[text()='More...']"));
        $this->clickAndWait("//div[@id='tagBox']//a[text()='More...']");

        $this->assertEquals("You are here: / Tags", $this->getText("breadCrumb"));
        $this->assertEquals("Tags", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("link=[EN]"));
        $this->assertTrue($this->isElementPresent("link=šÄßüл"));
        $this->assertTrue($this->isElementPresent("link=tag"));
        $this->assertTrue($this->isElementPresent("link=1"));
        $this->assertTrue($this->isElementPresent("link=2"));
        $this->assertTrue($this->isElementPresent("link=3"));
        $this->clickAndWait("link=Home");

        $this->clickAndWait("//div[@id='tagBox']//a[text()='tag']");
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("breadCrumb"));
        $this->assertEquals("Tag", $this->getText("//div[@id='content']/h1"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        // go to product 1002 details
        $this->clickAndWait("//ul[@id='productList']/li[3]//a");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("breadCrumb"));
        $this->assertFalse($this->isVisible("tags"));
        $this->assertEquals("Tags", $this->getText("//ul[@id='itemTabs']/li[2]"));
        $this->click("//ul[@id='itemTabs']/li[2]/a");
        $this->waitForItemAppear("tags");
        $this->assertTrue($this->isVisible("link=tag"));

        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));

        //adding new tag
        $this->assertFalse($this->isElementPresent("newTags"));
        $this->click("//ul[@id='itemTabs']//a[text()='Tags']");
        $this->waitForItemAppear("tags");
        $this->click("editTag");
        $this->waitForItemAppear("newTags");
        $this->assertTrue($this->isTextPresent("Add tags:"));
        $this->type("newTags", "new_tag");
        $this->click("saveTag");
        $this->waitForText("new_tag");
        $this->click("cancelTag");
        $this->waitForElement("link=new_tag");
        $this->assertTrue($this->isVisible("link=new_tag"));
        $this->clickAndWait("link=new_tag");
        $this->assertEquals("You are here: / Tags / New_tag", $this->getText("breadCrumb"));
        $this->assertEquals("New_tag", $this->getText("//h1"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertFalse($this->isElementPresent("productList_2"));
    }

    /**
     * Checking Tags functionality
     * @group navigation
     * @group frontend
     */
    public function testFrontendTagsNavigation()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("tagBox"));
        $this->assertEquals("Tags", $this->getText("//div[@id='tagBox']/h3"));
        $this->assertTrue($this->isElementPresent("//div[@id='tagBox']//a[text()='tag']"));

        $this->clickAndWait("//div[@id='tagBox']//a[text()='tag']");
        $this->assertEquals("You are here: / Tags / Tag", $this->getText("breadCrumb"));
        $this->assertEquals("Tag", $this->getText("//h1"));
        //login just to check, if no errors occur (there were some)
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));
        //sorting by title desc
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->selectDropDown("sortItems", "", "li[3]"); //Title desc
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));

        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //displaying 2 items per page
        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->selectDropDown("itemsPerPage", "2");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        //going to page 2
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='Next']");
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("productList_2"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        //sorting by title asc
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->selectDropDown("sortItems", "", "li[2]");
        //after sorting wer are redirected to 1st page
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_2"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
    }

    /**
     * News small box in main page and news page
     * @group navigation
     * @group user
     * @group frontend
     */
    public function testFrontendNewsBox()
    {
        $this->openShop();
        //there are news visible for not logged in users
        $this->assertTrue($this->isElementPresent("newsBox"));
        $this->assertEquals("The latest news... Read more", $this->clearString($this->getText("//div[@id='newsBox']//li[1]")));
        $this->assertFalse($this->isElementPresent("//div[@id='newsBox']//li[2]"));
        //Delete new from database where short description is News
        $sOxid = (oxDb::getDb()->getOne("SELECT oxid FROM oxnews WHERE OXSHORTDESC = 'News'"));
        $this->assertFalse(is_null($sOxid), "Oxid was not found in database for OXSHORTDESC='News'");
        $this->callShopSC("oxNews", "delete", $sOxid);

        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("newsBox"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isElementPresent("newsBox"));
        $this->assertEquals("Test news text 2 [EN] šÄßüл Read more", $this->clearString($this->getText("//div[@id='newsBox']//li[1]")));
        $this->assertTrue($this->isElementPresent("//div[@id='newsBox']//li[1]/a[text()='Read more']"));
        $this->clickAndWait("//div[@id='newsBox']//li[1]/a[text()='Read more']");
        //going to news page by clicking continue link
        $this->assertEquals("You are here: / Latest News and Updates", trim($this->getText("breadCrumb")));
        $this->assertTrue($this->isTextPresent("Latest News and Updates at"));
        $this->assertTrue($this->isTextPresent("02.01.2008 - Test news 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 2 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("01.01.2008 - Test news 1 [EN] šÄßüл"));
        $this->assertTrue($this->isTextPresent("Test news text 1 [EN] šÄßüл"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("newsBox"));

        //loading news not only in start page
        //Option (Load News only on Start Page) is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadNewsOnlyStart" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->assertFalse($this->isElementPresent("newsBox"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertTrue($this->isElementPresent("newsBox"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("newsBox"));

        // Option (Load News) are disabled
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadNews" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->assertFalse($this->isElementPresent("newsBox"));
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->assertFalse($this->isElementPresent("newsBox"));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("newsBox"));
    }

    /**
     * Promotions in frontend. Newest products (just arrived)
     * @group navigation
     * @group frontend
     */
    public function testFrontendPromotionsNewestProducts()
    {
        $this->openShop();
        //Just arrived!
        $this->assertTrue($this->isElementPresent("//div[@id='content']/h2/a[@id='rssNewestProducts']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='newItems']/li[1]//img"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("newItems_1")));
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("//ul[@id='newItems']/li[1]//span[@class='price']")));
        $this->assertTrue($this->isVisible("//ul[@id='newItems']/li[1]//button[text()='To cart']"));
        $this->assertTrue($this->isVisible("incVatMessage"));
        $this->assertEquals("* incl. VAT, plus shipping", $this->clearString($this->getText("incVatMessage")));

        $this->assertTrue($this->isElementPresent("//ul[@id='newItems']/li[2]//img"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("newItems_2")));
        $this->assertEquals("100,00 €", $this->clearString($this->getText("//ul[@id='newItems']/li[2]//span[@class='price']")));
        $this->assertTrue($this->isVisible("//ul[@id='newItems']/li[2]//a[text()='more Info']"));

        $this->assertEquals("RRP 150,00 €", $this->clearString($this->getText("//ul[@id='newItems']/li[2]/form//span[@class='oldPrice']")));

        //link on img
        $this->clickAndWait("//ul[@id='newItems']/li[1]//a[1]");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->clickAndWait("link=Home");

        //link on title
        $this->clickAndWait("newItems_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->clickAndWait("link=Home");

        //click toCart
        $this->clickAndWait("//ul[@id='newItems']/li[1]//button");
    }


    /**
     * Promotions in frontend. Top of the shop (right side)
     * @group navigation
     * @group frontend
     */
    public function testFrontendPromotionsTopOfTheShop()
    {
        $this->openShop();
        $this->assertTrue($this->isElementPresent("topBox"));
        $this->assertEquals("Top of the shop", $this->getHeadingText("//div[@id='topBox']/h3"));
        $this->assertTrue($this->isElementPresent("//div[@id='topBox']/h3/a[@id='rssTopProducts']"));
        $this->assertTrue($this->isElementPresent("//div[@id='topBox']/ul/li[1]//img[@alt='Test product 0 [EN] šÄßüл ']"));
        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->clearString($this->getText("//div[@id='topBox']//li[2]")));
        $this->clickAndWait("//div[@id='topBox']//li[2]/a");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));

        //testing top of the shop in category page
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertFalse($this->isElementPresent("topBox"));
    }

    /**
     * Checking Top Menu Navigation
     * @group navigation
     * @group frontend
     */
    public function testFrontendTopMenu()
    {
        $this->openShop();
        $this->assertTrue($this->isVisible("navigation"));
        $this->assertEquals("Home", $this->clearString($this->getText("//ul[@id='navigation']/li[1]")));
        $this->assertEquals("Test category 0 [EN] šÄßüл »", $this->clearString($this->getText("//ul[@id='navigation']/li[3]/a")));
        $this->assertFalse($this->isElementPresent("//ul[@id='tree']/li"));
        $this->clickAndWait("//ul[@id='navigation']/li[3]/a");

        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("//ul[@id='tree']/li"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent('productList_1'));
        $this->assertTrue($this->isElementPresent('productList_2'));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        $this->clickAndWait("//ul[@id='tree']/li/ul/li/a");
        $this->assertTrue($this->isElementPresent("//ul[@id='tree']/li"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li/ul/li/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent('productList_1'));
        $this->assertTrue($this->isElementPresent('productList_2'));

        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        //more
        $this->clickAndWait("//ul[@id='navigation']/li[4]/a");
        $this->assertTrue($this->isElementPresent("//ul[@id='navigation']/li[4]"));
        $this->assertEquals("More »", $this->getText("//ul[@id='navigation']/li[4]/a"));
        $this->assertFalse($this->isElementPresent("//ul[@id='navigation']/li[5]"));
        //checking on option (Amount of categories that is displayed at top) if is used value = 4
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iTopNaviCatCount" => array("type" => "str", "value" => '4', "module" => "theme:azure")));
        $this->openShop();
        $this->assertTrue($this->isElementPresent("//ul[@id='navigation']/li[5]"));
        $this->assertEquals("More »", $this->getText("//ul[@id='navigation']/li[5]/a"));
        $this->assertFalse($this->isElementPresent("//ul[@id='navigation']/li[6]"));
    }

    /**
     * Category navigation and all elements checking
     * @group navigation
     * @group frontend
     */
    public function testFrontendCategoryFilters()
    {
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");

        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->selectDropDown("itemsPerPage", "1");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        //checking if paging with filters works correctly
        $this->selectDropDown("attributeFilter[testattribute1]", "attr value 1 [EN] šÄßüл");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: attr value 1 [EN] šÄßüл", $this->clearString($this->getText("//div[@id='attributeFilter[testattribute1]']/p")));
        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: Please choose attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent('productList_1'));
        $this->selectDropDown("attributeFilter[testattribute1]", "Please choose");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]//input[@name='aid' and @value='1000']"));

        $this->selectDropDown("attributeFilter[testattribute2]", "attr value 12 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent('productList_1'));

        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: Please choose attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: attr value 12 [EN] šÄßüл Please choose attr value 12 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));

        $this->selectDropDown("attributeFilter[testattribute2]", "Please choose");
        $this->selectDropDown("attributeFilter[testattribute3]", "attr value 3 [EN] šÄßüл");
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]//input[@name='aid' and @value='1000']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: attr value 3 [EN] šÄßüл Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
    }


    /**
     * Category navigation testing
     * @group navigation
     * @group frontend
     */
    public function testFrontendCategoryNavigation()
    {
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->selectDropDown("sortItems", "", "li[2]");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл: Please choose attr value 1 [EN] šÄßüл attr value 11 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute1]")));
        $this->assertEquals("Test attribute 2 [EN] šÄßüл: Please choose attr value 12 [EN] šÄßüл attr value 2 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute2]")));
        $this->assertEquals("Test attribute 3 [EN] šÄßüл: Please choose attr value 3 [EN] šÄßüл", $this->clearString($this->getText("attributeFilter[testattribute3]")));

        $this->assertTrue($this->isElementPresent("//a[@id='rssActiveCategory']"));
        $this->assertEquals("Test category 0 desc [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertEquals("Category 0 long desc [EN] šÄßüл", $this->getText("catLongDesc"));
        $this->assertTrue($this->isElementPresent("//a[@id='moreSubCat_1']/@title"), "attribute title is gone from link. in 450 it was for categories names, that were shortened");
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_1']/@title")));

        //checking items per page
        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->selectDropDown("itemsPerPage", "1");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        //  $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]//span[text()='Test product 0 [EN] šÄßüл']"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        //pagination on top
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='Next']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='1']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
       $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        //testing bottom pagination
        $this->clickAndWait("//div[@id='itemsPagerbottom']/a[text()='2']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPagerbottom']/a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='Next']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->clickAndWait("//div[@id='itemsPager']/a[text()='1']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[2]"));

        $this->assertFalse($this->isElementPresent("amountToBasket_productList_1"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasket.productList_1']//input[@name='aid' and @value='1000']"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasket.productList_1']//button[text()='add to Cart']"));

        $this->selectDropDown("viewOptions", "Double grid");
        $this->assertFalse($this->isElementPresent("amountToBasket_productList_1"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasket.productList_1']//input[@name='aid' and @value='1000']"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasketproductList_1']//button[text()='To cart']"));

        $this->selectDropDown("viewOptions", "Grid");
        $this->assertFalse($this->isElementPresent("amountToBasket_productList_1"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasket.productList_1']//input[@name='aid' and @value='1000']"));
        $this->assertFalse($this->isElementPresent("//form[@name='tobasket.productList_1']//button"));

        $this->clickAndWait("moreSubCat_1");
        $this->assertEquals("You are here: / Test category 0 [EN] šÄßüл / Test category 1 [EN] šÄßüл", $this->getText("breadCrumb"));

    }


    /**
     * sorting, paging and navigation in lists. Sorting is not available for lists
     * @group navigation
     * @group frontend
     */
    public function testFrontendCategorySorting()
    {
        //sorting is enabled
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));

        $this->selectDropDown("viewOptions", "Line");
        $this->selectDropDown("sortItems", "", "li[3]");
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertTrue($this->isElementPresent("productList_2"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        $this->selectDropDown("sortItems", "", "li[2]");
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertTrue($this->isElementPresent("productList_2"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        //disabling option (Users can sort Product Lists)
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blShowSorting" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл »");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertFalse($this->isElementPresent("sortItems"));
    }


    /**
     * price category testing. Note, there are no functionality which would allow to use filters in price category.
     * @group navigation
     * @group frontend
     */
    public function testFrontendPriceCategory()
    {
        $this->openShop();
        $this->clickAndWait("link=price [EN] šÄßüл");
        $this->assertEquals("price [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("price category [EN] šÄßüл", $this->getText("catDesc"));

        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->selectDropDown("itemsPerPage", "2");
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']/a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']/a[text()='Next']"));

        $this->selectDropDown("sortItems", "", "li[4]"); //price asc
        $this->clickAndWait("//div[@id='itemsPager']/a[text()='2']");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->selectDropDown("sortItems", "", "li[2]"); //title desc
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']/a[text()='Previous']"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]//input[@name='aid' and @value='1000']"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));

        $this->selectDropDown("itemsPerPage", "10");
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[6]"));
        $this->assertFalse($this->isElementPresent("itemsPager"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[7]"));
    }

    /**
     * Search in frontend
     * @group navigation
     * @group frontend
     */
    public function testFrontendSearchNavigation()
    {
        $this->openShop();
        //searching for 1 product (using product search field value)
        $this->searchFor("šÄßüл1000");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isElementPresent("rssSearchProducts"));
        $this->assertEquals("1 Hits for \"šÄßüл1000\"", $this->getHeadingText("//h1"));
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("Test product 0 short desc [EN] šÄßüл", $this->clearString($this->getText("//ul[@id='searchList']/li[1]//div[2]/div[2]")));
        $this->assertEquals("50,00 € *", $this->clearString($this->getText("productPrice_searchList_1")));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("2 kg | 25,00 €/kg", $this->getText("productPricePerUnit_searchList_1"));

        $this->type("amountToBasket_searchList_1", "3");
        $this->clickAndWait("toBasket_searchList_1");
        $this->assertEquals("3", $this->getText("//div[@id='miniBasket']/span"));

        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");  //link on img
        $this->assertEquals("You are here: / Search result for \"šÄßüл1000\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("šÄßüл1000", $this->getValue("searchParam"));

        //navigation between search results
        $this->searchFor("100");
        $this->assertTrue($this->isTextPresent("4 Hits for \"100\""));
        $this->clickAndWait("searchList_1");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));

        $this->assertEquals("Delivery time: 1 day", $this->getText("productDeliveryTime"));
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("Delivery time: 1 week", $this->getText("productDeliveryTime"));
        $this->clickAndWait("linkNextArticle");
        //if product is not buyable, no delivery time should be shown
        $this->assertFalse($this->isTextPresent("Delivery time: 1 month"), "This is parent product. It is not buyable, so no delivery time should be shown. works ok in basic templates");
        $this->clickAndWait("linkNextArticle");
        $this->assertEquals("Delivery time: 4 - 9 days", $this->getText("productDeliveryTime"));
    }

    /**
     * Search in frontend
     * @group navigation
     * @group frontend
     */
    public function testFrontendSearchSpecialCases()
    {
        $this->openShop();
        //not existing search
        $this->searchFor("notExisting");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->assertEquals("0 Hits for \"notExisting\"", $this->getHeadingText("//h1"));
        //special chars search
        $this->searchFor("[EN] šÄßüл");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertEquals("4 Hits for \"[EN] šÄßüл\"", $this->getHeadingText("//h1"));
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent('searchList_1'));
        $this->assertTrue($this->isElementPresent('searchList_2'));
        $this->assertTrue($this->isElementPresent('searchList_3'));
        $this->assertTrue($this->isElementPresent('searchList_4'));
        $this->assertFalse($this->isElementPresent("//ul[@id='searchList']/li[5]"));

        //testing #1582
//        $this->executeSql("UPDATE `oxcategories` SET `OXACTIVE`='0', `OXACTIVE_1`='0' WHERE `OXID` = 'testcategory0';");
        $aCategoryParams = array("oxactive" => 0, "oxactive_1" => 0);
        $this->callShopSC("oxCategory", "save", "testcategory0", $aCategoryParams);

        //category is inactive
        $this->clickAndWait("link=Home");
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл"));
        $this->searchFor("1002");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertEquals("You are here: / Search result for \"1002\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Search in frontend. OR and AND separators
     * @group navigation
     * @group frontend
     */
    public function testFrontendSearchOrAnd()
    {
        //AND is used for search keys
        // Checking option ((If serveral Search Terms are entered, all Search Terms have to be found in Search Results (AND). (If this Setting is unchecked, only one Search Term has to be found (OR)) is ON
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blSearchUseAND" => array("type" => "bool", "value" => 'true')));
        $this->openShop();
        $this->searchFor("1000 1001");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("Sorry, no items found."));
        $this->assertEquals("0 Hits for \"1000 1001\"", $this->getHeadingText("//h1"));

        //OR is used for search keys
        //Checking option ((If serveral Search Terms are entered, all Search Terms have to be found in Search Results (AND). (If this Setting is unchecked, only one Search Term has to be found (OR)) is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blSearchUseAND" => array("type" => "bool", "value" => "false")));
        $this->searchFor("1000 1001");
        $this->assertEquals("2 Hits for \"1000 1001\"", $this->getHeadingText("//h1"));
    }

    /**
     * Search in frontend. Checking option: Fields to be considered in Search
     * @group navigation
     * @group frontend
     */
    public function testFrontendSearchConsideredFields()
    {
        //art num is not considered in search
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("aSearchCols" => array("type" => "arr", "value" => serialize(array ("oxtitle", "oxshortdesc", "oxtags" )))));
        $this->openShop();
        $this->searchFor("100");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("0 Hits for \"100\""));

        //art num is considered in search
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("aSearchCols" => array("type" => "arr", "value" => serialize(array("oxtitle", "oxshortdesc", "oxsearchkeys", "oxartnum", "oxtags")))));
        $this->searchFor("100");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("4 Hits for \"100\""));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("searchList_2")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("searchList_3")));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("searchList_4")));
        $this->assertFalse($this->isElementPresent("searchList_5"));

        $this->clickAndWait("searchList_3");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->selectVariant("variants", 1, "var2 [EN] šÄßüл", "Selected combination: var2 [EN] šÄßüл");
        $this->assertEquals("You are here: / Search result for \"100\"", $this->getText("breadCrumb"));
        $this->assertEquals("Test product 2 [EN] šÄßüл var2 [EN] šÄßüл", $this->getText("//h1"));

        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertTrue($this->isTextPresent("4 Hits for \"100\""));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->clearString($this->getText("searchList_1")));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->clearString($this->getText("searchList_2")));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->clearString($this->getText("searchList_3")));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->clearString($this->getText("searchList_4")));
    }

    /**
     * Frontend: various possible errors (expired license, exceeded etc)
     * @group navigation
     * @group frontend
     */
    public function testFrontendPossibleErrors()
    {

    }


    /**
     * Manufacturer navigation and all elements checking
     * @group navigation
     * @group frontend
     */
    public function testFrontendManufacturer()
    {
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("You are here: / By Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertTrue($this->isElementPresent("viewOptions"));
        $this->assertTrue($this->isElementPresent("itemsPerPage"));
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //going to vendor root by path link (you are here)
        $this->clickAndWait("//div[@id='breadCrumb']/a[1]");
        $this->assertEquals("You are here: / By Manufacturer", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("//h1"));
        //going to vendor via menu link
         //  $this->assertTrue($this->isElementPresent("//a[@id='moreSubCat_7']/@title"), "attribute title is gone from link. in 450 it was for manufacturers names, that were shortened");
            $this->assertEquals("Manufacturer [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_8']/@title")));
            $this->clickAndWait("moreSubCat_8");
        $this->assertEquals("You are here: / By Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Manufacturer description [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertTrue($this->isElementPresent("viewOptions"));
        $this->assertTrue($this->isElementPresent("itemsPerPage"));
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //manufacturers tree is disabled
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadManufacturerTree" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->assertFalse($this->isElementPresent("footerManufacturers"));

    }

    /**
     * Distributors navigation and all elements checking
     * @group navigation
     * @group frontend
     */
    public function testFrontendDistributors()
    {
        $this->openShop();
        $this->open(shopURL."index.php?cl=vendorlist&cnid=root");
        $this->clickAndWait("moreSubCat_1");

        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertTrue($this->isElementPresent("viewOptions"));
        $this->assertTrue($this->isElementPresent("itemsPerPage"));
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]")); //this one is empty but needed for design
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //going to vendor root by path link (you are here)
        $this->clickAndWait("//div[@id='breadCrumb']/a[1]");
        $this->assertEquals("You are here: / By Distributor", $this->getText("breadCrumb"));
        //going to vendor via menu link
        $this->assertTrue($this->isElementPresent("//a[@id='moreSubCat_1']/@title"), "attribute title is gone from link. in 450 it was for distributors names, that were shortened");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->clearString($this->getAttribute("//a[@id='moreSubCat_1']/@title")));
        $this->clickAndWait("moreSubCat_1");
        $this->assertEquals("You are here: / By Distributor / Distributor [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getText("//h1"));
        $this->assertEquals("Distributor description [EN] šÄßüл", $this->getText("catDesc"));
        $this->assertTrue($this->isElementPresent("viewOptions"));
        $this->assertTrue($this->isElementPresent("itemsPerPage"));
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[4]")); //this one is empty but needed for design
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[5]"));

        //disabling vendor tree
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadVendorTree" => array("type" => "bool", "value" => "false")));
        $this->openShop();
        $this->assertFalse($this->isElementPresent("footerVendors"));
    }

    /**
     * sorting, paging and navigation in manufacturers
     * @group navigation
     * @group frontend
     */
    public function testFrontendPagingAndNavigationManufacturers()
    {
        // Arunas: Commented some of the assertions that are incorrect.
        // Articles with ID's 1001 and 1002 have MultiDimensional variants so they shouldn't have the input[@name='aid']
        $this->openShop();
        $this->clickAndWait("//dl[@id='footerManufacturers']//a[text()='Manufacturer [EN] šÄßüл']");
        $this->assertEquals("You are here: / By Manufacturer / Manufacturer [EN] šÄßüл", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("itemsPager"));

        //top navigation
        $this->selectDropDown("sortItems", "", "li[4]"); //price asc
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("productList_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("productList_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("productList_3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("productList_4"));

        $this->selectDropDown("itemsPerPage", "2");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1' and @class='page active']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='3']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='2']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='3']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='Next']");
        $this->assertTrue($this->isElementPresent("productList_1"));
        //bottom navigation
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2' and @class='page active']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1' and @class='page active']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));

        $this->assertTrue($this->isElementPresent("productList_1"));
        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1' and @class='page active']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
         $this->assertTrue($this->isElementPresent("productList_1"));

        $this->assertFalse($this->isElementPresent("//ul[@id='productList']/li[3]"));
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='Next']");

        $this->assertFalse($this->isElementPresent("productList_3"));
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='Previous']");

        $this->assertTrue($this->isElementPresent("productList_1"));
    }

    /**
     * sorting, paging and navigation in search
     * @group navigation
     * @group frontend
     */
    public function testFrontendSortingSearch()
    {
        $this->openShop();
        //testing navigation in search
        $this->searchFor("100");
        $this->assertFalse($this->isElementPresent("itemsPager"));

        //top navigation testing
        $this->selectDropDown("sortItems", "", "li[4]"); //price asc
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_2"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("searchList_3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("searchList_4"));
        $this->assertFalse($this->isElementPresent("//ul[@id='searchList']/li[5]"));

        $this->selectDropDown("sortItems", "", "li[2]"); //title asc

        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("searchList_2"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("searchList_3"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("searchList_4"));
        //adding additional column for sorting
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("aSortCols" => array("type" => "arr", "value" => serialize(array("oxtitle", "oxvarminprice", "oxartnum")))));

        //DE lang
        $this->switchLanguage("Deutsch");
        $this->selectDropDown("sortItems", "", "li[3]"); //title desc

        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getText("searchList_2"));
        $this->assertEquals("[DE 3] Test product 3 šÄßüл", $this->getText("searchList_3"));
        $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getText("searchList_4"));

        $this->selectDropDown("sortItems", "", "li[5]"); //artnum asc
        $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getText("searchList_1"));
        $this->assertEquals("[DE 2] Test product 2 šÄßüл", $this->getText("searchList_2"));
        $this->assertEquals("[DE 3] Test product 3 šÄßüл", $this->getText("searchList_3"));
        $this->assertEquals("[DE 1] Test product 1 šÄßüл", $this->getText("searchList_4"));
    }

    /**
     * sorting, paging and navigation in search
     * @group navigation
     * @group frontend
     */
    public function testFrontendPagingAndNavigationSearch()
    {
        $this->openShop();
        //testing navigation in search
        $this->searchFor("100");
        $this->assertEquals("You are here: / Search", $this->getText("breadCrumb"));
        $this->assertFalse($this->isElementPresent("itemsPager"));

        $this->selectDropDown("itemsPerPage", "2");
        $this->assertTrue($this->isElementPresent("viewOptions"));
        $this->assertTrue($this->isElementPresent("itemsPerPage"));
        $this->assertTrue($this->isElementPresent("sortItems"));
        $this->assertTrue($this->isElementPresent("itemsPager"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='searchList']/li[3]"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='Next']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='2' and @class='page active']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPager']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPager']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='searchList']/li[3]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_2"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1000']"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='2']");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1003']"));
        $this->clickAndWait("//div[@id='itemsPager']//a[text()='1']");

        //bottom navigation
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='Next']");
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='1']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='2' and @class='page active']"));
        $this->assertFalse($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Next']"));
        $this->assertTrue($this->isElementPresent("//div[@id='itemsPagerbottom']//a[text()='Previous']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[1]"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[2]"));
        $this->assertFalse($this->isElementPresent("//ul[@id='searchList']/li[3]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("searchList_2"));

        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='Previous']");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1000']"));
        $this->clickAndWait("//div[@id='itemsPagerbottom']//a[text()='2']");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']//input[@name='aid' and @value='1003']"));
    }





    /**
     * Checking Performance options
     * option: Load Selection Lists in Product Lists
     * option: Support Price Modifications by Selection Lists
     * option: Load Selection Lists
     *
     * @group navigation
     * @group frontend
     */
    public function testFrontendPerfOptionsSelectionLists()
    {
        $this->openShop();
        $this->searchFor("1001");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent("selectlistsselector_searchList_1"));
        //page details. selection lists are with prices
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл +1,00 € selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectlistsselector_searchList_1")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл +1,00 € selvar1 [EN] šÄßüл +1,00 € selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл -2,00 € selvar4 [EN] šÄßüл +2%", $this->clearString($this->getText("selectlistsselector_productList_2")));

        //option (Support Price Modifications by Selection Lists) is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfUseSelectlistPrice" => array("type" => "bool", "value" => "false")));

        $this->openShop();
        $this->searchFor("1001");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectlistsselector_searchList_1")));
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("selectlistsselector_productList_2")));

        // loading selection lists in product lists is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadSelectListsInAList" => array("type" => "bool", "value" => "false")));

        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertFalse($this->isElementPresent("selectlistsselector_productList_2"));
        $this->clickAndWait("//ul[@id='productList']/li[2]//a");
        $this->assertEquals("test selection list [EN] šÄßüл: selvar1 [EN] šÄßüл selvar1 [EN] šÄßüл selvar2 [EN] šÄßüл selvar3 [EN] šÄßüл selvar4 [EN] šÄßüл", $this->clearString($this->getText("productSelections")));

        //loading selection lists is OFF
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadSelectLists" => array("type" => "bool", "value" => "false")));

        $this->openShop();
        $this->clickAndWait("link=Test category 0 [EN] šÄßüл");
        $this->selectDropDown("viewOptions", "Line");
        $this->assertFalse($this->isElementPresent("selectlistsselector_productList_2"));
        $this->clickAndWait("//ul[@id='productList']/li[2]//a");
        $this->assertFalse($this->isElementPresent("productSelections"));
    }



    /**
     * Listmania is disabled via performance options
     * @group navigation
     * @group frontend
     */
    public function testFrontendDisabledListmania()
    {
        //Listmania is disabled
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showListmania" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));

        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->searchFor("100");
        $this->assertFalse($this->isElementPresent("//div[@id='recommendationsBox']/h3"));
        $this->assertFalse($this->isElementPresent("//div[@id='recommendationsBox']//ul"));
        $this->assertFalse($this->isElementPresent("searchRecomm"));
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a");
        $this->assertFalse($this->isElementPresent("//div[@id='recommendationsBox']/h3"));
        $this->assertFalse($this->isElementPresent("//div[@id='recommendationsBox']//ul"));
        $this->assertFalse($this->isElementPresent("searchRecomm"));
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertTrue($this->isElementPresent("linkToWishList"));
        $this->assertTrue($this->isElementPresent("linkToNoticeList"));
        $this->assertFalse($this->isElementPresent("recommList"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->assertTrue($this->isElementPresent("//div[@id='sidebar']//a[text()='My Wish List']"));
        $this->assertTrue($this->isElementPresent("//div[@id='sidebar']//a[text()='My Gift Registry']"));
        $this->assertFalse($this->isElementPresent("//div[@id='sidebar']//a[text()='My Listmania List']"));
    }



    /**
     * Checking contact sending
     * @group navigation
     * @group frontend
     */
    public function testFrontendContact()
    {
        //In admin Set option (Installed GDLib Version) if "value" => ""
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iUseGDVersion" => array("type" => "str", "value" => '')));

        $this->openShop();
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Contact']");
        $this->assertEquals("You are here: / Contact", $this->getText("breadCrumb"));
        $this->assertEquals("Your Company Name", $this->getText("//h1"));
        $this->assertEquals("Mr Mrs", $this->clearString($this->getText("editval[oxuser__oxsal]")));
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("editval[oxuser__oxfname]", "first name");
        $this->type("editval[oxuser__oxlname]", "last name");
        $this->type("contactEmail", "birute_test@nfq.lt");
        $this->type("c_subject", "subject");
        $this->type("c_message", "message text");
        $this->type("c_mac", "");
        $this->click("//button[text()='Send']");
        $this->waitForText("Specify a value for this required field");
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("first name", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("last name", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("birute_test@nfq.lt", $this->getValue("contactEmail"));
        $this->assertEquals("subject", $this->getValue("c_subject"));
        $this->assertEquals("message text", $this->getValue("c_message"));
        $this->assertTrue($this->isElementPresent("c_mac"));
        $this->type("c_mac", $this->getText("verifyTextCode"));
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("Thank you."));
        $this->assertEquals("You are here: / Contact", $this->getText("breadCrumb"));
    }



    /**
     * Checking option 'Display Message when Product is added to Cart ' from Core settings -> System
     * @group navigation
     */
    public function testFrontendMessageWhenProductIsAddedToCart()
    {
        $this->openShop();
        $this->assertFalse($this->isElementPresent("newItemMsg"));
        $this->searchFor("1000");
        $this->clickAndWait("//ul[@id='searchList']//button");
        $this->assertFalse($this->isElementPresent("newItemMsg"));

        //displaying message, when product is added to basket
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iNewBasketItemMessage" => array("type" => "select", "value" => '1', "module" => "theme:azure")));

        $this->openShop();
        $this->searchFor("1000");
        $this->click("//ul[@id='searchList']//button");
        $this->waitForItemAppear("newItemMsg");
        $this->assertEquals("New item was added to cart", $this->clearString($this->getText("newItemMsg")));
        $this->waitForTextDisappear("New item was added to cart");
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));

        //display popup when product is added to basket
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iNewBasketItemMessage" => array("type" => "select", "value" => '2', "module" => "theme:azure")));

        $this->openShop();
        $this->searchFor("1000");
        $this->assertFalse($this->isElementPresent("modalbasketFlyout"));
        $this->click("//ul[@id='searchList']//button");
        $this->waitForItemAppear("modalbasketFlyout");

        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[1]"));
        $this->assertEquals("Total 50,00 €", $this->clearString($this->getText("//div[@id='modalbasketFlyout']//p[2]")));
        $this->clickAndWait("//div[@id='modalbasketFlyout']//a[text()='Display Cart']");
        $this->assertEquals("You are here: / View cart", $this->getText("breadCrumb"));
        $this->assertEquals("1", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertEquals("Test product 0 [EN] šÄßüл Art.No. 1000", $this->clearString($this->getText("//tr[@id='cartItem_1']/td[3]")));
        $this->clickAndWait("link=Home");
        $this->searchFor("1003");
        $this->assertFalse($this->isElementPresent("modalbasketFlyout"));
        $this->click("//ul[@id='searchList']//button");
        $this->waitForItemAppear("modalbasketFlyout");
        $this->assertEquals("Test product 0 [EN] šÄßüл 50,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[1]"));
        $this->assertEquals("Test product 3 [EN] šÄßüл 75,00 €", $this->getText("//div[@id='modalbasketFlyout']//ul/li[2]"));
        $this->assertEquals("Total 125,00 €", $this->clearString($this->getText("//div[@id='modalbasketFlyout']//p[2]")));
        $this->clickAndWait("//div[@id='modalbasketFlyout']//a[text()='Checkout']");
        $this->assertEquals("You are here: / Address", $this->getText("breadCrumb"));
        $this->assertEquals("2", $this->getText("//div[@id='miniBasket']/span"));
        $this->clickAndWait("link=Home");
        $this->searchFor("1000");
        $this->assertFalse($this->isElementPresent("modalbasketFlyout"));
        $this->click("//ul[@id='searchList']//button");
        $this->waitForItemAppear("modalbasketFlyout");
        $this->click("//div[@id='modalbasketFlyout']//p[1]/img");
        $this->waitForItemDisappear("modalbasketFlyout");
        $this->assertEquals("3", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertTrue($this->isElementPresent("searchList_1"));
        $this->click("//ul[@id='searchList']//button");
        $this->waitForItemAppear("modalbasketFlyout");
        $this->clickAndWait("//div[@id='modalbasketFlyout']//ul/li[2]/a");
        $this->assertEquals("4", $this->getText("//div[@id='miniBasket']/span"));
        $this->assertFalse($this->isElementPresent("modalbasketFlyout"));
        $this->assertEquals("Test product 3 [EN] šÄßüл", $this->getText("//h1"));
    }

    /**
     * Guestbook spam control
     * @group navigation
     * @group frontend
     */
    public function testFrontendGuestbookSpamProtection()
    {
        //setting spam protection 2 entries per day
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iMaxGBEntriesPerDay" => array("type" => "str", "value" => '2')));

        $this->openShop();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Guestbook']");
        $this->assertEquals("Guestbook", $this->getText("//h1"));
        $this->assertFalse($this->isVisible("rvw_txt"));
        $this->assertTrue($this->isElementPresent("writeNewReview"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        $this->type("rvw_txt", "guestbook entry No. 1");
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 1"));
        $this->assertTrue($this->isElementPresent("writeNewReview"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        $this->type("rvw_txt", "guestbook entry No. 2");
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 2"));
        $this->assertFalse($this->isElementPresent("writeNewReview"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Guestbook']");
        $this->assertFalse($this->isElementPresent("writeNewReview"));

        //increasing guestbook entries limit
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("iMaxGBEntriesPerDay" => array("type" => "str", "value" => '10')));

        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Guestbook']");
        $this->assertEquals("Guestbook", $this->getText("//h1"));
        $this->assertTrue($this->isElementPresent("writeNewReview"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        $this->type("rvw_txt", "guestbook entry No. 3");
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 3"));
        $this->assertTrue($this->isElementPresent("writeNewReview"));
        $this->click("writeNewReview");
        $this->waitForItemAppear("rvw_txt");
        $this->type("rvw_txt", "guestbook entry No. 4");
        $this->clickAndWait("//button[text()='Send']");
        $this->assertTrue($this->isTextPresent("guestbook entry No. 4"));
        $this->assertTrue($this->isElementPresent("writeNewReview"));
    }



    /**
     * Checking CMS pages marked as categories
     * @group navigation
     * @group frontend
     */
    public function testFrontendCmsAsCategories()
    {
        //activating CMS pages as categories
        $this->executeSql("UPDATE `oxcontents` SET `OXACTIVE`=1, `OXACTIVE_1`=1 WHERE `OXID` = 'testcontent1' OR `OXID` = 'testcontent2' OR `OXID` = 'oxsubshopcontent1' OR `OXID` = 'oxsubshopcontent2'");
        //cms as root category
        $this->openShop();
        $this->assertEquals("[last] [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='navigation']/li[3]")));
        $this->clickAndWait("//ul[@id='navigation']/li[3]//a");
        $this->assertEquals("You are here: / [last] [EN] content šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("content [EN] 1 šÄßüл"));

        //cms as subcategory
        $this->assertEquals("[last] [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='navigation']/li[3]")));
        $this->clickAndWait("//ul[@id='navigation']/li[4]/a");
        $this->assertEquals("Category overview", $this->getHeadingText("//h1"));
        $this->clickAndWait("moreSubCat_2");
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getHeadingText("//h1"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li[1]")));
        $this->assertTrue($this->isElementPresent("//a[@id='moreSubCms_1_1']/@title"), "attribute title is gone from link. in 450 it was for category names, that were shortened");
        $this->assertEquals("3 [EN] content šÄßüл", $this->getAttribute("//a[@id='moreSubCms_1_1']/@title"), "bug from Mantis #495");
        $this->clickAndWait("moreSubCms_1_1");
        $this->assertEquals("You are here: / 3 [EN] content šÄßüл", $this->getText("breadCrumb"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->getText("//h1"));
        $this->assertTrue($this->isTextPresent("content [EN] last šÄßüл"));
        $this->assertEquals("3 [EN] content šÄßüл", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li[1]")));
    }

    /**
     * Promotions in frontend. Categories
     * @group navigation
     * @group frontend
     */
    public function testFrontendPromotionsCategories()
    {
        $this->openShop();
        //Categories
        $this->assertTrue($this->isElementPresent("//div[@id='specCatBox']/h2"));
        $this->assertEquals("Wakeboards", $this->getText("//div[@id='specCatBox']/h2"));
        //fix it in future: mouseOver effect is implemented via css. selenium does not support it yet
        $this->clickAndWait("//div[@id='specCatBox']/a");
        $this->assertEquals("You are here: / Wakeboarding / Wakeboards", $this->getText("breadCrumb"));
        $this->assertEquals("Wakeboards", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isElementPresent("//ul[@id='productList']/li[1]"));
    }

    /**
     * Checking Performance options
     * @group navigation
     * @group frontend
     */
    public function testFrontendPerfOptions1()
    {
        $this->openShop();
        $this->assertEquals("50,00 € *", $this->getText("//ul[@id='newItems']/li[1]//span[@class='price']"));
        $this->assertEquals("50,00 €", $this->getText("//div[@id='topBox']/ul/li[2]//strong"));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfShowActionCatArticleCnt" => array("type" => "bool", "value" => true)));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadDelivery" => array("type" => "bool", "value" => false)));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadPriceForAddList" => array("type" => "bool", "value" => false)));

        $this->openShop();
        $this->assertFalse($this->isElementPresent("//ul[@id='newItems']/li[1]//strong[text()='50,00 € *']"));
        $this->assertFalse($this->isElementPresent("//div[@id='topBox']/ul/li[1]//strong[text()='50,00 €']"));
        $this->assertEquals("Test category 0 [EN] šÄßüл (2) »", $this->getText("//ul[@id='navigation']/li[3]"));
        $this->clickAndWait("//ul[@id='navigation']/li[3]/a");
        $this->assertEquals("Test category 0 [EN] šÄßüл (2)", $this->clearString($this->getText("//ul[@id='tree']/li[1]/a")));
        $this->assertEquals("Test category 1 [EN] šÄßüл (2)", $this->clearString($this->getText("//ul[@id='tree']/li[1]/ul/li")));
        $this->assertEquals("Test category 1 [EN] šÄßüл", $this->getAttribute("//a[@id='moreSubCat_1']@title"));
        $this->assertEquals("(2)", substr($this->getText("moreSubCat_1"),-3));
        $this->clickAndWait("productList_2");
        $this->clickAndWait("toBasket");
        $this->openBasket();
        $this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->clickAndWait("//button[text()='Continue to Next Step']");
        $this->selectAndWait("sShipSet", "label=Example Set1: UPS 48 hours");
        $this->assertFalse($this->isElementPresent("shipSetCost"));

        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadReviews" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadPrice" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadSimilar" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadCrossselling" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadAccessoires" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_showCompareList" => array("type" => "bool", "value" => "false", "module" => "theme:azure")));

        $this->openShop();
        $this->searchFor("100");
        $this->clickAndWait("//ul[@id='searchList']/li[3]//a");
        $this->assertFalse($this->isTextPresent("review for parent product šÄßüл"));
        $this->clickAndWait("linkPrevArticle");
        $this->clickAndWait("linkPrevArticle");
        $this->assertFalse($this->isElementPresent("productPrice"));
        $this->assertFalse($this->isElementPresent("similar"));
        $this->assertFalse($this->isElementPresent("cross"));
        $this->assertFalse($this->isElementPresent("accessories"));
        //$this->loginInFrontend("birute_test@nfq.lt", "useruser");
        $this->click("productLinks");
        $this->waitForItemAppear("suggest");
        $this->assertFalse($this->isElementPresent("addToCompare"));
        $this->clickAndWait("//dl[@id='footerServices']//a[text()='Account']");
        $this->assertFalse($this->isElementPresent("link=My Product Comparison"));

        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadAktion" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadCurrency" => array("type" => "bool", "value" => "false")));
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("bl_perfLoadLanguages" => array("type" => "bool", "value" => "false")));

        $this->openShop();
        $this->assertFalse($this->isElementPresent("link=Test category 0 [EN] šÄßüл (2)"));
        $this->assertFalse($this->isElementPresent("footerCategories"));
        $this->assertFalse($this->isElementPresent("titleBargain_1"));
        $this->assertFalse($this->isElementPresent("//div[@id='specCatBox']/h2"));
        $this->assertFalse($this->isElementPresent("topBox"));
        $this->assertFalse($this->isElementPresent("newItems"));
        $this->assertFalse($this->isElementPresent("currencyTrigger"));
        $this->assertFalse($this->isElementPresent("languageTrigger"));
    }

    /**
     * Promotions in frontend. week's special
     * @group navigation
     * @group frontend
     */
    public function testFrontendPromotionsWeekSpecial()
    {
        //buyable product as bargain
        $this->executeSql("UPDATE `oxactions2article` SET `OXARTID` = '1000'  WHERE `OXACTIONID` = 'oxbargain';");
        $this->openShop();
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//div[@id='specBox']/div/a"));
        $this->clickAndWait("//div[@id='specBox']/div/a");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=Home");
        $this->assertEquals("50,00 € *", $this->getText("//div[@id='priceBargain_1']//span"));
        $this->assertEquals("add to Cart", $this->clearString($this->getText("//div[@id='priceBargain_1']//a")));
        $this->clickAndWait("//div[@id='priceBargain_1']//a");
        $this->openBasket();
        $this->assertTrue($this->isElementPresent("//tr[@id='cartItem_1']//a/b[text()='Test product 0 [EN] šÄßüл']"));

        //configurable product as bargain
        $this->executeSql("UPDATE `oxactions2article` SET `OXARTID` = '1001'  WHERE `OXACTIONID` = 'oxbargain';");
        $this->clickAndWait("link=Home");
        $this->assertEquals("Week's Special", $this->getHeadingText("//div[@id='specBox']//h3"));
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//div[@id='specBox']/div/a"));
        $this->clickAndWait("//div[@id='specBox']/div/a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("link=Home");
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet
        $this->assertEquals("RRP 150,00 €", $this->getText("//div[@id='priceBargain_1']//span"));
        $this->assertEquals("100,00 €", $this->getText("//div[@id='priceBargain_1']//span[2]"));
        $this->assertEquals("more Info", $this->clearString($this->getText("//div[@id='priceBargain_1']//a")));
        $this->clickAndWait("//div[@id='priceBargain_1']//a");
        $this->assertEquals("Test product 1 [EN] šÄßüл", $this->getText("//h1"));


    }

    /**
     * checking if variants are displayed correctly in list
     * @group navigation
     * @group frontend
     */
    public function testVariantsInLists()
    {
        $this->openShop();
        $this->searchFor("3570 1002");
        $this->assertEquals("2 Hits for \"3570 1002\"", $this->getHeadingText("//h1"));
        $this->assertTrue($this->isElementPresent("searchList_1"));
        $this->assertTrue($this->isElementPresent("searchList_2"));
        $this->assertFalse($this->isElementPresent("searchList_3"));
        //double grid view
        $this->assertTrue($this->isElementPresent("//form[@name='tobasketsearchList_1']//a[text()='more Info']"));
        $this->assertTrue($this->isElementPresent("//form[@name='tobasketsearchList_2']//a[text()='more Info']"));
        $this->clickAndWait("//form[@name='tobasketsearchList_1']//a[text()='more Info']");

        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->waitForElement("searchList");
        $this->assertEquals("2 Hits for \"3570 1002\"", $this->getHeadingText("//h1"));
        $this->clickAndWait("//form[@name='tobasketsearchList_2']//a[text()='more Info']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

        $this->searchFor("3570 1002");
        $this->assertEquals("2 Hits for \"3570 1002\"", $this->getHeadingText("//h1"));
        //line view
        $this->selectDropDown("viewOptions", "Line");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[1]//a[text()='more Info']"));
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[2]//a[text()='more Info']"));
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a[text()='more Info']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

        $this->searchFor("3570 1002");
        $this->assertEquals("2 Hits for \"3570 1002\"", $this->getHeadingText("//h1"));
        //grid view
        $this->selectDropDown("viewOptions", "Grid");
        $this->assertTrue($this->isElementPresent("//ul[@id='searchList']/li[1]//img"));
        //fix it in future: mouseOver effect is is implemented via css. Selenium does not support it yet
        //$this->mouseOverAndClick("//ul[@id='searchList']/li[1]//img", "//ul[@id='searchList']/li[1]//a[text()='more Info']");
        $this->clickAndWait("//ul[@id='searchList']/li[1]//a[text()='more Info']");
        $this->assertEquals("Test product 2 [EN] šÄßüл", $this->getText("//h1"));
        $this->clickAndWait("//div[@id='overviewLink']/a");
        $this->waitForElement("searchList");
        $this->assertEquals("2 Hits for \"3570 1002\"", $this->getHeadingText("//h1"));
        //fix it in future: mouseOver effect is implemented via css. Selenium does not support it yet.
        //$this->mouseOverAndClick("//ul[@id='searchList']/li[2]//img", "//ul[@id='searchList']/li[2]//a[text()='more Info']");
        $this->clickAndWait("//ul[@id='searchList']/li[2]//a[text()='more Info']");
        $this->assertEquals("Kuyichi Jeans ANNA", $this->getText("//h1"));

         //Check functionality if "Load Variants in Lists" is disabled in admin area
        $this->callShopSC("oxConfig", "saveShopConfVar", null, array("blLoadVariants" => array("type" => "bool", "value" => "false")));

        $this->openShop();
        $this->searchFor("3570");
        $this->assertTrue($this->isElementPresent("link=Kuyichi Jeans ANNA"));
        $this->assertTrue($this->isElementPresent("link=Choose variant"));
        $this->clickAndWait("link=Choose variant ");
        $this->assertTrue($this->isTextPresent("Please select a variant"));
    }

    /**
    * Testing Cookie solution. Is Message appears in frontend about cookies saving
    * @group navigation
    */
    public function testCookieSettingsInFrontend()
    {
        //check if cookie option is off
        $this->openShop();
        $this->assertFalse($this->isElementPresent("cookieNote"));
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("link=Settings");
        $this->click("link=Other settings");
        $this->assertEquals("false", $this->getValue("confbools[blShowCookiesNotification]"));
        $this->assertFalse($this->isChecked("confbools[blShowCookiesNotification]"));
        //turn on cookie option
        $this->check("//input[@name='confbools[blShowCookiesNotification]' and @value='true']");
        $this->clickAndWait("save");
         //check cookie message in frontend
        $this->openShop();
        $this->assertTrue($this->isElementPresent("cookieNote"));
       // navigate to link where all cookie is deled
        $this->clickAndWait("link=If you do not agree, please click here.");
        $this->assertTrue($this->isTextPresent("You have decided to not accept cookies from our online shop. The cookies have been removed. You can deactivate the usage of cookies in the settings of your browser and visit the online shop with some functional limitations. You can also return to the shop without changing the browser settings and enjoy the full functionality."));
        $this->assertTrue($this->isTextPresent("Information about Cookies"));
        // do not turn off browser cookie settings and check in frontend is the message still apears
        $this->clickAndWait("link=Home");
        $this->assertTrue($this->isElementPresent("cookieNote"));
        // change language in DE and check cookie message
        $this->switchLanguage("Deutsch");
        $this->assertFalse($this->isElementPresent("cookieNote"));
    }
}