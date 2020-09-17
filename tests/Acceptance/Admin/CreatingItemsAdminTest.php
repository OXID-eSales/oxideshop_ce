<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Creating and deleting items. */
class CreatingItemsAdminTest extends AdminTestCase
{

    /**
     * creating Countries
     *
     * @group creatingitems
     */
    public function testCreateCountry()
    {
        $this->loginAdmin("Master Settings", "Countries");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->changeListSorting("link=Country");
        $this->openListItem("link=1 DE test Country šÄßüл");
        $this->assertEquals("1 DE test Country šÄßüл", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("[last] DE test Country desc", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("[last] EN test Country šÄßüл", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("1 EN test Country desc šÄßüл", $this->getValue("editval[oxcountry__oxshortdesc]"));
        //creating
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxcountry__oxactive]"));
        $this->check("editval[oxcountry__oxactive]");
        $this->type("editval[oxcountry__oxtitle]", "create_delete country [EN]_šÄßüл");
        $this->type("editval[oxcountry__oxshortdesc]", "create_delete country desc [EN]_šÄßüл");
        $this->type("editval[oxcountry__oxisoalpha2]", "1Äßüл");
        $this->type("editval[oxcountry__oxisoalpha3]", "1Äßüл");
        $this->type("editval[oxcountry__oxunnum3]", "1Äßüл");
        $this->type("editval[oxcountry__oxorder]", "0");
        $this->assertEquals('0', $this->getValue("editval[oxcountry__oxvatstatus]"));
        $this->type("editval[oxcountry__oxlongdesc]", "create_delete description [EN]_šÄßüл");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->assertEquals("Save", $this->getValue("saveArticle"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("on", $this->getValue("editval[oxcountry__oxactive]"));
        $this->assertEquals("create_delete country [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("create_delete country desc [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->assertEquals("1Ä", $this->getValue("editval[oxcountry__oxisoalpha2]"));
        $this->assertEquals("1Äß", $this->getValue("editval[oxcountry__oxisoalpha3]"));
        $this->assertEquals("1Äß", $this->getValue("editval[oxcountry__oxunnum3]"));
        $this->assertEquals("0", $this->getValue("editval[oxcountry__oxorder]"));
        $this->assertEquals("create_delete description [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxlongdesc]"));
        $this->type("editval[oxcountry__oxlongdesc]", "create_delete description [DE]");
        $this->type("editval[oxcountry__oxtitle]", "create_delete country [DE]");
        $this->type("editval[oxcountry__oxshortdesc]", "create_delete country desc [DE]");
        $this->type("editval[oxcountry__oxisoalpha2]", "22");
        $this->type("editval[oxcountry__oxisoalpha3]", "222");
        $this->type("editval[oxcountry__oxunnum3]", "333");
        $this->type("editval[oxcountry__oxorder]", "1");
        $this->check("//input[@name='editval[oxcountry__oxvatstatus]' and @value='1']");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertEquals("on", $this->getValue("editval[oxcountry__oxactive]"));
        $this->assertEquals("create_delete country [DE]", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("create_delete country desc [DE]", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->assertEquals("22", $this->getValue("editval[oxcountry__oxisoalpha2]"));
        $this->assertEquals("222", $this->getValue("editval[oxcountry__oxisoalpha3]"));
        $this->assertEquals("333", $this->getValue("editval[oxcountry__oxunnum3]"));
        $this->assertEquals("1", $this->getValue("editval[oxcountry__oxorder]"));
        $this->assertEquals("1", $this->getValue("//input[@name='editval[oxcountry__oxvatstatus]' and @value='1']"));
        $this->assertEquals("create_delete description [DE]", $this->getValue("editval[oxcountry__oxlongdesc]"));
        $this->changeAdminEditLanguage('English', 'test_editlanguage');
        $this->assertEquals("create_delete country [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("create_delete country desc [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->assertEquals("22", $this->getValue("editval[oxcountry__oxisoalpha2]"));
        $this->assertEquals("222", $this->getValue("editval[oxcountry__oxisoalpha3]"));
        $this->assertEquals("333", $this->getValue("editval[oxcountry__oxunnum3]"));
        $this->assertEquals("1", $this->getValue("editval[oxcountry__oxorder]"));
        $this->assertEquals("1", $this->getValue("//input[@name='editval[oxcountry__oxvatstatus]' and @value='1']"));
        $this->assertEquals("create_delete description [EN]_šÄßüл", $this->getValue("editval[oxcountry__oxlongdesc]"));
        $this->uncheck("editval[oxcountry__oxactive]");
        $this->type("editval[oxcountry__oxtitle]", "create_delete1 country [EN]");
        $this->type("editval[oxcountry__oxshortdesc]", "create_delete1 country desc [EN]");
        $this->type("editval[oxcountry__oxlongdesc]", "create_delete1 description [EN]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->changeAdminEditLanguage('Deutsch', 'test_editlanguage');
        $this->assertEquals("off", $this->getValue("editval[oxcountry__oxactive]"));
        $this->assertEquals("create_delete country [DE]", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("create_delete country desc [DE]", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->assertEquals("create_delete description [DE]", $this->getValue("editval[oxcountry__oxlongdesc]"));
        $this->changeAdminEditLanguage('English', 'test_editlanguage');
        $this->assertEquals("create_delete1 country [EN]", $this->getValue("editval[oxcountry__oxtitle]"));
        $this->assertEquals("create_delete1 country desc [EN]", $this->getValue("editval[oxcountry__oxshortdesc]"));
        $this->assertEquals("create_delete1 description [EN]", $this->getValue("editval[oxcountry__oxlongdesc]"));
        //checking if created item can be found
        $this->frame("list");
        $this->type("where[oxcountry][oxtitle]", "create_delete");
        $this->clickAndWaitFrame("submitit");
        $this->assertEquals("create_delete1 country [EN]", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[2]");
    }

    /**
     * creating Distributors
     *
     * @group creatingitems
     */
    public function testCreateDistributors()
    {
        $this->loginAdmin("Master Settings", "Distributors");
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=1 DE distributor šÄßüл", "editval[oxvendor__oxtitle]");
        $this->assertEquals("1 DE distributor šÄßüл", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("[last] EN distributor šÄßüл", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxvendor__oxactive]"));
        $this->check("editval[oxvendor__oxactive]");
        $this->type("editval[oxvendor__oxtitle]", "create_delete distributor [EN]_šÄßüл");
        $this->type("editval[oxvendor__oxshortdesc]", "short desc [EN]_šÄßüл");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertElementPresent("save");
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->assertElementPresent("saveArticle");
        $this->assertEquals("Save", $this->getValue("saveArticle"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxvendor__oxtitle]", "create_delete distributor [DE]");
        $this->type("editval[oxvendor__oxshortdesc]", "short desc [DE]");
        $this->assertEquals("on", $this->getValue("editval[oxvendor__oxactive]"));
        $this->uncheck("editval[oxvendor__oxactive]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("editval[oxvendor__oxactive]"));
        $this->assertEquals("create_delete distributor [EN]_šÄßüл", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("short desc [EN]_šÄßüл", $this->getValue("editval[oxvendor__oxshortdesc]"));
        $this->type("editval[oxvendor__oxtitle]", "create_delete distributor1 [EN]");
        $this->type("editval[oxvendor__oxshortdesc]", "short desc1 [EN]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("off", $this->getValue("editval[oxvendor__oxactive]"));
        $this->assertEquals("create_delete distributor [DE]", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("short desc [DE]", $this->getValue("editval[oxvendor__oxshortdesc]"));
        $this->check("editval[oxvendor__oxactive]");
        $this->type("editval[oxvendor__oxtitle]", "create_delete distributor1 [DE]");
        $this->type("editval[oxvendor__oxshortdesc]", "short desc1 [DE]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("editval[oxvendor__oxactive]"));
        $this->assertEquals("create_delete distributor1 [EN]", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("short desc1 [EN]", $this->getValue("editval[oxvendor__oxshortdesc]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("editval[oxvendor__oxactive]"));
        $this->assertEquals("create_delete distributor1 [DE]", $this->getValue("editval[oxvendor__oxtitle]"));
        $this->assertEquals("short desc1 [DE]", $this->getValue("editval[oxvendor__oxshortdesc]"));

        $this->checkTabs(array("SEO", 'Mall'));
        $this->frame("list");
        $this->type("where[oxvendor][oxtitle]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete distributor1 [EN]", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[2]");
    }

    /**
     * creating Manufacturers
     *
     * @group creatingitems
     */
    public function testCreateManufacturers()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=1 DE manufacturer šÄßüл");
        $this->assertEquals("1 DE manufacturer šÄßüл", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("[last] EN manufacturer šÄßüл", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->check("editval[oxmanufacturers__oxactive]");
        $this->type("editval[oxmanufacturers__oxtitle]", "create_delete manufacturer [EN]_šÄßüл");
        $this->type("editval[oxmanufacturers__oxshortdesc]", "short desc [EN]_šÄßüл");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertElementPresent("save");
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->assertElementPresent("saveArticle");
        $this->assertEquals("Save", $this->getValue("saveArticle"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxmanufacturers__oxtitle]", "create_delete manufacturer [DE]");
        $this->type("editval[oxmanufacturers__oxshortdesc]", "short desc [DE]");
        $this->assertEquals("on", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->uncheck("editval[oxmanufacturers__oxactive]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->assertEquals("create_delete manufacturer [EN]_šÄßüл", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("short desc [EN]_šÄßüл", $this->getValue("editval[oxmanufacturers__oxshortdesc]"));
        $this->type("editval[oxmanufacturers__oxtitle]", "create_delete manufacturer1 [EN]");
        $this->type("editval[oxmanufacturers__oxshortdesc]", "short desc1 [EN]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("off", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->assertEquals("create_delete manufacturer [DE]", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("short desc [DE]", $this->getValue("editval[oxmanufacturers__oxshortdesc]"));
        $this->check("editval[oxmanufacturers__oxactive]");
        $this->type("editval[oxmanufacturers__oxtitle]", "create_delete manufacturer1 [DE]");
        $this->type("editval[oxmanufacturers__oxshortdesc]", "short desc1 [DE]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=English", "editval[oxmanufacturers__oxactive]");
        $this->assertEquals("on", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->assertEquals("create_delete manufacturer1 [EN]", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("short desc1 [EN]", $this->getValue("editval[oxmanufacturers__oxshortdesc]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("editval[oxmanufacturers__oxactive]"));
        $this->assertEquals("create_delete manufacturer1 [DE]", $this->getValue("editval[oxmanufacturers__oxtitle]"));
        $this->assertEquals("short desc1 [DE]", $this->getValue("editval[oxmanufacturers__oxshortdesc]"));

        $this->checkTabs(array("SEO", 'Mall'));
        $this->frame("list");
        $this->type("where[oxmanufacturers][oxtitle]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete manufacturer1 [EN]", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[2]");
    }

    /**
     * creating Languages
     *
     * @group creatingitems
     */
    public function testCreateLanguages()
    {
        $this->loginAdmin("Master Settings", "Languages");
        $this->assertElementNotPresent("//tr[@id='row.3']");
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[active]"));
        $this->check("editval[active]");
        $this->type("editval[abbr]", "aa_bb");
        $this->type("editval[desc]", "Language_šÄßüл");
        $this->assertEquals("off", $this->getValue("editval[default]"));
        $this->check("editval[default]");
        $this->type("editval[baseurl]", "http://base.url");
        $this->type("editval[basesslurl]", "https://base.url");
        $this->type("editval[sort]", ""); //leaving empty
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertTextPresent("Please don't forget to update the database views under Service->Tools");
        $this->assertTextPresent("Attention: No language files were found in templates dir for selected language!");
        $this->frame("list");
        $this->assertEquals("aa_bb", $this->getText("//tr[@id='row.3']/td[2]"));
        $this->assertEquals("Language_šÄßüл", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.1']/td[3]/div/a/b");
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]/div/a/b");
        //$this->assertElementNotPresent("//tr[@id='row.3']/td[3]/div/a/b");
        $this->assertElementPresent("//tr[@id='row.3']/td[3]/div/a/b");
        $this->frame("edit");
        $this->assertEquals("aa_bb", $this->getValue("editval[abbr]"));
        $this->assertEquals("Language_šÄßüл", $this->getValue("editval[desc]"));
        $this->assertEquals("http://base.url", $this->getValue("editval[baseurl]"));
        $this->assertEquals("https://base.url", $this->getValue("editval[basesslurl]"));
        $this->assertEquals("on", $this->getValue("editval[active]"));
        $this->assertEquals("on", $this->getValue("editval[default]"));
        $this->assertEquals("99999", $this->getValue("editval[sort]")); //default filled value
        $this->assertEquals("2", $this->getText("//form[@id='myedit']/table/tbody/tr/td/table/tbody/tr[7]/td[2]"));
        $this->uncheck("editval[active]");
        $this->uncheck("editval[default]");
        $this->type("editval[abbr]", "bb_aa");
        $this->type("editval[desc]", "Language_šÄßüл1");
        $this->type("editval[baseurl]", "http://base1.url");
        $this->type("editval[basesslurl]", "https://base1.url");
        $this->type("editval[sort]", "1");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertEquals("off", $this->getValue("editval[active]"));
        $this->assertEquals("on", $this->getValue("editval[default]")); //default lang cant be unchecked.
        $this->assertEquals("bb_aa", $this->getValue("editval[abbr]"));
        $this->assertEquals("Language_šÄßüл1", $this->getValue("editval[desc]"));
        $this->assertEquals("http://base1.url", $this->getValue("editval[baseurl]"));
        $this->assertEquals("https://base1.url", $this->getValue("editval[basesslurl]"));
        $this->assertEquals("1", $this->getValue("editval[sort]"));
        //creating another language with existing Abbreviation
        $this->clickCreateNewItem();
        $this->type("editval[abbr]", "bb_aa");
        $this->type("editval[desc]", "language_2");
        $this->clickAndWaitFrame("saveArticle", "edit");
        $this->assertTextPresent("Error: a language with this abbreviation already exists!");
        $this->assertEquals("", $this->getValue("editval[abbr]"));
        $this->assertEquals("", $this->getValue("editval[desc]"));
        $this->openListItem("link=Language_šÄßüл1");
        $this->assertEquals("off", $this->getValue("editval[active]"));
        $this->assertEquals("on", $this->getValue("editval[default]")); //default lang cant be unchecked.
        $this->assertEquals("bb_aa", $this->getValue("editval[abbr]"));
        $this->assertEquals("Language_šÄßüл1", $this->getValue("editval[desc]"));
        $this->assertEquals("http://base1.url", $this->getValue("editval[baseurl]"));
        $this->assertEquals("https://base1.url", $this->getValue("editval[basesslurl]"));
        $this->assertEquals("1", $this->getValue("editval[sort]"));
        $this->frame("list");
        $this->assertEquals("Language_šÄßüл1", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("Deutsch", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("English", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->assertElementPresent("//tr[@id='row.1']/td[3]/div/a/b");
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]/div/a/b");
        $this->assertElementNotPresent("//tr[@id='row.3']/td[3]/div/a/b");
        //sorting languages
        $this->clickAndWait("link=Language");
        $this->assertEquals("Deutsch", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("English", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertEquals("Language_šÄßüл1", $this->getText("//tr[@id='row.3']/td[3]"));
        $this->clickAndWait("link=Abbreviation");
        $this->assertEquals("bb_aa", $this->getText("//tr[@id='row.1']/td[2]/div"));
        $this->assertEquals("de", $this->getText("//tr[@id='row.2']/td[2]/div"));
        $this->assertEquals("en", $this->getText("//tr[@id='row.3']/td[2]/div"));
        $this->clickAndWaitFrame("link=Active");
        $this->assertEquals("", $this->getText("//tr[@id='row.2']/td[1]/div"));
        $this->assertElementPresent("//tr[@id='row.1']/td[@class='listitem active']");
        $this->assertElementPresent("//tr[@id='row.2']/td[@class='listitem2 active']");
        $this->assertElementPresent("//tr[@id='row.3']/td[@class='listitem4']");
        //deleting language
        //active language can not be deleted
        $this->assertElementNotPresent("del.3");
        //making en lang as default
        $this->openListItem("link=English");
        $this->check("editval[default]");
        $this->clickAndWaitFrame("oLockButton", 'list');
        $this->clickDeleteListItem(3);
        $this->clickAndWait("link=Language");
        $this->assertElementPresent("link=Deutsch");
        $this->assertElementPresent("link=English");
        $this->assertElementNotPresent("//tr[@id='row.3']");
        //trying to delete lang with id = 0. it must be impossible
        $this->clickDeleteListItem(1);
        $this->assertTextPresent("Attention: you can't delete main language (with ID = 0)!");
        $this->assertElementPresent("link=Deutsch");
    }

    /**
     * creating payment method
     *
     * @group creatingitems
     */
    public function testCreatePaymentMethod()
    {
        if ($this->getTestConfig()->isSubShop()) {
            $this->markTestSkipped('Functionality is not available in subshop');
        }
        //creating
        $this->loginAdmin("Shop Settings", "Payment Methods");
        $this->frame("list");
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=3 DE test payment šÄßüл");
        $this->assertEquals("3 DE test payment šÄßüл", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("2 EN test payment šÄßüл", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        //creating
        $this->assertEquals("off", $this->getValue("editval[oxpayments__oxactive]"));
        $this->assertEquals("off", $this->getValue("editval[oxpayments__oxchecked]"));
        $this->type("editval[oxpayments__oxdesc]", "create_delete payment [EN]_šÄßüл");
        $this->check("editval[oxpayments__oxactive]");
        $this->type("editval[oxpayments__oxaddsum]", "1");
        $this->select("editval[oxpayments__oxaddsumtype]", "label=%");
        $this->type("editval[oxpayments__oxfromboni]", "50");
        $this->type("editval[oxpayments__oxfromamount]", "10");
        $this->type("editval[oxpayments__oxtoamount]", "9999");
        $this->check("editval[oxpayments__oxchecked]");
        $this->type("editval[oxpayments__oxsort]", "0");
        $this->typeToEditor('oxpayments__oxlongdesc', "create_delete short desc [EN]_šÄßüл");
        $this->assertEquals("on", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='1']"));
        $this->assertEquals("on", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='2']"));
        $this->assertEquals("on", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='4']"));
        $this->assertEquals("on", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='8']"));
        $this->assertEquals("off", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='16']"));
        $this->uncheck("//input[@name='oxpayments__oxaddsumrules[]' and @value='1']");
        $this->uncheck("//input[@name='oxpayments__oxaddsumrules[]' and @value='2']");
        $this->uncheck("//input[@name='oxpayments__oxaddsumrules[]' and @value='4']");
        $this->uncheck("//input[@name='oxpayments__oxaddsumrules[]' and @value='8']");
        $this->check("//input[@name='oxpayments__oxaddsumrules[]' and @value='16']");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->assertEquals("Copy to", $this->getValue("//input[@class='saveinnewlangtext']"));
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->clickAndWaitFrame("//input[@class='saveinnewlangtext']", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("create_delete payment [EN]_šÄßüл", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->type("editval[oxpayments__oxdesc]", "create_delete payment [DE]");
        $this->assertEquals("%", $this->getSelectedLabel("editval[oxpayments__oxaddsumtype]"));
        $this->assertEquals("1", $this->getValue("editval[oxpayments__oxaddsum]"));
        $this->assertEquals("abs %", $this->clearString($this->getText("editval[oxpayments__oxaddsumtype]")));
        $this->assertEquals("50", $this->getValue("editval[oxpayments__oxfromboni]"));
        $this->assertEquals("10", $this->getValue("editval[oxpayments__oxfromamount]"));
        $this->assertEquals("9999", $this->getValue("editval[oxpayments__oxtoamount]"));
        $this->assertEquals("on", $this->getValue("editval[oxpayments__oxactive]"));
        $this->assertEquals("on", $this->getValue("editval[oxpayments__oxchecked]"));
        $this->assertEquals("0", $this->getValue("editval[oxpayments__oxsort]"));
        $this->assertEquals("off", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='1']"));
        $this->assertEquals("off", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='2']"));
        $this->assertEquals("off", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='4']"));
        $this->assertEquals("off", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='8']"));
        $this->assertEquals("on", $this->getValue("//input[@name='oxpayments__oxaddsumrules[]' and @value='16']"));
        $this->assertEquals("create_delete short desc [EN]_šÄßüл", $this->getEditorValue("oxpayments__oxlongdesc"));
        $this->typeToEditor('oxpayments__oxlongdesc', "create_delete short desc [DE]");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->assertEquals("", $this->getText("aFields[]"));
        $this->type("sAddField", "field [DE]_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("field [DE]_šÄßüл", $this->getText("//option[@value='field [DE]_šÄßüл']"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("create_delete payment [EN]_šÄßüл", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->assertEquals("create_delete short desc [EN]_šÄßüл", $this->getEditorValue("oxpayments__oxlongdesc"));
        $this->type("editval[oxpayments__oxaddsum]", "2");
        $this->select("editval[oxpayments__oxaddsumtype]", "label=abs");
        $this->type("editval[oxpayments__oxfromboni]", "100");
        $this->type("editval[oxpayments__oxfromamount]", "15");
        $this->type("editval[oxpayments__oxtoamount]", "1000");
        $this->click("editval[oxpayments__oxchecked]");
        $this->click("editval[oxpayments__oxactive]");
        $this->type("editval[oxpayments__oxsort]", "1");
        $this->typeToEditor("oxpayments__oxlongdesc", "create_delete1 short desc [EN]");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->assertEquals("create_delete payment [EN]_šÄßüл", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->assertEquals("2", $this->getValue("editval[oxpayments__oxaddsum]"));
        $this->assertEquals("abs", $this->getSelectedLabel("editval[oxpayments__oxaddsumtype]"));
        $this->assertEquals("100", $this->getValue("editval[oxpayments__oxfromboni]"));
        $this->assertEquals("15", $this->getValue("editval[oxpayments__oxfromamount]"));
        $this->assertEquals("1000", $this->getValue("editval[oxpayments__oxtoamount]"));
        $this->assertEquals("off", $this->getValue("editval[oxpayments__oxchecked]"));
        $this->assertEquals("1", $this->getValue("editval[oxpayments__oxsort]"));
        $this->assertEquals("create_delete1 short desc [EN]", $this->getEditorValue("oxpayments__oxlongdesc"));
        $this->assertEquals("", $this->getText("aFields[]"));
        $this->type("sAddField", "field [EN]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("field [EN]", $this->getText("//option[@value='field [EN]']"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("create_delete payment [DE]", $this->getValue("editval[oxpayments__oxdesc]"));
        $this->assertEquals("2", $this->getValue("editval[oxpayments__oxaddsum]"));
        $this->assertEquals("abs", $this->getSelectedLabel("editval[oxpayments__oxaddsumtype]"));
        $this->assertEquals("100", $this->getValue("editval[oxpayments__oxfromboni]"));
        $this->assertEquals("15", $this->getValue("editval[oxpayments__oxfromamount]"));
        $this->assertEquals("1000", $this->getValue("editval[oxpayments__oxtoamount]"));
        $this->assertEquals("off", $this->getValue("editval[oxpayments__oxchecked]"));
        $this->assertEquals("1", $this->getValue("editval[oxpayments__oxsort]"));
        $this->assertEquals("create_delete short desc [DE]", $this->getEditorValue("oxpayments__oxlongdesc"));
        $this->assertEquals("field [DE]_šÄßüл", $this->getText("//option[@value='field [DE]_šÄßüл']"));
        $this->addSelection("aFields[]", "label=field [DE]_šÄßüл");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Delete Selected Fields']", "list");
        $this->assertEquals("", $this->getText("aFields[]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("field [EN]", $this->getText("//option[@value='field [EN]']"));
        $this->addSelection("aFields[]", "label=field [EN]");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Delete Selected Fields']", "list");
        $this->assertEquals("", $this->getText("aFields[]"));

        $this->checkTabs(array('Country'));
        $this->frame("list");
        $this->type("where[oxpayments][oxdesc]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete payment [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[contains(@class, 'payment_name')]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating discount
     *
     * @group creatingitems
     */
    public function testCreateDiscount()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=discount for category [DE] šÄßüл");
        $this->assertEquals("discount for category [DE] šÄßüл", $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("discount for category [EN] šÄßüл", $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));

        // Create a new discount:
        $oxsorting = "9999";
        $oxtitle = "create_delete discount [EN]_šÄßüл";
        $this->clickCreateNewItem();
        $this->type("editval[oxdiscount__oxtitle]", $oxtitle);
        $this->type("editval[oxdiscount__oxsort]", $oxsorting);
        $this->clickAndWaitFrame("save", "list");

        $this->assertElementPresent("//input[@value='Copy to']");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->assertEquals($oxtitle, $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->check("editval[oxdiscount__oxactive]");
        $this->type("editval[oxdiscount__oxactivefrom]", "2008-01-01");
        $this->type("editval[oxdiscount__oxactiveto]", "2009-01-01");
        $this->type("editval[oxdiscount__oxamount]", "1");
        $this->type("editval[oxdiscount__oxamountto]", "9");
        $this->type("editval[oxdiscount__oxprice]", "6");
        $this->type("editval[oxdiscount__oxpriceto]", "100");
        $this->type("editval[oxdiscount__oxaddsum]", "3");
        $this->select("editval[oxdiscount__oxaddsumtype]", "label=%");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals($oxtitle, $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->assertEquals("on", $this->getValue("editval[oxdiscount__oxactive]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxdiscount__oxactivefrom]"));
        $this->assertEquals("2009-01-01 00:00:00", $this->getValue("editval[oxdiscount__oxactiveto]"));
        $this->assertEquals("1", $this->getValue("editval[oxdiscount__oxamount]"));
        $this->assertEquals("9", $this->getValue("editval[oxdiscount__oxamountto]"));
        $this->assertEquals("6", $this->getValue("editval[oxdiscount__oxprice]"));
        $this->assertEquals("100", $this->getValue("editval[oxdiscount__oxpriceto]"));
        $this->assertEquals("3", $this->getValue("editval[oxdiscount__oxaddsum]"));
        $this->assertEquals("%", $this->getSelectedLabel("editval[oxdiscount__oxaddsumtype]"));
        $this->assertElementPresent("//input[@value='Copy to']");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->clickAndWaitFrame("//input[@value='Copy to']", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxdiscount__oxtitle]", "create_delete discount [DE]");
        $this->uncheck("editval[oxdiscount__oxactive]");
        $this->type("editval[oxdiscount__oxactivefrom]", "");
        $this->type("editval[oxdiscount__oxactiveto]", "");
        $this->type("editval[oxdiscount__oxamount]", "0");
        $this->type("editval[oxdiscount__oxamountto]", "1");
        $this->type("editval[oxdiscount__oxprice]", "2");
        $this->type("editval[oxdiscount__oxpriceto]", "3");
        $this->select("editval[oxdiscount__oxaddsumtype]", "label=itm");
        $this->waitForItemAppear("//input[@value='Choose product']");
        $this->assertTextNotPresent("1000 Test product 0 [EN] šÄßüл");
        $this->click("//input[@value='Choose product']");
        $this->usePopUp();
        $this->type("_0", "1000");
        $this->keyUp("_0", "0");
        $this->dragAndDrop("//div[@id='container1_c']/table/tbody[2]/tr[1]/td[1]", "container2");
        $this->assertElementText("1000", "//div[@id='container2_c']/table/tbody[2]/tr[1]/td[1]");
        $this->close();
        $this->selectWindow(null);
        $this->frame("edit");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertTextPresent("1000 [DE 4] Test product 0 šÄßüл");
        $this->assertEquals("create_delete discount [DE]", $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->assertEquals("off", $this->getValue("editval[oxdiscount__oxactive]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxdiscount__oxactivefrom]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxdiscount__oxactiveto]"));
        $this->assertEquals("0", $this->getValue("editval[oxdiscount__oxamount]"));
        $this->assertEquals("1", $this->getValue("editval[oxdiscount__oxamountto]"));
        $this->assertEquals("2", $this->getValue("editval[oxdiscount__oxprice]"));
        $this->assertEquals("3", $this->getValue("editval[oxdiscount__oxpriceto]"));
        $this->assertEquals("itm", $this->getSelectedLabel("editval[oxdiscount__oxaddsumtype]"));
        $this->assertTextPresent("1000 [DE 4] Test product 0 šÄßüл");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals($oxtitle, $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->assertEquals("off", $this->getValue("editval[oxdiscount__oxactive]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxdiscount__oxactivefrom]"));
        $this->assertEquals("0000-00-00 00:00:00", $this->getValue("editval[oxdiscount__oxactiveto]"));
        $this->assertEquals("0", $this->getValue("editval[oxdiscount__oxamount]"));
        $this->assertEquals("1", $this->getValue("editval[oxdiscount__oxamountto]"));
        $this->assertEquals("2", $this->getValue("editval[oxdiscount__oxprice]"));
        $this->assertEquals("3", $this->getValue("editval[oxdiscount__oxpriceto]"));
        $this->assertEquals("itm", $this->getSelectedLabel("editval[oxdiscount__oxaddsumtype]"));
        $this->assertTextPresent("1000 Test product 0 [EN] šÄßüл", "bug from mantis #2366");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("create_delete discount [DE]", $this->getValue("editval[oxdiscount__oxtitle]"));
        $this->checkTabs(array('Products', 'Users', 'Mall'));
        $this->frame("list");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->type("where[oxdiscount][oxtitle]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals($oxtitle, $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals($oxsorting, $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /*
     * Try to create a discount with invalid entries in the field oxsort.
     *
     * @group creatingitems
     *
     */
    public function testCreateInvalidDiscount()
    {
        $this->loginAdmin("Shop Settings", "Discounts");
        $oxtitle = "discount with sorting that already exists [EN]_šÄßüл";
        $this->clickCreateNewItem();
        $this->type("editval[oxdiscount__oxtitle]", $oxtitle);
        $this->type("editval[oxdiscount__oxsort]", "100");
        $this->clickAndWaitFrame("save", "list");
        $this->assertTextPresent('Error: The value of the field "Sorting" must be unique.');
        $this->type("editval[oxdiscount__oxsort]", "oxSortString");
        $this->clickAndWaitFrame("save", "list");
        $this->assertTextPresent('Error: The value of the field "Sorting" must be a number.');
    }

    /**
     * creating Shipping Methods
     *
     * @group creatingitems
     */
    public function testCreateSHSets()
    {
        $this->loginAdmin("Shop Settings", "Shipping Methods");
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=Test S&H set [DE] šÄßüл");
        $this->assertEquals("Test S&H set [DE] šÄßüл", $this->getValue("editval[oxdeliveryset__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("Test S&H set [EN] šÄßüл", $this->getValue("editval[oxdeliveryset__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();   //button for create new
        $this->type("editval[oxdeliveryset__oxtitle]", "create_delete SHSet [EN]_šÄßüл");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("create_delete SHSet [EN]_šÄßüл", $this->getValue("editval[oxdeliveryset__oxtitle]"));
        $this->check("editval[oxdeliveryset__oxactive]");
        $this->type("editval[oxdeliveryset__oxactivefrom]", "2008-01-01");
        $this->type("editval[oxdeliveryset__oxactiveto]", "2009-01-01 22:22:22");
        $this->type("editval[oxdeliveryset__oxpos]", "5");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->type("editval[oxdeliveryset__oxtitle]", "create_delete SHSet [DE]");
        $this->assertEquals("on", $this->getValue("editval[oxdeliveryset__oxactive]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxdeliveryset__oxactivefrom]"));
        $this->assertEquals("2009-01-01 22:22:22", $this->getValue("editval[oxdeliveryset__oxactiveto]"));
        $this->assertEquals("5", $this->getValue("editval[oxdeliveryset__oxpos]"));
        $this->clickAndWaitFrame("save", "list");
        $this->selectAndWait("test_editlanguage", "label=English", "editval[oxdeliveryset__oxtitle]");
        $this->assertEquals("create_delete SHSet [EN]_šÄßüл", $this->getValue("editval[oxdeliveryset__oxtitle]"));
        $this->assertEquals("on", $this->getValue("editval[oxdeliveryset__oxactive]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxdeliveryset__oxactivefrom]"));
        $this->assertEquals("2009-01-01 22:22:22", $this->getValue("editval[oxdeliveryset__oxactiveto]"));
        $this->assertEquals("5", $this->getValue("editval[oxdeliveryset__oxpos]"));
        $this->type("editval[oxdeliveryset__oxpos]", "2");
        $this->type("editval[oxdeliveryset__oxactiveto]", "2009-01-01 23:23:23");
        $this->type("editval[oxdeliveryset__oxactivefrom]", "2008-01-01 11:11:11");
        $this->uncheck("editval[oxdeliveryset__oxactive]");
        //$this->clickAndWaitFrame("save", "list");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("create_delete SHSet [DE]", $this->getValue("editval[oxdeliveryset__oxtitle]"));
        $this->assertEquals("off", $this->getValue("editval[oxdeliveryset__oxactive]"));
        $this->assertEquals("2008-01-01 11:11:11", $this->getValue("editval[oxdeliveryset__oxactivefrom]"));
        $this->assertEquals("2009-01-01 23:23:23", $this->getValue("editval[oxdeliveryset__oxactiveto]"));
        $this->assertEquals("2", $this->getValue("editval[oxdeliveryset__oxpos]"));

        $this->checkTabs(array('Payment', 'Users', 'Mall'));
        $this->frame("list");
        $this->type("where[oxdeliveryset][oxtitle]", "create_delete SHSet");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete SHSet [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating Shipping Cost Rules
     *
     * @group creatingitems
     */
    public function testCreateSH()
    {
        $this->loginAdmin("Shop Settings", "Shipping Cost Rules");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=Test delivery category [DE] šÄßüл", "editval[oxdelivery__oxtitle]");
        $this->assertEquals("Test delivery category [DE] šÄßüл", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("Test delivery category [EN] šÄßüл", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();   //creating new item
        $this->type("editval[oxdelivery__oxtitle]", "create_delete SH [EN]_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete SH [EN]_šÄßüл", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->check("editval[oxdelivery__oxactive]");
        $this->type("editval[oxdelivery__oxactivefrom]", "2008-01-01");
        $this->type("editval[oxdelivery__oxactiveto]", "2009-03-04 10:11:12");
        $this->select("editval[oxdelivery__oxdeltype]", "label=Size");
        $this->type("editval[oxdelivery__oxparam]", "2");
        $this->type("editval[oxdelivery__oxparamend]", "100");
        $this->type("editval[oxdelivery__oxaddsum]", "3.9");
        $this->select("editval[oxdelivery__oxaddsumtype]", "label=%");
        $this->check("//input[@name='editval[oxdelivery__oxfixed]' and @value='1']");
        $this->type("editval[oxdelivery__oxsort]", "0");
        $this->check("editval[oxdelivery__oxfinalize]");
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->assertEquals("create_delete SH [EN]_šÄßüл", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->assertEquals("on", $this->getValue("editval[oxdelivery__oxactive]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxdelivery__oxactivefrom]"));
        $this->assertEquals("2009-03-04 10:11:12", $this->getValue("editval[oxdelivery__oxactiveto]"));
        $this->assertEquals("Size", $this->getSelectedLabel("editval[oxdelivery__oxdeltype]"));
        $this->assertEquals("2", $this->getValue("editval[oxdelivery__oxparam]"));
        $this->assertEquals("100", $this->getValue("editval[oxdelivery__oxparamend]"));
        $this->assertEquals("3.9", $this->getValue("editval[oxdelivery__oxaddsum]"));
        $this->assertEquals("%", $this->getSelectedLabel("editval[oxdelivery__oxaddsumtype]"));
        $this->assertEquals("1", $this->getValue("editval[oxdelivery__oxfixed]"));
        $this->assertEquals("0", $this->getValue("editval[oxdelivery__oxsort]"));
        $this->assertEquals("on", $this->getValue("editval[oxdelivery__oxfinalize]"));
        $this->clickAndWaitFrame("save", "list");
        $this->type("editval[oxdelivery__oxtitle]", "create_delete SH [DE]");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->uncheck("editval[oxdelivery__oxactive]");
        $this->type("editval[oxdelivery__oxactivefrom]", "2008-01-01 01:01:01");
        $this->select("editval[oxdelivery__oxdeltype]", "label=Amount");
        $this->type("editval[oxdelivery__oxparam]", "5");
        $this->type("editval[oxdelivery__oxparamend]", "9999");
        $this->type("editval[oxdelivery__oxaddsum]", "1.5");
        $this->select("editval[oxdelivery__oxaddsumtype]", "label=abs");
        $this->check("//input[@name='editval[oxdelivery__oxfixed]' and @value='2']");
        $this->type("editval[oxdelivery__oxsort]", "1");
        $this->uncheck("editval[oxdelivery__oxfinalize]");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("create_delete SH [EN]_šÄßüл", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->assertEquals("off", $this->getValue("editval[oxdelivery__oxactive]"));
        $this->assertEquals("2008-01-01 01:01:01", $this->getValue("editval[oxdelivery__oxactivefrom]"));
        $this->assertEquals("2009-03-04 10:11:12", $this->getValue("editval[oxdelivery__oxactiveto]"));
        $this->assertEquals("Amount", $this->getSelectedLabel("editval[oxdelivery__oxdeltype]"));
        $this->assertEquals("5", $this->getValue("editval[oxdelivery__oxparam]"));
        $this->assertEquals("9999", $this->getValue("editval[oxdelivery__oxparamend]"));
        $this->assertEquals("1.5", $this->getValue("editval[oxdelivery__oxaddsum]"));
        $this->assertEquals("abs", $this->getSelectedLabel("editval[oxdelivery__oxaddsumtype]"));
        $this->assertEquals("2", $this->getValue("editval[oxdelivery__oxfixed]"));
        $this->assertEquals("1", $this->getValue("editval[oxdelivery__oxsort]"));
        $this->assertEquals("off", $this->getValue("editval[oxdelivery__oxfinalize]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("create_delete SH [DE]", $this->getValue("editval[oxdelivery__oxtitle]"));
        $this->assertEquals("off", $this->getValue("editval[oxdelivery__oxactive]"));
        $this->assertEquals("2008-01-01 01:01:01", $this->getValue("editval[oxdelivery__oxactivefrom]"));
        $this->assertEquals("2009-03-04 10:11:12", $this->getValue("editval[oxdelivery__oxactiveto]"));
        $this->assertEquals("Amount", $this->getSelectedLabel("editval[oxdelivery__oxdeltype]"));
        $this->assertEquals("5", $this->getValue("editval[oxdelivery__oxparam]"));
        $this->assertEquals("9999", $this->getValue("editval[oxdelivery__oxparamend]"));
        $this->assertEquals("1.5", $this->getValue("editval[oxdelivery__oxaddsum]"));
        $this->assertEquals("abs %", $this->clearString($this->getText("editval[oxdelivery__oxaddsumtype]")));
        $this->assertEquals("2", $this->getValue("editval[oxdelivery__oxfixed]"));
        $this->assertEquals("1", $this->getValue("editval[oxdelivery__oxsort]"));
        $this->assertEquals("off", $this->getValue("editval[oxdelivery__oxfinalize]"));

        $this->checkTabs(array('Payment', 'Users', 'Mall'));
        $this->frame("list");
        $this->type("where[oxdelivery][oxtitle]", "create_delete SH");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete SH [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating Coupon
     *
     * @group creatingitems
     */
    public function testCreateCoupon()
    {
        $this->loginAdmin("Shop Settings", "Coupon Series");
        $this->frame("edit");
        $this->clickCreateNewItem();
        $this->type("editval[oxvoucherseries__oxserienr]", "create_delete coupon_šÄßüл");
        $this->type("editval[oxvoucherseries__oxseriedescription]", "create_delete coupon description_šÄßüл");
        $this->type("editval[oxvoucherseries__oxbegindate]", "2008-01-01");
        $this->type("editval[oxvoucherseries__oxenddate]", "2009-02-02");
        $this->type("editval[oxvoucherseries__oxdiscount]", "10");
        $this->select("editval[oxvoucherseries__oxdiscounttype]", "label=%");
        $this->type("editval[oxvoucherseries__oxminimumvalue]", "100");
        $this->check("editval[oxvoucherseries__oxallowsameseries]");
        $this->check("editval[oxvoucherseries__oxallowotherseries]");
        $this->check("editval[oxvoucherseries__oxallowuseanother]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete coupon_šÄßüл", $this->getValue("editval[oxvoucherseries__oxserienr]"));
        $this->assertEquals("create_delete coupon description_šÄßüл", $this->getValue("editval[oxvoucherseries__oxseriedescription]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxvoucherseries__oxbegindate]"));
        $this->assertEquals("2009-02-02 00:00:00", $this->getValue("editval[oxvoucherseries__oxenddate]"));
        $this->assertEquals("10.00", $this->getValue("editval[oxvoucherseries__oxdiscount]"));
        $this->assertEquals("%", $this->getSelectedLabel("editval[oxvoucherseries__oxdiscounttype]"));
        $this->assertEquals("100.00", $this->getValue("editval[oxvoucherseries__oxminimumvalue]"));
        $this->assertEquals("1", $this->getValue("editval[oxvoucherseries__oxallowsameseries]"));
        $this->assertEquals("1", $this->getValue("editval[oxvoucherseries__oxallowotherseries]"));
        $this->assertEquals("1", $this->getValue("editval[oxvoucherseries__oxallowuseanother]"));
        $this->type("editval[oxvoucherseries__oxserienr]", "create_delete coupon1");
        $this->type("editval[oxvoucherseries__oxseriedescription]", "create_delete coupon description1");
        $this->type("editval[oxvoucherseries__oxbegindate]", "2008-01-01 22:22:22");
        $this->type("editval[oxvoucherseries__oxenddate]", "2008-09-09 03:03:03");
        $this->type("editval[oxvoucherseries__oxdiscount]", "15");
        $this->select("editval[oxvoucherseries__oxdiscounttype]", "abs");
        $this->type("editval[oxvoucherseries__oxminimumvalue]", "110");
        $this->check("//input[@name='editval[oxvoucherseries__oxallowuseanother]' and @value='1']");
        $this->check("//input[@name='editval[oxvoucherseries__oxallowotherseries]' and @value='1']");
        $this->check("//input[@name='editval[oxvoucherseries__oxallowuseanother]' and @value='1']");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete coupon1", $this->getValue("editval[oxvoucherseries__oxserienr]"));
        $this->assertEquals("create_delete coupon description1", $this->getValue("editval[oxvoucherseries__oxseriedescription]"));
        $this->assertEquals("2008-01-01 22:22:22", $this->getValue("editval[oxvoucherseries__oxbegindate]"));
        $this->assertEquals("2008-09-09 03:03:03", $this->getValue("editval[oxvoucherseries__oxenddate]"));
        $this->assertEquals("15.00", $this->getValue("editval[oxvoucherseries__oxdiscount]"));
        $this->assertEquals("0", $this->getSelectedIndex("editval[oxvoucherseries__oxdiscounttype]"));
        $this->assertEquals("110.00", $this->getValue("editval[oxvoucherseries__oxminimumvalue]"));
        $this->assertEquals("1", $this->getValue("//input[@name='editval[oxvoucherseries__oxallowsameseries]']"));
        $this->assertEquals("1", $this->getValue("//input[@name='editval[oxvoucherseries__oxallowotherseries]']"));
        $this->assertEquals("1", $this->getValue("//input[@name='editval[oxvoucherseries__oxallowuseanother]']"));
        $this->assertEquals("1", $this->getValue("randomVoucherNr"));
        $this->assertEquals("", $this->getValue("voucherNr"));
        $this->assertEquals("0", $this->getValue("voucherAmount"));
        $this->type("voucherNr", "222");
        $this->type("voucherAmount", "5");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Generate']");
        $this->frame("dynexport_do", false, false);
        $this->waitForText("Coupons generation completed");
        $this->checkForErrors();
        $this->assertEquals("5", $this->getText("//tr[2]/td[2]"));
        $this->assertEquals("5", $this->getText("//tr[3]/td[2]"));
        $this->assertEquals("0", $this->getText("//tr[4]/td[2]"));
        $this->frame("edit");
        $this->checkForErrors();
        $this->clickAndWaitFrame("//input[@name='save' and @value='Export']");
        $this->frame("dynexport_do", false, false);
        $this->waitForText("Coupons export completed");
        //$this->checkForErrors();
        $this->frame("edit");
        $this->checkForErrors();

        $this->checkTabs(array('User Groups & Products', 'Mall'));
        $this->frame("list");
        $this->assertElementNotPresent("link=Export");
        $this->type("where[oxvoucherseries][oxserienr]", "create_delete coupon");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete coupon1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating Gift Wrapping
     *
     * @group creatingitems
     */
    public function testCreateGiftWrapping()
    {
        $this->loginAdmin("Shop Settings", "Gift Wrapping");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Name");
        $this->openListItem("link=1 DE Gift Wrapping šÄßüл");
        $this->assertEquals("1 DE Gift Wrapping šÄßüл", $this->getValue("editval[oxwrapping__oxname]"));
        $this->changeAdminListLanguage('English');
        $this->assertEquals("3 EN Gift Wrapping šÄßüл", $this->getValue("editval[oxwrapping__oxname]"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->check("editval[oxwrapping__oxactive]");
        $this->type("editval[oxwrapping__oxname]", "create_delete gift [EN]_šÄßüл");
        $this->type("editval[oxwrapping__oxprice]", "10");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("on", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->assertEquals("create_delete gift [EN]_šÄßüл", $this->getValue("editval[oxwrapping__oxname]"));
        $this->assertEquals("10", $this->getValue("editval[oxwrapping__oxprice]"));
        $this->type("editval[oxwrapping__oxname]", "create_delete gift [DE]");
        $this->uncheck("editval[oxwrapping__oxactive]");
        $this->type("editval[oxwrapping__oxprice]", "12");
        $this->select("editval[oxwrapping__oxtype]", "label=Greeting Card");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("create_delete gift [DE]", $this->getValue("editval[oxwrapping__oxname]"));
        $this->assertEquals("off", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->assertEquals("12", $this->getValue("editval[oxwrapping__oxprice]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->assertEquals("Gift Wrapping Greeting Card", $this->clearString($this->getText("editval[oxwrapping__oxtype]")));
        $this->assertEquals("12", $this->getValue("editval[oxwrapping__oxprice]"));
        $this->assertEquals("create_delete gift [EN]_šÄßüл", $this->getValue("editval[oxwrapping__oxname]"));
        $this->type("editval[oxwrapping__oxname]", "create_delete gift1 [EN]");
        $this->type("editval[oxwrapping__oxprice]", "11");
        $this->uncheck("editval[oxwrapping__oxactive]");
        $this->select("editval[oxwrapping__oxtype]", "label=Gift Wrapping");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("off", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->assertEquals("Gift Wrapping Greeting Card", $this->clearString($this->getText("editval[oxwrapping__oxtype]")));
        $this->assertEquals("create_delete gift1 [EN]", $this->getValue("editval[oxwrapping__oxname]"));
        $this->assertEquals("11", $this->getValue("editval[oxwrapping__oxprice]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("off", $this->getValue("editval[oxwrapping__oxactive]"));
        $this->assertEquals("Gift Wrapping Greeting Card", $this->clearString($this->getText("editval[oxwrapping__oxtype]")));
        $this->assertEquals("create_delete gift [DE]", $this->getValue("editval[oxwrapping__oxname]"));
        $this->assertEquals("11", $this->getValue("editval[oxwrapping__oxprice]"));

        $this->checkTabs(array('Mall'));
        $this->frame("list");
        $this->type("where[oxwrapping][oxname]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete gift1 [EN]", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating Product. Main, Extending
     *
     * @group creatingitems
     */
    public function testCreateProductMainExtended()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->changeAdminListLanguage('Deutsch');
        $this->type("where[oxarticles][oxartnum]", "100");
        $this->clickAndWait("submitit");
        $this->openListItem("link=[DE 4] Test product 0 šÄßüл");
        $this->assertEquals("[DE 4] Test product 0 šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("Test product 0 [EN] šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        //Product tab
        $this->clickCreateNewItem();
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxactive]"));
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->type("editval[oxarticles__oxtitle]", "create_delete product [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxartnum]", "10000");
        $this->type("editval[oxarticles__oxshortdesc]", "create_delete short desc [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxsearchkeys]", "search [EN]_šÄßüл");
        $this->select("editval[oxarticles__oxvendorid]", "label=Distributor [EN] šÄßüл");
        $this->select("editval[oxarticles__oxmanufacturerid]", "label=Manufacturer [EN] šÄßüл");
        $this->type("editval[oxarticles__oxprice]", "5.9");
        $this->type("editval[oxarticles__oxpricea]", "1.1");
        $this->type("editval[oxarticles__oxpriceb]", "1.2");
        $this->type("editval[oxarticles__oxpricec]", "1.3");
        $this->type("editval[oxarticles__oxvat]", "4");
        $this->select("art_category", "label=Test category 0 [EN] šÄßüл");
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxean]"));
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxdistean]"));
        $this->type("editval[oxarticles__oxean]", "EAN_šÄßüл");
        $this->type("editval[oxarticles__oxdistean]", "vendor EAN_ßÄ");
        $this->assertEquals("", $this->getEditorValue("oxarticles__oxlongdesc"));
        $this->typeToEditor("oxarticles__oxlongdesc", "long desc [EN]_šÄßüл");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxactive]"));
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->assertEquals("create_delete product [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("10000", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->assertEquals("create_delete short desc [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->assertEquals("search [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxsearchkeys]"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getSelectedLabel("editval[oxarticles__oxvendorid]"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getSelectedLabel("editval[oxarticles__oxmanufacturerid]"));
        $this->assertEquals("5.9", $this->getValue("editval[oxarticles__oxprice]"));
        $this->assertEquals("1.1", $this->getValue("editval[oxarticles__oxpricea]"));
        $this->assertEquals("1.2", $this->getValue("editval[oxarticles__oxpriceb]"));
        $this->assertEquals("1.3", $this->getValue("editval[oxarticles__oxpricec]"));
        $this->assertEquals("4", $this->getValue("editval[oxarticles__oxvat]"));
        $this->assertEquals("EAN_šÄßüл", $this->getValue("editval[oxarticles__oxean]"));
        $this->assertEquals("vendor EAN_ßÄ", $this->getValue("editval[oxarticles__oxdistean]"));
        $this->assertEquals("long desc [EN]_šÄßüл", $this->getEditorValue("oxarticles__oxlongdesc"));
        $this->assertEquals("Save", $this->getValue("saveArticle"));
        $this->assertEquals("Copy Product", $this->getValue("save"));
        $this->assertElementPresent("//input[@name='save' and @value='Copy to']");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->clickAndWaitFrame("//input[@name='save' and @value='Copy to']", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->check("editval[oxarticles__oxactive]");
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->type("editval[oxarticles__oxtitle]", "create_delete product [DE]");
        $this->type("editval[oxarticles__oxartnum]", "10001");
        $this->type("editval[oxarticles__oxshortdesc]", "create_delete short desc [DE]");
        $this->type("editval[oxarticles__oxsearchkeys]", "search [DE]");
        $this->type("editval[oxarticles__oxprice]", "5.91");
        $this->type("editval[oxarticles__oxpricea]", "1.11");
        $this->type("editval[oxarticles__oxpriceb]", "1.21");
        $this->type("editval[oxarticles__oxpricec]", "1.31");
        $this->type("editval[oxarticles__oxvat]", "4.5");
        $this->typeToEditor("oxarticles__oxlongdesc", "long desc [DE]");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");

        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxactive]"));
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->assertEquals("create_delete product [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("10001", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->assertEquals("create_delete short desc [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->assertEquals("search [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxsearchkeys]"));
        $this->assertEquals("5.91", $this->getValue("editval[oxarticles__oxprice]"));
        $this->assertEquals("1.11", $this->getValue("editval[oxarticles__oxpricea]"));
        $this->assertEquals("1.21", $this->getValue("editval[oxarticles__oxpriceb]"));
        $this->assertEquals("1.31", $this->getValue("editval[oxarticles__oxpricec]"));
        $this->assertEquals("4.5", $this->getValue("editval[oxarticles__oxvat]"));
        $this->assertEquals("EAN_šÄßüл", $this->getValue("editval[oxarticles__oxean]"));
        $this->assertEquals("vendor EAN_ßÄ", $this->getValue("editval[oxarticles__oxdistean]"));
        $this->assertEquals("long desc [EN]_šÄßüл", $this->getEditorValue("oxarticles__oxlongdesc"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxactive]"));
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->assertEquals("create_delete product [DE]", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("10001", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->assertEquals("create_delete short desc [DE]", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->assertEquals("search [DE]", $this->getValue("editval[oxarticles__oxsearchkeys]"));
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getSelectedLabel("editval[oxarticles__oxvendorid]"));
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getSelectedLabel("editval[oxarticles__oxmanufacturerid]"));
        $this->assertEquals("5.91", $this->getValue("editval[oxarticles__oxprice]"));
        $this->assertEquals("1.11", $this->getValue("editval[oxarticles__oxpricea]"));
        $this->assertEquals("1.21", $this->getValue("editval[oxarticles__oxpriceb]"));
        $this->assertEquals("1.31", $this->getValue("editval[oxarticles__oxpricec]"));
        $this->assertEquals("4.5", $this->getValue("editval[oxarticles__oxvat]"));
        $this->assertEquals("EAN_šÄßüл", $this->getValue("editval[oxarticles__oxean]"));
        $this->assertEquals("vendor EAN_ßÄ", $this->getValue("editval[oxarticles__oxdistean]"));
        $this->assertEquals("long desc [DE]", $this->getEditorValue("oxarticles__oxlongdesc"));
        $this->selectAndWait("test_editlanguage", "label=English");
        //Extended tab
        $this->openTab("Extended");
        $this->assertElementNotPresent("editval[oxarticles__oxurlimg]", "#289 from Mantis");
        $this->type("editval[oxarticles__oxweight]", "1");
        $this->type("editval[oxarticles__oxlength]", "2");
        $this->type("editval[oxarticles__oxwidth]", "3");
        $this->type("editval[oxarticles__oxheight]", "4");
        $this->type("editval[oxarticles__oxunitquantity]", "5");
        $this->type("editval[oxarticles__oxunitname]", "6");
        $this->type("editval[oxarticles__oxexturl]", "http://url.lt");
        $this->type("editval[oxarticles__oxurldesc]", "url text [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxbprice]", "7");
        $this->type("editval[oxarticles__oxtprice]", "8");
        $this->type("editval[oxarticles__oxtemplate]", "template_šÄßüл");
        $this->type("editval[oxarticles__oxquestionemail]", "contact_šÄßüл");
        $this->assertEquals("on", $this->getValue("//input[@name='editval[oxarticles__oxissearch]' and @type='checkbox']"));
        $this->uncheck("//input[@name='editval[oxarticles__oxissearch]' and @type='checkbox']");
        $this->assertEquals("off", $this->getValue("//input[@name='editval[oxarticles__oxnonmaterial]' and @type='checkbox']"));
        $this->check("//input[@name='editval[oxarticles__oxnonmaterial]' and @type='checkbox']");
        $this->assertEquals("off", $this->getValue("//input[@name='editval[oxarticles__oxfreeshipping]' and @type='checkbox']"));
        $this->check("//input[@name='editval[oxarticles__oxfreeshipping]' and @type='checkbox']");
        $this->assertEquals("off", $this->getValue("//input[@name='editval[oxarticles__oxblfixedprice]' and @type='checkbox']"));
        $this->check("editval[oxarticles__oxblfixedprice]");
        $this->assertEquals("url text [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxurldesc]"));
        $this->assertElementNotPresent("editval[oxarticles__oxbundleid]");
        $this->assertElementPresent("//input[@value='Assign Products']");
        if ($this->isElementPresent("//input[@name='editval[oxarticles__oxskipdiscounts]' and @type='checkbox']")) {
            $this->assertEquals("off", $this->getValue("//input[@name='editval[oxarticles__oxskipdiscounts]' and @type='checkbox']"));
            $this->check("//input[@name='editval[oxarticles__oxskipdiscounts]' and @type='checkbox']");
        }
        $this->clickAndWait("save");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("1", $this->getValue("editval[oxarticles__oxweight]"));
        $this->assertEquals("2", $this->getValue("editval[oxarticles__oxlength]"));
        $this->assertEquals("3", $this->getValue("editval[oxarticles__oxwidth]"));
        $this->assertEquals("4", $this->getValue("editval[oxarticles__oxheight]"));
        $this->assertEquals("http://url.lt", $this->getValue("editval[oxarticles__oxexturl]"));
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxurldesc]"));
        $this->type("editval[oxarticles__oxurldesc]", "url text [DE]");
        $this->assertEquals("7", $this->getValue("editval[oxarticles__oxbprice]"));
        $this->assertEquals("8", $this->getValue("editval[oxarticles__oxtprice]"));
        $this->assertEquals("template_šÄßüл", $this->getValue("editval[oxarticles__oxtemplate]"));
        $this->assertEquals("contact_šÄßüл", $this->getValue("editval[oxarticles__oxquestionemail]"));
        $this->assertEquals("off", $this->getValue("//input[@name='editval[oxarticles__oxissearch]' and @type='checkbox']"));
        $this->assertEquals("on", $this->getValue("//input[@name='editval[oxarticles__oxnonmaterial]' and @type='checkbox']"));
        $this->assertEquals("on", $this->getValue("//input[@name='editval[oxarticles__oxfreeshipping]' and @type='checkbox']"));
        $this->assertEquals("on", $this->getValue("//input[@name='editval[oxarticles__oxblfixedprice]' and @type='checkbox']"));
        $this->assertElementNotPresent("editval[oxarticles__oxbundleid]");
        if ($this->isElementPresent("//input[@name='editval[oxarticles__oxskipdiscounts]' and @type='checkbox']")) {
            $this->assertEquals("on", $this->getValue("//input[@name='editval[oxarticles__oxskipdiscounts]' and @type='checkbox']"));
        }
        $this->clickAndWait("save");
        $this->assertEquals("url text [DE]", $this->getValue("editval[oxarticles__oxurldesc]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("url text [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxurldesc]"));
        //testing if other tabs are working
        $this->checkTabs(array(
            'Stock',
            'Selection',
            'Crosssell.',
            'Variants',
            'Pictures',
            'Review',
            'Statistics',
            'SEO',
            'Rights',
            'Mall'
        ));
    }

    /**
     * creating Product. Inventory tab
     *
     * @group creatingitems
     */
    public function testCreateProductInventory()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->frame("edit");
        //Product tab
        //  $this->clickCreateNewItem();
        $this->type("editval[oxarticles__oxtitle]", "create_delete product");
        $this->type("editval[oxarticles__oxartnum]", "10000");
        $this->type("editval[oxarticles__oxprice]", "5.9");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Copy to']", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        // Inventory tab
        $this->openTab("Stock");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxarticles__oxstock]", "10.1");
        $this->assertEquals("Standard", $this->getSelectedLabel("editval[oxarticles__oxstockflag]"));
        $this->select("editval[oxarticles__oxstockflag]", "label=External Storehouse");
        $this->assertEquals("0000-00-00", $this->getValue("editval[oxarticles__oxdelivery]"));
        $this->type("editval[oxarticles__oxdelivery]", "2008-01-01");
        $this->check("editval[oxarticles__oxremindactive]");
        $this->type("editval[oxarticles__oxremindamount]", "5");
        $this->type("editval[oxarticles__oxstocktext]", "in stock [DE]_šÄßüл");
        $this->type("editval[oxarticles__oxnostocktext]", "out of stock [DE]_šÄßüл");
        $this->type("editval[oxarticles__oxmindeltime]", "2");
        $this->type("editval[oxarticles__oxmaxdeltime]", "5");
        $this->select("editval[oxarticles__oxdeltimeunit]", "label=Weeks");
        $this->clickAndWait("save");
        $this->assertEquals("2", $this->getValue("editval[oxarticles__oxmindeltime]"));
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxmaxdeltime]"));
        $this->assertEquals("Weeks", $this->getSelectedLabel("editval[oxarticles__oxdeltimeunit]"));
        $this->assertEquals("10.1", $this->getValue("editval[oxarticles__oxstock]"));
        $this->assertEquals("External Storehouse", $this->getSelectedLabel("editval[oxarticles__oxstockflag]"));
        $this->assertEquals("2008-01-01", $this->getValue("editval[oxarticles__oxdelivery]"));
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxremindamount]"));
        $this->assertEquals("on", $this->getValue("editval[oxarticles__oxremindactive]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("in stock [DE]_šÄßüл", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("out of stock [DE]_šÄßüл", $this->getValue("editval[oxarticles__oxnostocktext]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxnostocktext]"));
        $this->type("editval[oxarticles__oxstocktext]", "in stock [EN]");
        $this->type("editval[oxarticles__oxnostocktext]", "out of stock [EN]");
        $this->clickAndWait("save");
        $this->assertEquals("in stock [EN]", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("out of stock [EN]", $this->getValue("editval[oxarticles__oxnostocktext]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("in stock [DE]_šÄßüл", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("out of stock [DE]_šÄßüл", $this->getValue("editval[oxarticles__oxnostocktext]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("in stock [EN]", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("out of stock [EN]", $this->getValue("editval[oxarticles__oxnostocktext]"));
    }

    /**
     * creating Product. Copy product
     *
     * @group creatingitems
     */
    public function testCreateProductCopy()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->frame("edit");
        $this->assertEquals("0", $this->getValue("editval[oxarticles__oxactive]"));
        $this->assertElementNotPresent("editval[oxarticles__oxactivefrom]");
        $this->assertElementNotPresent("editval[oxarticles__oxactiveto]");
        $this->type("editval[oxarticles__oxtitle]", "create_delete product [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxartnum]", "10001_šÄßüл");
        $this->type("editval[oxarticles__oxshortdesc]", "create_delete short desc [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxsearchkeys]", "search [EN]_šÄßüл");
        $this->select("editval[oxarticles__oxvendorid]", "label=Distributor [EN] šÄßüл");
        $this->select("editval[oxarticles__oxmanufacturerid]", "label=Manufacturer [EN] šÄßüл");
        $this->type("editval[oxarticles__oxprice]", "5.91");
        $this->type("editval[oxarticles__oxpricea]", "1.11");
        $this->type("editval[oxarticles__oxpriceb]", "1.21");
        $this->type("editval[oxarticles__oxpricec]", "1.31");
        $this->type("editval[oxarticles__oxvat]", "4.5");
        $this->select("art_category", "label=Test category 0 [EN] šÄßüл");
        $this->type("editval[oxarticles__oxean]", "EAN_Äß");
        $this->type("editval[oxarticles__oxdistean]", "vendor EAN_Äß");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->clickAndWaitFrame("/descendant::input[@name='save'][2]", "list");
        $this->check("editval[oxarticles__oxactive]");
        $this->type("editval[oxarticles__oxtitle]", "create_delete product [DE]");
        $this->type("editval[oxarticles__oxshortdesc]", "create_delete short desc [DE]");
        $this->type("editval[oxarticles__oxsearchkeys]", "search [DE]");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        //Extended tab
        $this->frame("list");
        $this->openListItem("link=Extended", "editval[oxarticles__oxweight]");
        $this->type("editval[oxarticles__oxweight]", "1");
        $this->type("editval[oxarticles__oxlength]", "2");
        $this->type("editval[oxarticles__oxwidth]", "3");
        $this->type("editval[oxarticles__oxheight]", "4");
        $this->type("editval[oxarticles__oxunitquantity]", "5");
        $this->type("unitinput", "6");
        $this->type("editval[oxarticles__oxexturl]", "http://url.lt");
        $this->type("editval[oxarticles__oxurldesc]", "url text [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxbprice]", "7");
        $this->type("editval[oxarticles__oxtprice]", "8");
        $this->type("editval[oxarticles__oxtemplate]", "template_šÄßüл");
        $this->type("editval[oxarticles__oxquestionemail]", "contact_šÄßüл");
        $this->uncheck("/descendant::input[@name='editval[oxarticles__oxissearch]'][2]");
        $this->check("/descendant::input[@name='editval[oxarticles__oxnonmaterial]'][2]");
        $this->check("/descendant::input[@name='editval[oxarticles__oxfreeshipping]'][2]");
        $this->check("editval[oxarticles__oxblfixedprice]");
        $this->clickAndWait("save");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->type("editval[oxarticles__oxurldesc]", "url text [DE]");
        $this->clickAndWait("save");
        $this->selectAndWait("test_editlanguage", "label=English");
        // Inventory tab
        $this->openTab("Stock");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->type("editval[oxarticles__oxstock]", "10");
        $this->select("editval[oxarticles__oxstockflag]", "label=External Storehouse");
        $this->type("editval[oxarticles__oxdelivery]", "2008-01-01");
        $this->check("editval[oxarticles__oxremindactive]");
        $this->type("editval[oxarticles__oxremindamount]", "5");
        $this->type("editval[oxarticles__oxstocktext]", "in stock [DE]");
        $this->type("editval[oxarticles__oxnostocktext]", "out of stock [DE]");
        $this->clickAndWait("save");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->type("editval[oxarticles__oxstocktext]", "in stock [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxnostocktext]", "out of stock [EN]_šÄßüл");
        $this->clickAndWait("save");
        $this->frame("list");
        //copying article
        $this->type("where[oxarticles][oxartnum]", "10001");
        $this->clickAndWait("submitit");
        $this->assertElementPresent("//tr[@id='row.1']/td[2]");
        $this->assertElementNotPresent("//tr[@id='row.2']/td[2]");
        $this->openTab("Main");
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("create_delete short desc [DE]", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("create_delete short desc [DE]", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->selectAndWait("test_editlanguage", "label=English", "editval[oxarticles__oxtitle]");
        $this->assertEquals("create_delete product [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->assertEquals("10001_šÄßüл", $this->getValue("editval[oxarticles__oxartnum]"));
        $this->assertEquals("create_delete short desc [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxshortdesc]"));
        $this->assertEquals("search [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxsearchkeys]"));
        $this->assertEquals("5.91", $this->getValue("editval[oxarticles__oxprice]"));
        $this->assertEquals("1.11", $this->getValue("editval[oxarticles__oxpricea]"));
        $this->assertEquals("1.21", $this->getValue("editval[oxarticles__oxpriceb]"));
        $this->assertEquals("1.31", $this->getValue("editval[oxarticles__oxpricec]"));
        $this->assertEquals("4.5", $this->getValue("editval[oxarticles__oxvat]"));
        $this->openTab("Extended");
        $this->assertEquals("1", $this->getValue("editval[oxarticles__oxweight]"));
        $this->assertEquals("2", $this->getValue("editval[oxarticles__oxlength]"));
        $this->assertEquals("3", $this->getValue("editval[oxarticles__oxwidth]"));
        $this->assertEquals("4", $this->getValue("editval[oxarticles__oxheight]"));
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxunitquantity]"));
        $this->assertEquals("6", $this->getValue("unitinput"));
        $this->assertEquals("http://url.lt", $this->getValue("editval[oxarticles__oxexturl]"));
        $this->assertEquals("url text [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxurldesc]"));
        $this->assertEquals("7", $this->getValue("editval[oxarticles__oxbprice]"));
        $this->assertEquals("8", $this->getValue("editval[oxarticles__oxtprice]"));
        $this->assertEquals("template_šÄßüл", $this->getValue("editval[oxarticles__oxtemplate]"));
        $this->assertEquals("contact_šÄßüл", $this->getValue("editval[oxarticles__oxquestionemail]"));
        $this->assertTrue($this->isChecked("/descendant::input[@name='editval[oxarticles__oxnonmaterial]'][2]"));
        $this->assertTrue($this->isChecked("/descendant::input[@name='editval[oxarticles__oxfreeshipping]'][2]"));
        $this->assertFalse($this->isChecked("/descendant::input[@name='editval[oxarticles__oxissearch]'][2]"));
        $this->assertEquals("on", $this->getValue("editval[oxarticles__oxblfixedprice]"));
        $this->openTab("Stock");
        $this->assertEquals("10", $this->getValue("editval[oxarticles__oxstock]"));
        $this->assertEquals("2008-01-01", $this->getValue("editval[oxarticles__oxdelivery]"));
        $this->assertEquals("5", $this->getValue("editval[oxarticles__oxremindamount]"));
        $this->assertEquals("in stock [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxstocktext]"));
        $this->assertEquals("out of stock [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxnostocktext]"));
        //testing if other tabs are working
        $this->checkTabs(array(
            'Selection',
            'Crosssell.',
            'Variants',
            'Pictures',
            'Review',
            'Statistics',
            'SEO',
            'Rights',
            'Mall'
        ));
        $this->frame("list");
        $this->assertEquals("create_delete product [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertEquals("create_delete product [EN]_šÄßüл", $this->getText("//tr[@id='row.2']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.3']/td[3]");
    }

    /**
     * creating Product. stock prices
     *
     * @group creatingitems
     */
    public function testCreateProductStockPrices()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->frame("edit");
        //Product tab
        $this->clickCreateNewItem();
        $this->type("editval[oxarticles__oxtitle]", "create_delete product");
        $this->type("editval[oxarticles__oxartnum]", "10000");
        $this->type("editval[oxarticles__oxprice]", "5.9");
        $this->waitForEditable("saveArticle");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->clickAndWaitFrame("/descendant::input[@name='save'][2]", "list");
        $this->selectAndWait("test_editlanguage", "label=English");

        // Inventory tab
        $this->openTab("Stock");

        // Add scale price
        $this->type("editval[oxprice2article__oxamount]", "1");
        $this->type("editval[oxprice2article__oxamountto]", "5");
        $this->type("editval[price]", "10");
        $this->clickAndWait("/descendant::input[@name='save'][2]");

        // Check scale price
        $this->assertEquals("Attention: Scale price must be lower than normal price.", $this->getText("//fieldset[@title='Scale Prices']/table/tbody/tr/td[1]/div"));
        $this->assertEquals("1", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr/td[1]/input[1]"));
        $this->assertEquals("5", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr/td[1]/input[2]"));
        $this->assertEquals("oxprice2article__oxaddabs", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr/td[2]/select[1]"));
        $this->assertEquals("10", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr/td[2]/input[1]"));
        $this->assertEquals("6", $this->getXpathCount("//fieldset[@title='Scale Prices']/table/tbody/tr"));

        // Update scale price to correct values
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr/td[2]/input", "4");
        $this->clickAndWait("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td/input");

        // Add scale price
        $this->type("editval[oxprice2article__oxamount]", "6");
        $this->type("editval[oxprice2article__oxamountto]", "99999");
        $this->select("editval[pricetype]", "label=% Discount");
        $this->type("editval[price]", "15");
        $this->clickAndWait("/descendant::input[@name='save'][2]");

        // Check scale price
        $this->assertEquals("6", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[1]/input[1]"));
        $this->assertEquals("99999", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[1]/input[2]"));
        $this->assertEquals("oxprice2article__oxaddperc", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[2]/select[1]"));
        $this->assertEquals("15", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[2]/input[1]"));
        $this->assertEquals("6", $this->getXpathCount("//fieldset[@title='Scale Prices']/table/tbody/tr"));

        //test updating
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td/input", "2");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td/input[2]", "20");
        $this->select("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/select", "label=% Discount");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/input", "11");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td/input", "21");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td/input[2]", "99998");
        $this->select("//fieldset/table/tbody/tr[2]/td[2]/select", "label=abs");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[2]/input", "19");

        $this->clickAndWait("saveAll");

        $this->assertEquals("Attention: Scale price must be lower than normal price.", $this->getText("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[1]/div"));
        $this->assertEquals("2", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[1]/input[1]"));
        $this->assertEquals("20", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[1]/input[2]"));
        $this->assertEquals("oxprice2article__oxaddperc", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/select[1]"));
        $this->assertEquals("11", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/input[1]"));
        $this->assertEquals("21", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[1]/input[1]"));
        $this->assertEquals("99998", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[1]/input[2]"));
        $this->assertEquals("19", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[2]/input[1]"));
        $this->assertEquals("oxprice2article__oxaddabs", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[2]/select[1]"));
        $this->assertEquals("7", $this->getXpathCount("//fieldset[@title='Scale Prices']/table/tbody/tr"));

        //editing scale price
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[1]/input[1]", "3");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[1]/input[2]", "21");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/input[1]", "12");
        $this->select("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[2]/select", "label=abs");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td/input", "22");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td/input[2]", "99999");
        $this->type("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[2]/input", "20");
        $this->select("//fieldset/table/tbody/tr[3]/td[2]/select", "label=% Discount");
        $this->clickAndWait("saveAll");

        $this->assertEquals("3", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[1]/input[1]"));
        $this->assertEquals("21", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[1]/input[2]"));
        $this->assertEquals("oxprice2article__oxaddabs", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[2]/select[1]"));
        $this->assertEquals("12", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[2]/input[1]"));
        $this->assertEquals("22", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[1]/input[1]"));
        $this->assertEquals("99999", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[1]/input[2]"));
        $this->assertEquals("20", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[2]/input[1]"));
        $this->assertEquals("oxprice2article__oxaddperc", $this->getValue("//fieldset[@title='Scale Prices']/table/tbody/tr[3]/td[2]/select[1]"));

        //delete created prices
        $this->clickAndConfirm("//fieldset[@title='Scale Prices']/table/tbody/tr[2]/td[3]/a");
        $this->assertEquals("5", $this->getXpathCount("//fieldset[@title='Scale Prices']/table/tbody/tr"));
        $this->clickAndConfirm("//fieldset[@title='Scale Prices']/table/tbody/tr[1]/td[3]/a");
        $this->assertEquals("2", $this->getXpathCount("//fieldset[@title='Scale Prices']/table/tbody/tr"));
    }

    /**
     * creating Product. Variants
     *
     * @group creatingitems
     */
    public function testCreateProductVariants()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->frame("edit");
        //Product tab
        $this->clickCreateNewItem();
        $this->type("editval[oxarticles__oxtitle]", "create_delete product");
        $this->type("editval[oxarticles__oxartnum]", "10000");
        $this->type("editval[oxarticles__oxprice]", "5.9");
        $this->waitForEditable("saveArticle");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->assertEquals("create_delete product", $this->getValue("editval[oxarticles__oxtitle]"));
        $this->clickAndWaitFrame("/descendant::input[@name='save'][2]", "list");
        $this->assertEquals("create_delete product", $this->getValue("editval[oxarticles__oxtitle]"));
        // Variants tab
        $this->frame("list");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->openTab("Variants");
        $this->changeAdminEditLanguage("English", "editlanguage");
        $this->assertEquals("English", $this->getSelectedLabel("editlanguage"));
        $this->type("editval[oxarticles__oxvarname]", "selection name [EN]_šÄßüл");
        $this->clickAndWait("//input[@value='Save Selection']");
        $this->type("editval[oxarticles__oxvarselect]", "var 1 [EN]_šÄßüл");
        $this->type("editval[oxarticles__oxartnum]", "10001-1_šÄßüл");
        $this->type("editval[oxarticles__oxprice]", "75");
        $this->type("editval[oxarticles__oxsort]", "1");
        $this->type("editval[oxarticles__oxstock]", "5");
        $this->select("editval[oxarticles__oxstockflag]", "label=If out of Stock, offline");
        $this->clickAndWait("//input[@value='New Variant']");
        $this->selectAndWait("editlanguage", "label=Deutsch");
        $this->assertEquals("", $this->getValue("editval[oxarticles__oxvarname]"));
        $this->type("editval[oxarticles__oxvarname]", "selection name [DE]");
        $this->clickAndWait("//input[@value='Save Selection']");
        $this->assertEquals("", $this->getValue("//tr[@id='test_variant.1']/td[3]/input"));
        $this->type("//tr[@id='test_variant.1']/td[3]/input", "var 1 [DE]");
        $this->check("//tr[@id='test_variant.1']/td[2]/input");
        $this->assertEquals("10001-1_šÄßüл", $this->getValue("//tr[@id='test_variant.1']/td[4]/input"));
        $this->assertEquals("75", $this->getValue("//tr[@id='test_variant.1']/td[5]/input"));
        $this->assertEquals("1", $this->getValue("//tr[@id='test_variant.1']/td[6]/input"));
        $this->assertEquals("5", $this->getValue("//tr[@id='test_variant.1']/td[7]/input"));
        $this->assertEquals("If out of Stock, offline", $this->getSelectedLabel("//tr[@id='test_variant.1']/td[8]/select"));
        $this->select("//tr[@id='test_variant.1']/td[8]/select", "label=If out of Stock, offline");
        $this->clickAndWait("//input[@value=' Save Variants']");
        $this->assertEquals("If out of Stock, offline", $this->getSelectedLabel("//tr[@id='test_variant.1']/td[8]/select"));
        $this->assertEquals("var 1 [DE]", $this->getValue("//tr[@id='test_variant.1']/td[3]/input"));
        $this->assertEquals("selection name [DE]", $this->getValue("editval[oxarticles__oxvarname]"));
        $this->selectAndWait("editlanguage", "label=English");
        $this->assertEquals("var 1 [EN]_šÄßüл", $this->getValue("//tr[@id='test_variant.1']/td[3]/input"));
        $this->assertEquals("selection name [EN]_šÄßüл", $this->getValue("editval[oxarticles__oxvarname]"));
        //deleting variant
        $this->assertElementPresent("//input[@value='var 1 [EN]_šÄßüл']");
        $this->clickAndConfirm("//tr[@id='test_variant.1']/td[9]/a");
        $this->assertElementNotPresent("//input[@value='var 1 [EN]_šÄßüл']");
    }

    /**
     * creating Product. Media urls
     *
     * @group creatingitems
     */
    public function testCreateProductMedia()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->frame("edit");
        //Product tab
        $this->clickCreateNewItem();
        $this->type("editval[oxarticles__oxtitle]", "create_delete product");
        $this->type("editval[oxarticles__oxartnum]", "10000");
        $this->type("editval[oxarticles__oxprice]", "5.9");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->clickAndWaitFrame("/descendant::input[@name='save'][2]", "list");
        $this->selectAndWait("test_editlanguage", "label=English");
        //Extended tab
        $this->openTab("Extended");
        $this->type("mediaUrl", "https://www.youtube.com/watch?v=iQ69Hv8WP_g");
        $this->clickAndWait("save");
        $this->assertTextPresent("Please enter description");
        $this->type("mediaDesc", "media file [EN]_šÄßüл");
        $this->type("mediaUrl", "https://www.youtube.com/watch?v=iQ69Hv8WP_g");
        $this->clickAndWait("save");
        $this->assertEquals("media file [EN]_šÄßüл", $this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("", $this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input"));
        $this->type("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input", "media desc [DE]");
        $this->clickAndWait("//fieldset[@title='Media URLs']/table/tbody/tr[2]/td/input");
        $this->assertEquals("media desc [DE]", $this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("media file [EN]_šÄßüл", $this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input"));
        $this->type("mediaDesc", "second media file");
        $this->type("mediaUrl", "https://www.youtube.com/watch?v=iQ69Hv8WP_g");
        $this->clickAndWait("save");
        $this->assertElementPresent("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input");
        $this->assertElementPresent("//fieldset[@title='Media URLs']/table/tbody/tr[2]/td[3]/input");

        $expected = array('media file [EN]_šÄßüл',
                          'second media file');
        $result = array($this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input"),
                        $this->getValue("//fieldset[@title='Media URLs']/table/tbody/tr[2]/td[3]/input"));

        sort($result);
        $this->assertEquals($expected, $result);

        $this->clickAndConfirm("//fieldset[@title='Media URLs']/table/tbody/tr[2]/td[2]/a");
        $this->assertElementNotPresent("//fieldset[@title='Media URLs']/table/tbody/tr[2]/td[3]/input");

        $this->clickAndConfirm("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[2]/a");
        $this->assertElementNotPresent("//fieldset[@title='Media URLs']/table/tbody/tr[1]/td[3]/input");
    }

    /**
     * creating Product. Multidimensional variants
     *
     * @group creatingitems
     */
    public function testCreateProductMultidimensionalVariants()
    {
        $this->executeSql("DELETE FROM `oxconfig` WHERE `OXVARNAME`='blUseMultidimensionVariants'");

        $shopId = ShopIdCalculator::BASE_SHOP_ID;
        $shopId = $this->getTestConfig()->isSubShop() ? 2 : $shopId;
        $id = $this->getTestConfig()->isSubShop() ? 'ee3uioiop3795dea7855be2d1e' : '9d1ef0f8237werea96756e2d1e';
        $this->executeSql("INSERT INTO `oxconfig` (`OXID`, `OXSHOPID`, `OXVARNAME`, `OXVARTYPE`, `OXVARVALUE`) VALUES ('$id', '$shopId', 'blUseMultidimensionVariants', 'bool', 0x07);");

        $this->loginAdmin("Administer Products", "Products");
        $this->openListItem("link=10010");
        $this->assertEquals("1.5", $this->getValue("editval[oxarticles__oxprice]"));
        $this->type("editval[oxarticles__oxprice]", "2.5");
        $this->clickAndWaitFrame("saveArticle", "list");
        $this->openTab("Variants");
        $this->changeAdminEditLanguage('Deutsch', 'editlanguage');
        $this->type("editval[oxarticles__oxvarname]", "selection de");
        $this->clickAndWait("//input[@value='Save Selection']");
        $this->assertElementNotPresent("//tr[@id='test_variant.1']");
        $this->addSelection("allsel[]", "label=test selection list [EN] šÄßüл | test sellist šÄßüл");
        $this->clickAndWait("//b");

        // String to access variant inputs (d1 = variant id, d2 = table column id)
        $inputPath = "//tr[@id='test_variant.%d']/td[%d]/input";

        // gets values of all three variant input fields: Title, Code, Price
        $variantValues = function ($index) use ($inputPath) {
            // 3 = Title, 4 = Code, 5 = Price
            return [
                $this->getValue(sprintf($inputPath, $index, 3)),
                $this->getValue(sprintf($inputPath, $index, 4)),
                $this->getValue(sprintf($inputPath, $index, 5))
            ];
        };

        $this->waitForItemAppear(sprintf($inputPath, 1, 3)."[@value='selvar1 [DE]']");
        $this->assertEquals(["selvar1 [DE]", "10010-1", "3.5"], $variantValues(1));
        $this->assertEquals(["selvar2 [DE]", "10010-2", "2.5"], $variantValues(2));
        $this->assertEquals(["selvar3 [DE]", "10010-3", "0.5"], $variantValues(3));

        $this->addSelection("allsel[]", "label=test selection list [EN] šÄßüл | test sellist šÄßüл");
        $this->clickAndWait("//b");

        $this->waitForItemAppear(sprintf($inputPath, 1, 3)."[@value='selvar1 [DE] | selvar1 [DE]']");
        $this->assertEquals(["selvar1 [DE] | selvar1 [DE]", "10010-1", "4.5"], $variantValues(1));
        $this->assertEquals(["selvar1 [DE] | selvar2 [DE]", "10010-1-1", "3.5"], $variantValues(2));
        $this->assertEquals(["selvar1 [DE] | selvar3 [DE]", "10010-1-2", "1.5"], $variantValues(3));
        $this->assertEquals(["selvar1 [DE] | selvar4 [DE]", "10010-1-3", "3.55"], $variantValues(4));
        $this->assertEquals(["selvar2 [DE] | selvar1 [DE]", "10010-2", "3.5"], $variantValues(5));
        $this->assertEquals(["selvar2 [DE] | selvar2 [DE]", "10010-2-1", "2.5"], $variantValues(6));
        $this->assertEquals(["selvar2 [DE] | selvar3 [DE]", "10010-2-2", "0.5"], $variantValues(7));
        $this->assertEquals(["selvar2 [DE] | selvar4 [DE]", "10010-2-3", "2.55"], $variantValues(8));
        $this->assertEquals(["selvar3 [DE] | selvar1 [DE]", "10010-3", "1.5"], $variantValues(9));
        $this->assertEquals(["selvar3 [DE] | selvar2 [DE]", "10010-3-1", "0.5"], $variantValues(10));
        $this->assertEquals(["selvar3 [DE] | selvar3 [DE]", "10010-3-2", "-1.5"], $variantValues(11));
        $this->assertEquals(["selvar3 [DE] | selvar4 [DE]", "10010-3-3", "0.55"], $variantValues(12));
        $this->assertEquals(["selvar4 [DE] | selvar1 [DE]", "10010-4", "3.55"], $variantValues(13));
        $this->assertEquals(["selvar4 [DE] | selvar2 [DE]", "10010-4-1", "2.55"], $variantValues(14));
        $this->assertEquals(["selvar4 [DE] | selvar3 [DE]", "10010-4-2", "0.55"], $variantValues(15));
        $this->assertEquals(["selvar4 [DE] | selvar4 [DE]", "10010-4-3", "2.6"], $variantValues(16));

        $this->changeAdminEditLanguage('English', 'editlanguage');

        $this->waitForItemAppear(sprintf($inputPath, 1, 3)."[@value='selvar1 [EN] šÄßüл | selvar1 [EN] šÄßüл']");
        $this->assertEquals("selvar1 [EN] šÄßüл | selvar2 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.2']/td[3]/input"));
        $this->assertEquals("selvar1 [EN] šÄßüл | selvar3 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.3']/td[3]/input"));
        $this->assertEquals("selvar1 [EN] šÄßüл | selvar4 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.4']/td[3]/input"));
        $this->assertEquals("selvar2 [EN] šÄßüл | selvar1 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.5']/td[3]/input"));
        $this->assertEquals("selvar2 [EN] šÄßüл | selvar2 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.6']/td[3]/input"));
        $this->assertEquals("selvar2 [EN] šÄßüл | selvar3 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.7']/td[3]/input"));
        $this->assertEquals("selvar2 [EN] šÄßüл | selvar4 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.8']/td[3]/input"));
        $this->assertEquals("selvar3 [EN] šÄßüл | selvar1 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.9']/td[3]/input"));
        $this->assertEquals("selvar3 [EN] šÄßüл | selvar2 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.10']/td[3]/input"));
        $this->assertEquals("selvar3 [EN] šÄßüл | selvar3 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.11']/td[3]/input"));
        $this->assertEquals("selvar3 [EN] šÄßüл | selvar4 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.12']/td[3]/input"));
        $this->assertEquals("selvar4 [EN] šÄßüл | selvar1 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.13']/td[3]/input"));
        $this->assertEquals("selvar4 [EN] šÄßüл | selvar2 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.14']/td[3]/input"));
        $this->assertEquals("selvar4 [EN] šÄßüл | selvar3 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.15']/td[3]/input"));
        $this->assertEquals("selvar4 [EN] šÄßüл | selvar4 [EN] šÄßüл", $this->getValue("//tr[@id='test_variant.16']/td[3]/input"));
        $this->clickAndConfirm("//tr[@id='test_variant.16']/td[9]/a");
        $this->assertElementPresent("test_variant.15");
        $this->assertElementNotPresent("test_variant.16");
        $this->openTab("Main");
        $this->assertEquals("1 EN product šÄßüл - selvar1 [EN] šÄßüл | selvar1 [EN] šÄßüл - selvar1 [EN] šÄßüл | selvar2 [EN] šÄßüл - selvar1 [EN] šÄßüл | selvar3 [EN] šÄßüл - selvar1 [EN] šÄßüл | selvar4 [EN] šÄßüл - selvar2 [EN] šÄßüл | selvar1 [EN] šÄßüл - selvar2 [EN] šÄßüл | selvar2 [EN] šÄßüл - selvar2 [EN] šÄßüл | selvar3 [EN] šÄßüл - selvar2 [EN] šÄßüл | selvar4 [EN] šÄßüл - selvar3 [EN] šÄßüл | selvar1 [EN] šÄßüл - selvar3 [EN] šÄßüл | selvar2 [EN] šÄßüл - selvar3 [EN] šÄßüл | selvar3 [EN] šÄßüл - selvar3 [EN] šÄßüл | selvar4 [EN] šÄßüл - selvar4 [EN] šÄßüл | selvar1 [EN] šÄßüл - selvar4 [EN] šÄßüл | selvar2 [EN] šÄßüл - selvar4 [EN] šÄßüл | selvar3 [EN] šÄßüл", $this->clearString($this->getText("art_variants")));
    }

    /**
     * creating Attribute
     * @group admin
     * @group create
     * @group creatingitems
     */
    public function testCreateAttribute()
    {
        $this->loginAdmin("Administer Products", "Attributes");
        $this->changeAdminListLanguage('Deutsch');
        $this->type("where[oxattribute][oxtitle]", "attribute");
        $this->clickAndWait("submitit");
        $this->openListItem("link=Test attribute 1 [DE] šÄßüл");
        $this->assertEquals("Test attribute 1 [DE] šÄßüл", $this->getValue("editval[oxattribute__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("Test attribute 1 [EN] šÄßüл", $this->getValue("editval[oxattribute__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->type("editval[oxattribute__oxtitle]", "create_delete attribute [EN]_šÄßüл");
        $this->type("editval[oxattribute__oxpos]", "0");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("create_delete attribute [EN]_šÄßüл", $this->getValue("editval[oxattribute__oxtitle]"));
        $this->assertEquals("0", $this->getValue("editval[oxattribute__oxpos]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxattribute__oxtitle]", "create_delete attribute [DE]");
        $this->type("editval[oxattribute__oxpos]", "2");
        $this->clickAndWaitFrame("//input[@value='Save']", "list");
        $this->assertEquals("create_delete attribute [DE]", $this->getValue("editval[oxattribute__oxtitle]"));
        $this->assertEquals("2", $this->getValue("editval[oxattribute__oxpos]"));
        //testing if other tabs are working
        $this->checkTabs(array('Category', 'Mall'));
        //checking if item can be found
        $this->frame("list");
        $this->type("where[oxattribute][oxtitle]", "create_delete attribute");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete attribute [DE]", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating Category
     *
     * @group creatingitems
     */
    public function testCreateCategory()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] category šÄßüл");
        $this->assertEquals("1 [DE] category šÄßüл", $this->getValue("editval[oxcategories__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("[last] [EN] category šÄßüл", $this->getValue("editval[oxcategories__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxcategories__oxactive]"));
        $this->assertEquals("off", $this->getValue("editval[oxcategories__oxhidden]"));
        $this->check("editval[oxcategories__oxactive]");
        $this->check("editval[oxcategories__oxhidden]");
        $this->type("editval[oxcategories__oxtitle]", "create_delete category [EN]_šÄßüл");
        $this->type("editval[oxcategories__oxdesc]", "description [EN]_šÄßüл");
        $this->assertTextPresent("Test category 0 [EN] šÄßüл", "Bug #597 from Mantis");
        $this->select("editval[oxcategories__oxparentid]", "label=Test category 0 [EN] šÄßüл");
        $this->type("editval[oxcategories__oxsort]", "0");
        $this->type("editval[oxcategories__oxextlink]", "http://www.ENlink.com");
        $this->type("editval[oxcategories__oxtemplate]", "template [EN]_šÄßüл");
        $this->select("editval[oxcategories__oxdefsort]", "label=Product Number");
        $this->check("editval[oxcategories__oxdefsortmode]");
        $this->type("editval[oxcategories__oxpricefrom]", "5");
        $this->type("editval[oxcategories__oxpriceto]", "100");
        $this->type("editval[oxcategories__oxvat]", "10");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("/descendant::input[@name='save'][2]"));
        $this->clickAndWaitFrame("/descendant::input[@name='save'][2]", "list");
        $this->assertEquals("on", $this->getValue("editval[oxcategories__oxactive]"));
        $this->assertEquals("on", $this->getValue("editval[oxcategories__oxhidden]"));
        $this->assertEquals("create_delete category [EN]_šÄßüл", $this->getValue("editval[oxcategories__oxtitle]"));
        $this->type("editval[oxcategories__oxtitle]", "create_delete category [DE]");
        $this->assertEquals("description [EN]_šÄßüл", $this->getValue("editval[oxcategories__oxdesc]"));
        $this->type("editval[oxcategories__oxdesc]", "description [DE]");
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getSelectedLabel("editval[oxcategories__oxparentid]"), "Bug #597 from Mantis");
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxsort]"));
        $this->type("editval[oxcategories__oxsort]", "1");
        $this->assertEquals("http://www.ENlink.com", $this->getValue("editval[oxcategories__oxextlink]"));
        $this->type("editval[oxcategories__oxextlink]", "http://www.DElink.com");
        $this->assertEquals("template [EN]_šÄßüл", $this->getValue("editval[oxcategories__oxtemplate]"));
        $this->type("editval[oxcategories__oxtemplate]", "template [DE]");
        $this->assertEquals("Product Number", $this->getSelectedLabel("editval[oxcategories__oxdefsort]"));
        $this->assertEquals("0", $this->getValue("editval[oxcategories__oxdefsortmode]"));
        $this->assertEquals("5", $this->getValue("editval[oxcategories__oxpricefrom]"));
        $this->assertEquals("100", $this->getValue("editval[oxcategories__oxpriceto]"));
        $this->assertEquals("10", $this->getValue("editval[oxcategories__oxvat]"));
        $this->check("/descendant::input[@name='editval[oxcategories__oxdefsortmode]'][2]");
        $this->select("editval[oxcategories__oxdefsort]", "label=Title");
        $this->type("editval[oxcategories__oxpricefrom]", "50");
        $this->type("editval[oxcategories__oxpriceto]", "1000");
        $this->type("editval[oxcategories__oxvat]", "7");
        $this->uncheck("editval[oxcategories__oxactive]");
        $this->uncheck("editval[oxcategories__oxhidden]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("off", $this->getValue("editval[oxcategories__oxactive]"));
        $this->assertEquals("off", $this->getValue("editval[oxcategories__oxhidden]"));
        $this->assertEquals("create_delete category [DE]", $this->getValue("editval[oxcategories__oxtitle]"));
        $this->assertEquals("description [DE]", $this->getValue("editval[oxcategories__oxdesc]"));
        $this->assertEquals("Test category 0 [DE] šÄßüл", $this->getSelectedLabel("editval[oxcategories__oxparentid]"));
        $this->assertEquals("1", $this->getValue("editval[oxcategories__oxsort]"));
        $this->assertEquals("http://www.DElink.com", $this->getValue("editval[oxcategories__oxextlink]"));
        $this->assertEquals("template [DE]", $this->getValue("editval[oxcategories__oxtemplate]"));
        $this->assertEquals("Title", $this->getSelectedLabel("editval[oxcategories__oxdefsort]"));
        $this->assertEquals("1", $this->getValue("/descendant::input[@name='editval[oxcategories__oxdefsortmode]'][2]"));
        $this->assertEquals("50", $this->getValue("editval[oxcategories__oxpricefrom]"));
        $this->assertEquals("1000", $this->getValue("editval[oxcategories__oxpriceto]"));
        $this->assertEquals("7", $this->getValue("editval[oxcategories__oxvat]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("editval[oxcategories__oxactive]"));
        $this->assertEquals("off", $this->getValue("editval[oxcategories__oxhidden]"));
        $this->assertEquals("create_delete category [EN]_šÄßüл", $this->getValue("editval[oxcategories__oxtitle]"));
        $this->assertEquals("description [EN]_šÄßüл", $this->getValue("editval[oxcategories__oxdesc]"));
        $this->assertEquals("Test category 0 [EN] šÄßüл", $this->getSelectedLabel("editval[oxcategories__oxparentid]"));
        $this->assertEquals("1", $this->getValue("editval[oxcategories__oxsort]"));
        $this->assertEquals("http://www.DElink.com", $this->getValue("editval[oxcategories__oxextlink]"));
        $this->assertEquals("template [DE]", $this->getValue("editval[oxcategories__oxtemplate]"));
        $this->assertEquals("Title", $this->getSelectedLabel("editval[oxcategories__oxdefsort]"));
        $this->assertEquals("1", $this->getValue("/descendant::input[@name='editval[oxcategories__oxdefsortmode]'][2]"));
        $this->assertEquals("50", $this->getValue("editval[oxcategories__oxpricefrom]"));
        $this->assertEquals("1000", $this->getValue("editval[oxcategories__oxpriceto]"));
        $this->assertEquals("7", $this->getValue("editval[oxcategories__oxvat]"));
        //testing if other tabs are working
        $this->openTab("Text");
        $this->assertEquals("English", $this->getSelectedLabel("//select[@name='catlang']"));
        $this->assertEquals("", $this->getEditorValue("oxcategories__oxlongdesc"));
        $this->typeToEditor("oxcategories__oxlongdesc", "long desc [EN]_šÄßüл");
        $this->clickAndWait("save");

        $this->waitForElementText("long desc [EN]_šÄßüл", "editor_oxcategories__oxlongdesc");
        $this->changeAdminEditLanguage("Deutsch", "catlang");

        $this->waitForElementText("", "editor_oxcategories__oxlongdesc");
        $this->typeToEditor("oxcategories__oxlongdesc", "long desc [DE]");
        $this->clickAndWait("save");

        $this->waitForElementText("long desc [DE]", "editor_oxcategories__oxlongdesc");
        $this->changeAdminEditLanguage("English", "catlang");

        $this->waitForElementText("long desc [EN]_šÄßüл", "editor_oxcategories__oxlongdesc");
        $this->changeAdminEditLanguage("Deutsch", "catlang");

        $this->waitForElementText("long desc [DE]", "editor_oxcategories__oxlongdesc");
        $this->checkTabs(array('Picture', 'Sorting', 'SEO', 'Rights', 'Mall'));
        $this->frame("list");
        $this->type("where[oxcategories][oxtitle]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete category [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating Selection List
     *
     * @group creatingitems
     */
    public function testCreateSelectionList()
    {
        $this->loginAdmin("Administer Products", "Selection Lists");
        $this->changeAdminListLanguage('Deutsch');
        $this->openListItem("link=test selection list [DE] šÄßüл");
        $this->assertEquals("test selection list [DE] šÄßüл", $this->getValue("editval[oxselectlist__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("test selection list [EN] šÄßüл", $this->getValue("editval[oxselectlist__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->type("editval[oxselectlist__oxtitle]", "create_delete sellist [EN]_šÄßüл");
        $this->type("editval[oxselectlist__oxident]", "working title_šÄßüл");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->type("EditAddName", "field [EN]_šÄßüл");
        $this->type("EditAddPrice", "5");
        $this->select("EditAddPriceUnit", "label=%");
        $this->type("EditAddPos", "1");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("1 - field [EN]_šÄßüл,5%", $this->getText("//option[@value='1__@@field [EN]_šÄßüл__@@5%']"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("//input[@value='Copy to']"));
        $this->clickAndWaitFrame("//input[@value='Copy to']", "list");
        $this->type("editval[oxselectlist__oxtitle]", "create_delete sellist [DE]");
        $this->clickAndWaitFrame("//input[@name='save' and @value='Save']", "list");
        $this->type("EditAddName", "field [DE]");
        $this->type("EditAddPrice", "5");
        $this->select("EditAddPriceUnit", "label=%");
        $this->type("EditAddPos", "1");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete sellist [DE]", $this->getValue("editval[oxselectlist__oxtitle]"));
        $this->assertEquals("1 - field [DE],5%", $this->getText("//option[@value='1__@@field [DE]__@@5%']"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("create_delete sellist [EN]_šÄßüл", $this->getValue("editval[oxselectlist__oxtitle]"));
        $this->assertEquals("1 - field [EN]_šÄßüл,5%", $this->getText("//option[@value='1__@@field [EN]_šÄßüл__@@5%']"));
        $this->assertEquals("working title_šÄßüл", $this->getValue("editval[oxselectlist__oxident]"));
        $this->addSelection("aFields", "label=1 - field [EN]_šÄßüл,5%");
        $this->type("EditAddName", "field1 [EN]");
        $this->type("EditAddPrice", "50");
        $this->type("EditAddPos", "2");
        $this->clickAndWaitFrame("submit_modify", "edit");
        $this->assertTextPresent("Sorting out of bounds");
        $this->addSelection("aFields", "label=1 - field [EN]_šÄßüл,5%");
        $this->type("EditAddName", "field1 [EN]");
        $this->type("EditAddPrice", "10");
        $this->type("EditAddPos", "0");
        $this->clickAndWaitFrame("submit_modify", "list");
        $this->assertEquals("1 - field1 [EN],10%", $this->getText("//option[@value='1__@@field1 [EN]__@@10%']"));
        $this->changeAdminEditLanguage("Deutsch", "test_editlanguage");
        $this->assertEquals("1 - field [DE],5%", $this->getText("//option[@value='1__@@field [DE]__@@5%']"));
        $this->assertEquals("create_delete sellist [DE]", $this->getValue("editval[oxselectlist__oxtitle]"));
        $this->addSelection("aFields", "label=1 - field [DE],5%");
        $this->waitForEditable('submit_delete');
        usleep(500000); // fix for unclear random case when submit_delete button click does not work
        $this->clickAndWaitFrame("submit_delete", "list");
        $this->assertEquals("", $this->getText("aFields"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("1 - field1 [EN],10%", $this->getText("//option[@value='1__@@field1 [EN]__@@10%']"));

        $this->checkTabs(array('Mall'));
        $this->frame("list");
        $this->type("where[oxselectlist][oxtitle]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete sellist [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[1]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating User
     *
     * @group creatingitems
     */
    public function testCreateUserMainInfo()
    {
        //Main tab
        $this->loginAdmin("Administer Users", "Users");
        $this->frame("edit");
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxuser__oxactive]"));
        $this->check("editval[oxuser__oxactive]");
        $this->type("editval[oxuser__oxusername]", "example01@oxid-esales.dev");
        $this->type("editval[oxuser__oxcustnr]", "20");
        $this->select("editval[oxuser__oxsal]", "label=Mrs");
        $this->type("editval[oxuser__oxfname]", "Name_šÄßüл");
        $this->type("editval[oxuser__oxlname]", "Surname_šÄßüл");
        $this->type("editval[oxuser__oxcompany]", "company_šÄßüл");
        $this->type("editval[oxuser__oxstreet]", "street_šÄßüл");
        $this->type("editval[oxuser__oxstreetnr]", "1");
        $this->type("editval[oxuser__oxzip]", "3000");
        $this->type("editval[oxuser__oxcity]", "City_šÄßüл");
        $this->type("editval[oxuser__oxustid]", "111222");
        $this->type("editval[oxuser__oxaddinfo]", "additional info_šÄßüл");
        $this->select("editval[oxuser__oxcountryid]", "label=Germany");
        $this->type("editval[oxuser__oxstateid]", "BW");
        $this->type("editval[oxuser__oxfon]", "111222333");
        $this->type("editval[oxuser__oxfax]", "222333444");
        $this->type("editval[oxuser__oxbirthdate][day]", "01");
        $this->type("editval[oxuser__oxbirthdate][month]", "12");
        $this->type("editval[oxuser__oxbirthdate][year]", "1980");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("on", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("Customer", $this->getSelectedLabel("editval[oxuser__oxrights]"));
        $this->assertEquals("example01@oxid-esales.dev", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("20", $this->getValue("editval[oxuser__oxcustnr]"));
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxuser__oxsal]"));
        $this->assertEquals("Name_šÄßüл", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("Surname_šÄßüл", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("company_šÄßüл", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("street_šÄßüл", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("3000", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("City_šÄßüл", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("111222", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("additional info_šÄßüл", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Germany", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("BW", $this->getValue("editval[oxuser__oxstateid]"));
        $this->assertEquals("111222333", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("222333444", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("01", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("12", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1980", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        //substr used because help popup is in same td and selenium gets whole text as a result
        $this->assertEquals("No", substr($this->clearString($this->getText("//form[@id='myedit']/table/tbody/tr/td[1]/table/tbody/tr[17]/td[2]")), 0, 2));
        $this->assertEquals("", $this->getValue("newPassword"));
        $this->uncheck("editval[oxuser__oxactive]");
        $this->select("editval[oxuser__oxrights]", "label=Admin");
        $this->type("newPassword", "adminpass");
        $this->type("editval[oxuser__oxusername]", "example00@oxid-esales.dev");
        $this->type("editval[oxuser__oxcustnr]", "121");
        $this->select("editval[oxuser__oxsal]", "label=Mr");
        $this->type("editval[oxuser__oxfname]", "Name1");
        $this->type("editval[oxuser__oxlname]", "Surname1");
        $this->type("editval[oxuser__oxcompany]", "company1");
        $this->type("editval[oxuser__oxstreet]", "street1");
        $this->type("editval[oxuser__oxstreetnr]", "11");
        $this->type("editval[oxuser__oxzip]", "30001");
        $this->type("editval[oxuser__oxcity]", "City11");
        $this->type("editval[oxuser__oxaddinfo]", "additional info1");
        $this->select("editval[oxuser__oxcountryid]", "label=Belgium");
        $this->type("editval[oxuser__oxstateid]", "BE");
        $this->type("editval[oxuser__oxfon]", "1112223331");
        $this->type("editval[oxuser__oxfax]", "2223334441");
        $this->type("editval[oxuser__oxbirthdate][day]", "03");
        $this->type("editval[oxuser__oxbirthdate][month]", "13");
        $this->type("editval[oxuser__oxbirthdate][year]", "1979");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("off", $this->getValue("editval[oxuser__oxactive]"));
        $this->assertEquals("Admin", $this->getSelectedLabel("editval[oxuser__oxrights]"));
        $this->assertEquals("example00@oxid-esales.dev", $this->getValue("editval[oxuser__oxusername]"));
        $this->assertEquals("121", $this->getValue("editval[oxuser__oxcustnr]"));
        $this->assertEquals("Name1", $this->getValue("editval[oxuser__oxfname]"));
        $this->assertEquals("Surname1", $this->getValue("editval[oxuser__oxlname]"));
        $this->assertEquals("company1", $this->getValue("editval[oxuser__oxcompany]"));
        $this->assertEquals("street1", $this->getValue("editval[oxuser__oxstreet]"));
        $this->assertEquals("11", $this->getValue("editval[oxuser__oxstreetnr]"));
        $this->assertEquals("30001", $this->getValue("editval[oxuser__oxzip]"));
        $this->assertEquals("City11", $this->getValue("editval[oxuser__oxcity]"));
        $this->assertEquals("111222", $this->getValue("editval[oxuser__oxustid]"));
        $this->assertEquals("additional info1", $this->getValue("editval[oxuser__oxaddinfo]"));
        $this->assertEquals("Belgium", $this->getSelectedLabel("editval[oxuser__oxcountryid]"));
        $this->assertEquals("BE", $this->getValue("editval[oxuser__oxstateid]"));
        $this->assertEquals("1112223331", $this->getValue("editval[oxuser__oxfon]"));
        $this->assertEquals("2223334441", $this->getValue("editval[oxuser__oxfax]"));
        $this->assertEquals("03", $this->getValue("editval[oxuser__oxbirthdate][day]"));
        $this->assertEquals("01", $this->getValue("editval[oxuser__oxbirthdate][month]"));
        $this->assertEquals("1979", $this->getValue("editval[oxuser__oxbirthdate][year]"));
        $this->assertEquals("Yes", substr($this->clearString($this->getText("//form[@id='myedit']/table/tbody/tr/td[1]/table/tbody/tr[17]/td[2]")), 0, 3));
        $this->assertEquals("", $this->getValue("newPassword"));
        // Extended tab
        $this->openTab("Extended");
        $this->assertEquals("Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331", $this->clearString($this->getText("test_userAddress")));
        //History tab
        $this->openTab("History");
        $this->assertNotEquals("", $this->getText("//select[@name='rem_oxid']"));
        $this->clickCreateNewItem("btn.newremark");
        $this->type("remarktext", "new note_šÄßüл");
        $this->clickAndWait("save");
        $this->selectAndWaitFrame("//select[@name='rem_oxid']", "index=0");
        $this->assertEquals("new note_šÄßüл", $this->getValue("remarktext"));
        $this->clickAndWait("//input[@value='Delete']");
        $this->assertNotEquals("", $this->getText("//select[@name='rem_oxid']"));
        $this->selectAndWait("//select[@name='rem_oxid']", "index=0");
        $this->assertNotEquals("new note_šÄßüл", $this->getValue("remarktext"));
        //testing if other tabs are working
        $this->checkTabs(array('Products', 'Payment'));
        //checking if created item can be found
        $this->frame("list");
        $this->type("where[oxuser][oxusername]", "example00");
        $this->clickAndWait("submitit");
        $this->assertEquals("example00@oxid-esa...", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating User. Extended tab
     *
     * @group creatingitems
     */
    public function testCreateUserExtendedInfo()
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $sql = "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`,
                                      `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`,
                                      `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`,
                                      `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`)
                              VALUES ('kdiruuc', 0, 'malladmin', ".ShopIdCalculator::BASE_SHOP_ID.", 'example00@oxid-esales.dev', '89bb88b81f9b3669fc4c44e082dd9927', '3032396331663033316535343361356231363666653666316533376235353830',
                                      121, '111222', 'company1', 'Name1', 'Surname1', 'street1', '11', 'additional info1', 'City11',
                                      'a7c40f632e04633c9.47194042', 'BE', '30001', '1112223331', '2223334441', 'MR', 1000, '2010-02-05 10:22:37', '2010-02-05 10:22:48',
                                      '', '', '1979-01-03', '', '', 0);";
        } else {
            $shopId = $this->getTestConfig()->isSubShop() ? 2 : 1;
            $sql = "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`,
                                  `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`,
                                  `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`,
                                  `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXLDAPKEY`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`)
                          VALUES ('ddesdr', 0, 'malladmin', $shopId, 'example00@oxid-esales.dev', '1397d0b4392f452a5bd058891c9b255e', '6634653730386562303132363065393735333334386530353233323337346435',
                                  121, '111222', 0, 'company1', 'Name1', 'Surname1', 'street1', '11', 'additional info1', 'City11',
                                  'a7c40f632e04633c9.47194042', 'BE', '30001', '1112223331', '2223334441', 'MR', 1000, '2010-02-05 09:23:13', '2010-02-05 09:23:19',
                                  '', '', '1979-01-03', '', '', 0, '', 0);";
        }
        $this->executeSql($sql);
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWaitFrame("link=example00@oxid-esa...", "edit");
        // Extended tab
        $this->openTab("Extended");
        $this->assertEquals("Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331", $this->clearString($this->getText("test_userAddress")));
        $this->type("editval[oxuser__oxprivfon]", "555444555");
        $this->type("editval[oxuser__oxmobfon]", "666555666");
        $this->assertEquals("off", $this->getValue("/descendant::input[@name='editnews'][2]"));
        $this->check("/descendant::input[@name='editnews'][2]");
        $this->assertEquals("off", $this->getValue("/descendant::input[@name='emailfailed'][2]"));
        $this->check("/descendant::input[@name='emailfailed'][2]");
        $this->assertEquals("1000", $this->getValue("editval[oxuser__oxboni]"));
        $this->type("editval[oxuser__oxboni]", "1500");
        $this->type("editval[oxuser__oxurl]", "http://www.url.com");
        $this->clickAndWait("save");
        $this->assertEquals("555444555", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("666555666", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->assertEquals("on", $this->getValue("/descendant::input[@name='editnews'][2]"));
        $this->assertEquals("on", $this->getValue("/descendant::input[@name='emailfailed'][2]"));
        $this->assertEquals("1500", $this->getValue("editval[oxuser__oxboni]"));
        $this->assertEquals("http://www.url.com", $this->getValue("editval[oxuser__oxurl]"));
        $this->uncheck("/descendant::input[@name='editnews'][2]");
        $this->uncheck("/descendant::input[@name='emailfailed'][2]");
        $this->type("editval[oxuser__oxboni]", "1000");
        $this->type("editval[oxuser__oxprivfon]", "5554445551");
        $this->type("editval[oxuser__oxmobfon]", "6665556661");
        $this->type("editval[oxuser__oxurl]", "http://www.url1.com");
        $this->clickAndWait("save");
        $this->assertEquals("5554445551", $this->getValue("editval[oxuser__oxprivfon]"));
        $this->assertEquals("6665556661", $this->getValue("editval[oxuser__oxmobfon]"));
        $this->assertEquals("off", $this->getValue("/descendant::input[@name='editnews'][2]"));
        $this->assertEquals("off", $this->getValue("/descendant::input[@name='emailfailed'][2]"));
        $this->assertEquals("1000", $this->getValue("editval[oxuser__oxboni]"));
        $this->assertEquals("http://www.url1.com", $this->getValue("editval[oxuser__oxurl]"));
    }

    /**
     * creating User
     *
     * @group creatingitems
     */
    public function testCreateUserAddresses()
    {
        if ($this->getTestConfig()->getShopEdition() != 'EE') {
            $sql = "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`,
                                      `OXCUSTNR`, `OXUSTID`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`,
                                      `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`,
                                      `OXPRIVFON`, `OXMOBFON`, `OXBIRTHDATE`, `OXURL`, `OXUPDATEKEY`, `OXUPDATEEXP`)
                              VALUES ('kdiruuc', 0, 'malladmin', 1, 'example00@oxid-esales.dev', '89bb88b81f9b3669fc4c44e082dd9927', '3032396331663033316535343361356231363666653666316533376235353830',
                                      121, '111222', 'company1', 'Name1', 'Surname1', 'street1', '11', 'additional info1', 'City11',
                                       'a7c40f632e04633c9.47194042', 'BE', '30001', '1112223331', '2223334441', 'MR', 1000, '2010-02-05 10:22:37', '2010-02-05 10:22:48',
                                      '5554445551', '6665556661', '1979-01-03', 'http://www.url1.com', '', 0);";
        } else {
            $shopId = $this->getTestConfig()->isSubShop() ? 2 : 1;
            $sql = "INSERT INTO `oxuser` (`OXID`, `OXACTIVE`, `OXRIGHTS`, `OXSHOPID`, `OXUSERNAME`, `OXPASSWORD`, `OXPASSSALT`,
                                     `OXCUSTNR`, `OXUSTID`, `OXUSTIDSTATUS`, `OXCOMPANY`, `OXFNAME`, `OXLNAME`, `OXSTREET`, `OXSTREETNR`, `OXADDINFO`, `OXCITY`,
                                     `OXCOUNTRYID`, `OXSTATEID`, `OXZIP`, `OXFON`, `OXFAX`, `OXSAL`, `OXBONI`, `OXCREATE`, `OXREGISTER`, `OXPRIVFON`, `OXMOBFON`,
                                     `OXBIRTHDATE`, `OXURL`, `OXLDAPKEY`, `OXWRONGLOGINS`, `OXUPDATEKEY`, `OXUPDATEEXP`)
                             VALUES ('ddesdr', 0, 'malladmin', $shopId, 'example00@oxid-esales.dev', '1397d0b4392f452a5bd058891c9b255e', '6634653730386562303132363065393735333334386530353233323337346435',
                                     121, '111222', 0, 'company1', 'Name1', 'Surname1', 'street1', '11', 'additional info1', 'City11',
                                     'a7c40f632e04633c9.47194042', 'BE', '30001', '1112223331', '2223334441', 'MR', 1000, '2010-02-05 09:23:13', '2010-02-05 09:23:19', '5554445551', '6665556661',
                                     '1979-01-03', 'http://www.url1.com', '', 0, '', 0);";
        }
        $this->executeSql($sql);
        $this->loginAdmin("Administer Users", "Users");
        $this->clickAndWaitFrame("link=example00@oxid-esa...", "edit");
        //Addresses tab
        $this->openTab("Addresses");
        //creating addresses
        $this->assertEquals("-", $this->getSelectedLabel("//select"));
        $this->clickCreateNewItem("btn.newaddress");
        $this->select("editval[oxaddress__oxsal]", "label=Mr");
        $this->type("editval[oxaddress__oxfname]", "shipping name_šÄßüл");
        $this->type("editval[oxaddress__oxlname]", "shipping surname_šÄßüл");
        $this->type("editval[oxaddress__oxcompany]", "shipping company_šÄßüл");
        $this->type("editval[oxaddress__oxstreet]", "shipping street_šÄßüл");
        $this->type("editval[oxaddress__oxstreetnr]", "1");
        $this->type("editval[oxaddress__oxzip]", "1000");
        $this->type("editval[oxaddress__oxcity]", "shipping city_šÄßüл");
        $this->type("editval[oxaddress__oxaddinfo]", "shipping additional info_šÄßüл");
        $this->select("editval[oxaddress__oxcountryid]", "label=Italy");
        $this->type("editval[oxaddress__oxfon]", "7778788");
        $this->type("editval[oxaddress__oxfax]", "8887877");
        $this->clickAndWait("save");
        $this->assertEquals("shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл", $this->getSelectedLabel("//select"));
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("shipping name_šÄßüл", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("shipping surname_šÄßüл", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("shipping company_šÄßüл", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("shipping street_šÄßüл", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("1000", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("shipping city_šÄßüл", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("Italy", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("7778788", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("8887877", $this->getValue("editval[oxaddress__oxfax]"));
        $this->clickCreateNewItem("btn.newaddress");
        $this->select("editval[oxaddress__oxsal]", "label=Mrs");
        $this->type("editval[oxaddress__oxfname]", "name2");
        $this->type("editval[oxaddress__oxlname]", "last name 2");
        $this->type("editval[oxaddress__oxcompany]", "company 2");
        $this->type("editval[oxaddress__oxstreet]", "street2");
        $this->type("editval[oxaddress__oxstreetnr]", "12");
        $this->type("editval[oxaddress__oxzip]", "2001");
        $this->type("editval[oxaddress__oxcity]", "city2");
        $this->type("editval[oxaddress__oxaddinfo]", "additional info2");
        $this->select("editval[oxaddress__oxcountryid]", "label=Portugal");
        $this->type("editval[oxaddress__oxfon]", "999666");
        $this->type("editval[oxaddress__oxfax]", "666999");
        $this->clickAndWait("save");
        //deleting addresses
        $this->selectAndWait("oxaddressid", "-");
        $this->selectAndWait("oxaddressid", "label=name2 last name 2, street2, city2");
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("name2", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("last name 2", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("company 2", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("street2", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("12", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("2001", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("city2", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("additional info2", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Portugal", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("999666", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("666999", $this->getValue("editval[oxaddress__oxfax]"));
        $this->selectAndWait("oxaddressid", "label=shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл");
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("shipping name_šÄßüл", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("shipping surname_šÄßüл", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("shipping company_šÄßüл", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("shipping street_šÄßüл", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("1", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("1000", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("shipping city_šÄßüл", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("shipping additional info_šÄßüл", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Italy", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("7778788", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("8887877", $this->getValue("editval[oxaddress__oxfax]"));
        $this->clickAndWait("//input[@value='Delete']");
        $this->assertElementPresent("oxaddressid", "Failed to delete address in Admin: Users -> Addresses tab");
        $this->assertEquals("- name2 last name 2, street2, city2", $this->clearString($this->getText("oxaddressid")));
        $this->selectAndWait("oxaddressid", "label=name2 last name 2, street2, city2");
        $this->assertEquals("Mrs", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("name2", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("last name 2", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("company 2", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("street2", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("12", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("2001", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("city2", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("additional info2", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Portugal", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("999666", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("666999", $this->getValue("editval[oxaddress__oxfax]"));
        $this->clickAndWait("//input[@value='Delete']");
        $this->assertEquals("-", $this->getText("//select"));
        $this->assertEquals("Mr", $this->getSelectedLabel("editval[oxaddress__oxsal]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxfname]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxlname]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxcompany]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxstreet]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxstreetnr]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxzip]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxcity]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxaddinfo]"));
        $this->assertEquals("Austria", $this->getSelectedLabel("editval[oxaddress__oxcountryid]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxfon]"));
        $this->assertEquals("", $this->getValue("editval[oxaddress__oxfax]"));
        $this->assertElementPresent("save");
    }

    /**
     * creating User Groups
     *
     * @group creatingitems
     */
    public function testCreateUserGroups()
    {
        //Main tab
        $this->loginAdmin("Administer Users", "User Groups");
        $this->frame("edit");
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxgroups__oxactive]"));
        $this->check("editval[oxgroups__oxactive]");
        $this->type("editval[oxgroups__oxtitle]", "create_delete group_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("on", $this->getValue("editval[oxgroups__oxactive]"));
        $this->assertEquals("create_delete group_šÄßüл", $this->getValue("editval[oxgroups__oxtitle]"));
        $this->uncheck("editval[oxgroups__oxactive]");
        $this->type("editval[oxgroups__oxtitle]", "create_delete group1");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("off", $this->getValue("editval[oxgroups__oxactive]"));
        $this->assertEquals("create_delete group1", $this->getValue("editval[oxgroups__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("create_delete group_šÄßüл", $this->getValue("editval[oxgroups__oxtitle]"));
        $this->frame("list");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->type("where[oxgroups][oxtitle]", "create_delete group");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete group_šÄßüл", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->changeAdminListLanguage("Deutsch");
        $this->assertEquals("create_delete group1", $this->getText("//tr[@id='row.1']/td[2]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[1]");
    }

    /**
     * creating News
     *
     * @group creatingitems
     * @group quarantine
     */
    public function testCreateNews()
    {
        $this->loginAdmin("Customer Info", "News");
        $this->changeAdminListLanguage("Deutsch");

        $this->clickAndWait("link=Title");
        $this->openListItem("link=1 [DE] Test news šÄßüл");
        $this->waitForElementText("1 [DE] Test news šÄßüл", "editval[oxnews__oxshortdesc]");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));

        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->waitForElementText("[last] [EN] Test news šÄßüл", "editval[oxnews__oxshortdesc]");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));

        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxnews__oxactive]"));
        $this->check("editval[oxnews__oxactive]");
        $this->type("editval[oxnews__oxactivefrom]", "2008-01-01");
        $this->type("editval[oxnews__oxactiveto]", "2009-01-01");
        $this->type("editval[oxnews__oxdate]", "2008-02-03");
        $this->type("editval[oxnews__oxshortdesc]", "create_delete news [EN]_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("on", $this->getValue("editval[oxnews__oxactive]"));
        $this->assertEquals("2008-01-01 00:00:00", $this->getValue("editval[oxnews__oxactivefrom]"));
        $this->assertEquals("2009-01-01 00:00:00", $this->getValue("editval[oxnews__oxactiveto]"));
        $this->assertEquals("2008-02-03", $this->getValue("editval[oxnews__oxdate]"));
        $this->assertEquals("create_delete news [EN]_šÄßüл", $this->getValue("editval[oxnews__oxshortdesc]"));
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxnews__oxshortdesc]", "create_delete news [DE]");
        $this->type("editval[oxnews__oxdate]", "2008-04-22");
        $this->type("editval[oxnews__oxactiveto]", "2008-05-01 00:00:00");
        $this->type("editval[oxnews__oxactivefrom]", "2008-01-01 00:11:11");
        $this->uncheck("editval[oxnews__oxactive]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("off", $this->getValue("editval[oxnews__oxactive]"));
        $this->assertEquals("2008-01-01 00:11:11", $this->getValue("editval[oxnews__oxactivefrom]"));
        $this->assertEquals("2008-05-01 00:00:00", $this->getValue("editval[oxnews__oxactiveto]"));
        $this->assertEquals("2008-04-22", $this->getValue("editval[oxnews__oxdate]"));
        $this->assertEquals("create_delete news [DE]", $this->getValue("editval[oxnews__oxshortdesc]"));

        $this->selectAndWait("test_editlanguage", "label=English");
        $this->waitForElementText("create_delete news [EN]_šÄßüл", "editval[oxnews__oxshortdesc]");
        $this->assertEquals("on", $this->getValue("editval[oxnews__oxactive]"));
        $this->assertEquals("2008-01-01 00:11:11", $this->getValue("editval[oxnews__oxactivefrom]"));
        $this->assertEquals("2008-05-01 00:00:00", $this->getValue("editval[oxnews__oxactiveto]"));
        $this->assertEquals("2008-04-22", $this->getValue("editval[oxnews__oxdate]"));

        $this->openTab("Text");
        $this->assertEquals("English", $this->getSelectedLabel("newslang"));
        $this->assertEquals("", $this->getEditorValue("oxnews__oxlongdesc"));
        $this->typeToEditor("oxnews__oxlongdesc", "news desc [EN]_šÄßüл");
        $this->clickAndWait("save");
        $this->assertEquals("news desc [EN]_šÄßüл", $this->getEditorValue("oxnews__oxlongdesc"));
        $this->changeAdminEditLanguage("Deutsch", "newslang");
        $this->assertEquals("", $this->getEditorValue("oxnews__oxlongdesc"));
        $this->typeToEditor("oxnews__oxlongdesc", "news desc [DE]");
        $this->clickAndWait("save");
        $this->assertEquals("news desc [DE]", $this->getEditorValue("oxnews__oxlongdesc"));
        $this->changeAdminEditLanguage("English", "newslang");
        $this->assertEquals("news desc [EN]_šÄßüл", $this->getEditorValue("oxnews__oxlongdesc"));
        $this->changeAdminEditLanguage("Deutsch", "newslang");
        $this->assertEquals("news desc [DE]", $this->getEditorValue("oxnews__oxlongdesc"));

        $this->checkTabs(array('Mall'));
        $this->frame("list");
        $this->type("where[oxnews][oxshortdesc]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete news [EN]_šÄßüл", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating Newsletter
     *
     * @group creatingitems
     */
    public function testCreateNewsletter()
    {
        $this->loginAdmin("Customer Info", "Newsletter");
        $this->type("where[oxnewsletter][oxtitle]", "title");
        $this->clickAndWait("submitit");
        $this->assertElementNotPresent("link=title");
        $this->frame("edit");
        $this->clickCreateNewItem();
        $this->assertEquals("", $this->getValue("editval[oxnewsletter__oxtitle]"));
        $this->assertEquals("", $this->getEditorValue("oxnewsletter__oxtemplate"));
        $this->type("editval[oxnewsletter__oxtitle]", "title_šÄßüл");
        $this->typeToEditor("oxnewsletter__oxtemplate", "sample text_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("title_šÄßüл", $this->getValue("editval[oxnewsletter__oxtitle]"));
        $this->assertEquals("sample text_šÄßüл", $this->getEditorValue("oxnewsletter__oxtemplate"));
        $this->frame("list");
        $this->assertElementPresent("link=title_šÄßüл");
    }

    /**
     * creating Links
     *
     * @group creatingitems
     */
    public function testCreateLinks()
    {
        $this->loginAdmin("Customer Info", "Links");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=URL");
        $this->openListItem("link=http://www.1google.com");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->frame("list");
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxlinks__oxactive]"));
        $this->check("editval[oxlinks__oxactive]");
        $this->type("editval[oxlinks__oxinsert]", "2008-01-02");
        $this->type("editval[oxlinks__oxurl]", "http://www.create_delete.com");
        $this->assertEquals("", $this->getEditorValue("oxlinks__oxurldesc"));
        $this->typeToEditor("oxlinks__oxurldesc", "link desc [EN]_šÄßüл");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("on", $this->getValue("editval[oxlinks__oxactive]"));
        $this->assertEquals("2008-01-02 00:00:00", $this->getValue("editval[oxlinks__oxinsert]"));
        $this->assertEquals("http://www.create_delete.com", $this->getValue("editval[oxlinks__oxurl]"));
        $this->assertEquals("link desc [EN]_šÄßüл", $this->getEditorValue("oxlinks__oxurldesc"));
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        //$this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("editval[oxlinks__oxactive]"));
        $this->assertEquals("2008-01-02 00:00:00", $this->getValue("editval[oxlinks__oxinsert]"));
        $this->assertEquals("http://www.create_delete.com", $this->getValue("editval[oxlinks__oxurl]"));
        $this->uncheck("editval[oxlinks__oxactive]");
        $this->type("editval[oxlinks__oxinsert]", "2008-01-02 00:10:00");
        $this->type("editval[oxlinks__oxurl]", "http://www.create_delete1.com");
        $this->assertEquals("link desc [EN]_šÄßüл", $this->getEditorValue("oxlinks__oxurldesc"));
        $this->typeToEditor("oxlinks__oxurldesc", "link desc [DE]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("link desc [DE]", $this->getEditorValue("oxlinks__oxurldesc"));
        $this->selectAndWait("test_editlanguage", "label=English", "editval[oxlinks__oxactive]");
        $this->assertEquals("link desc [EN]_šÄßüл", $this->getEditorValue("oxlinks__oxurldesc"));
        $this->assertEquals("off", $this->getValue("editval[oxlinks__oxactive]"));
        $this->assertEquals("2008-01-02 00:10:00", $this->getValue("editval[oxlinks__oxinsert]"));
        $this->assertEquals("http://www.create_delete1.com", $this->getValue("editval[oxlinks__oxurl]"));

        $this->checkTabs(array('Mall'));
        $this->frame("list");
        $this->type("where[oxlinks][oxurl]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("http://www.create_delete1.com", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating CMS pages
     *
     * @group creatingitems
     */
    public function testCreateCms()
    {
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Ident");
        $this->clickAndWait("nav.last");
        $this->openListItem("link=[last]testcontent");
        $this->assertEquals("1 [DE] content šÄßüл", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->changeAdminListLanguage('English');
        $this->frame("edit");
        $this->assertEquals("[last] [EN] content šÄßüл", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->clickCreateNewItem();
        $this->assertEquals("off", $this->getValue("editval[oxcontents__oxactive]"));
        $this->check("editval[oxcontents__oxactive]");
        $this->type("editval[oxcontents__oxtitle]", "create_delete cms [EN]_šÄßüл");
        $this->assertNotEquals("", $this->getValue("editval[oxcontents__oxloadid]"));
        $this->type("editval[oxcontents__oxloadid]", "create_delete_ident");
        $this->assertEquals("", $this->getValue("editval[oxcontents__oxtype]"));
        $this->check("oxtype0");
        $this->assertEquals("None", $this->getSelectedLabel("editval[oxcontents__oxfolder]"));
        $this->assertEquals("", $this->getEditorValue("oxcontents__oxcontent"));
        $this->typeToEditor("oxcontents__oxcontent", "cms page [EN] šÄßüл");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->assertEquals("English", $this->getText("test_editlanguage"));
        $this->assertEquals("Deutsch", $this->getText("new_lang"));
        $this->assertEquals("Copy to", $this->getValue("save"));
        $this->assertEquals("on", $this->getValue("editval[oxcontents__oxactive]"));
        $this->assertEquals("create_delete cms [EN]_šÄßüл", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("create_delete_ident", $this->getValue("editval[oxcontents__oxloadid]"));
        $this->assertEquals("0", $this->getValue("editval[oxcontents__oxtype]"));
        $this->clickAndWaitFrame("save", "list");
        $this->type("editval[oxcontents__oxtitle]", "create_delete cms [DE]");
        $this->assertEquals("cms page [EN] šÄßüл", $this->getEditorValue("oxcontents__oxcontent"));
        $this->typeToEditor("oxcontents__oxcontent", "cms page [DE]");
        $this->check("oxtype1");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->assertEquals("on", $this->getValue("editval[oxcontents__oxactive]"));
        $this->assertEquals("create_delete cms [DE]", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("create_delete_ident", $this->getValue("editval[oxcontents__oxloadid]"));
        $this->assertEquals("1", $this->getValue("editval[oxcontents__oxtype]"));
        $this->assertEquals("cms page [DE]", $this->getEditorValue("oxcontents__oxcontent"));
        $this->type("editval[oxcontents__oxloadid]", "create_delete_ident1");
        $this->uncheck("editval[oxcontents__oxactive]");
        $this->check("oxtype2");
        $this->select("editval[oxcontents__oxfolder]", "label=E-mails");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->select("editval[oxcontents__oxcatid]", "label=1 [EN] category šÄßüл");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->assertEquals("1 [EN] category šÄßüл", $this->getSelectedLabel("editval[oxcontents__oxcatid]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("editval[oxcontents__oxactive]"));
        $this->assertEquals("create_delete cms [EN]_šÄßüл", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("create_delete_ident1", $this->getValue("editval[oxcontents__oxloadid]"));
        $this->assertEquals("E-mails", $this->getSelectedLabel("editval[oxcontents__oxfolder]"));
        $this->assertEquals("2", $this->getValue("editval[oxcontents__oxtype]"));
        $this->assertEquals("1 [EN] category šÄßüл", $this->getSelectedLabel("editval[oxcontents__oxcatid]"));
        $this->assertEquals("cms page [EN] šÄßüл", $this->getEditorValue("oxcontents__oxcontent"));
        $this->type("editval[oxcontents__oxtitle]", "create_delete cms1 [EN]");
        $this->type("editval[oxcontents__oxloadid]", "create_delete_ident");
        $this->check("oxtype3");
        $this->uncheck("editval[oxcontents__oxactive]");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->assertEquals("off", $this->getValue("editval[oxcontents__oxactive]"));
        $this->assertEquals("create_delete cms1 [EN]", $this->getValue("editval[oxcontents__oxtitle]"));
        $this->assertEquals("create_delete_ident", $this->getValue("editval[oxcontents__oxloadid]"));
        $this->assertEquals("3", $this->getValue("editval[oxcontents__oxtype]"));
        $this->assertElementPresent("//tr[@id='manuell']/td[2]/input");
        $this->select("editval[oxcontents__oxfolder]", "label=Customer information");
        $this->clickAndWaitFrame("saveContent", "list");
        $this->assertEquals("Customer information", $this->getSelectedLabel("editval[oxcontents__oxfolder]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("Customer information", $this->getSelectedLabel("editval[oxcontents__oxfolder]"));
        $this->assertEquals("off", $this->getValue("editval[oxcontents__oxactive]"));
        $this->assertEquals("cms page [DE]", $this->getEditorValue("oxcontents__oxcontent"));
        //testing if other tabs are working
        $this->openTab("SEO");
        //checking if entered entry can be found
        $this->frame("list");
        $this->type("where[oxcontents][oxloadid]", "create_delete");
        $this->clickAndWait("submitit");
        $this->assertEquals("create_delete_ident", $this->getText("//tr[@id='row.1']/td[3]"));
        $this->assertElementNotPresent("//tr[@id='row.2']/td[3]");
    }

    /**
     * creating promotions (new shopping club functionality)
     *
     * @group creatingitems
     */
    public function testCreatePromotions()
    {
        // deleting azure theme banners
        $aActionsParams = array("OXTITLE" => 'Banner 1');
        $this->callShopSC("oxActions", "delete", "b5639c6431b26687321f6ce654878fa5", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 2');
        $this->callShopSC("oxActions", "delete", "b56a097dedf5db44e20ed56ac6defaa8", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 3');
        $this->callShopSC("oxActions", "delete", "b56efaf6c93664b6dca5b1cee1f87057", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 4');
        $this->callShopSC("oxActions", "delete", "cb34f86f56162d0c95890b5985693710", $aActionsParams);

        $this->executeSql("UPDATE `oxactions` SET `OXACTIVE` = '1';");
        $this->executeSql("DELETE FROM `oxactions` WHERE `OXID` = 'oxnewsletter';");
        $this->loginAdmin("Customer Info", "Promotions");
        $this->assertElementNotPresent("nav.page.2");
        $this->frame("edit");
        $this->clickCreateNewItem();
        $this->type("editval[oxactions__oxtitle]", "create_delete promotion");
        $this->check("editval[oxactions__oxactive]");
        $this->type("editval[oxactions__oxactivefrom]", "2010-01-01");
        $this->type("editval[oxactions__oxactiveto]", "2010-12-12");
        $this->select("editval[oxactions__oxtype]", "label=Promotion");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete promotion", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("on", $this->getValue("editval[oxactions__oxactive]"));
        $this->assertEquals("2010-01-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-12 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxactions__oxtitle]", "create_delete promotion1");
        $this->type("editval[oxactions__oxactivefrom]", "2010-02-01 00:00:00");
        $this->type("editval[oxactions__oxactiveto]", "2010-12-31 00:00:00");
        $this->uncheck("editval[oxactions__oxactive]");
        $this->clickAndWaitFrame("save", "list");
        $this->assertEquals("create_delete promotion1", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("2010-02-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-31 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("editval[oxactions__oxactive]"));
        $this->selectAndWaitFrame("test_editlanguage", "label=English");
        $this->assertEquals("create_delete promotion", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("2010-02-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-31 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->assertEquals("off", $this->getValue("editval[oxactions__oxactive]"));
        $this->frame("list");
        $this->clickAndWaitFrame("link=A");
        if ($this->getTestConfig()->isSubShop()) {
            $this->assertElementNotPresent("nav.page.2");
        }
        $this->clickAndWaitFrame("link=A");
        $newListItemNumber = 10;
        $listItemWeeksSpecialItemNumber = 9;
        $this->assertEquals("create_delete promotion", $this->getText("//tr[@id='row.$newListItemNumber']/td[2]"));
        $this->clickDeleteListItem($newListItemNumber);
        $this->assertElementNotPresent("nav.page.2");
        $this->clickAndWaitFrame("link=Name");
        $this->assertEquals("Week's Special", $this->getText("//tr[@id='row.$listItemWeeksSpecialItemNumber']/td[2]"));
    }

    /**
     * creating Actions. it is possible now to create actions and delete them afterwards
     *
     * @group creatingitems
     */
    public function testCreatePromotionsAction()
    {
        // deleting azure theme banners
        $aActionsParams = array("OXTITLE" => 'Banner 1');
        $this->callShopSC("oxActions", "delete", "b5639c6431b26687321f6ce654878fa5", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 2');
        $this->callShopSC("oxActions", "delete", "b56a097dedf5db44e20ed56ac6defaa8", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 3');
        $this->callShopSC("oxActions", "delete", "b56efaf6c93664b6dca5b1cee1f87057", $aActionsParams);

        $aActionsParams = array("OXTITLE" => 'Banner 4');
        $this->callShopSC("oxActions", "delete", "cb34f86f56162d0c95890b5985693710", $aActionsParams);

        $this->executeSql("UPDATE `oxactions` SET `OXACTIVE` = '1';");
        $this->executeSql("DELETE FROM `oxactions` WHERE `OXID` = 'oxnewsletter';");
        $this->loginAdmin("Customer Info", "Promotions");
        $this->assertElementNotPresent("nav.page.2");
        $this->frame("edit");
        $this->clickCreateNewItem();

        $this->type("editval[oxactions__oxtitle]", "create_delete action");
        $this->check("editval[oxactions__oxactive]");
        $this->type("editval[oxactions__oxactivefrom]", "2010-01-01");
        $this->type("editval[oxactions__oxactiveto]", "2010-12-12");
        $this->select("editval[oxactions__oxtype]", "label=Action");
        $this->clickAndWaitFrame("save", 'edit');

        $this->assertEquals("create_delete action", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("on", $this->getValue("editval[oxactions__oxactive]"));
        $this->assertEquals("2010-01-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-12 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->clickAndWaitFrame("save", 'list');

        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxactions__oxtitle]", "create_delete action1");
        $this->type("editval[oxactions__oxactivefrom]", "2010-02-01 00:00:00");
        $this->type("editval[oxactions__oxactiveto]", "2010-12-31 00:00:00");
        $this->uncheck("editval[oxactions__oxactive]");
        $this->clickAndWaitFrame("save", 'list');

        $this->assertEquals("create_delete action1", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("2010-02-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-31 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("editval[oxactions__oxactive]"));
        $this->changeAdminEditLanguage("English", "test_editlanguage");
        $this->assertEquals("create_delete action", $this->getValue("editval[oxactions__oxtitle]"));
        $this->assertEquals("2010-02-01 00:00:00", $this->getValue("editval[oxactions__oxactivefrom]"));
        $this->assertEquals("2010-12-31 00:00:00", $this->getValue("editval[oxactions__oxactiveto]"));
        $this->assertEquals("off", $this->getValue("editval[oxactions__oxactive]"));
        $this->changeListSorting("link=A");
        if ($this->getTestConfig()->isSubShop()) {
            $this->assertElementNotPresent("nav.page.2");
        }
        $this->changeListSorting("link=A");
        $newListItemNumber = 10;
        $listItemWeeksSpecialItemNumber = 9;
        $this->assertEquals("create_delete action", $this->getText("//tr[@id='row.$newListItemNumber']/td[2]"));
        $this->clickDeleteListItem($newListItemNumber);
        $this->assertElementNotPresent("nav.page.2");
        $this->clickAndWait("link=Name");
        $this->assertEquals("Week's Special", $this->getText("//tr[@id='row.$listItemWeeksSpecialItemNumber']/td[2]"));
    }

    /**
     * @param array $tabsToTest
     */
    protected function checkTabs($tabsToTest)
    {
        $this->frame('list');
        foreach ($tabsToTest as $tab) {
            if ($this->isElementPresent("//div[@class='tabs']//a[text()='$tab']")) {
                $this->openTab($tab);
            }
        }
    }
}
