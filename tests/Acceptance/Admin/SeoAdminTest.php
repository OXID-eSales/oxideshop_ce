<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** SEO functionality */
class SeoAdminTest extends AdminTestCase
{
    /**
     * Seo: Core settings -> SEO
     *
     * @group seo
     */
    public function testSeoCoreSettings()
    {
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("Main");
        $this->assertEquals("English", $this->getSelectedLabel("subjlang"));
        $this->assertEquals("Your order at OXID eShop", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->clickAndWaitFrame("oLockButton", 'list');
        $this->selectAndWait("subjlang", "label=Deutsch", "editval[oxshops__oxordersubject]");
        $this->assertEquals("Ihre Bestellung bei OXID eSales", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->clickAndWaitFrame("oLockButton", 'list');
        $this->openTab("SEO");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("OXID Surf and Kite Shop", $this->getValue("editval[oxshops__oxtitleprefix]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("OXID Surf- und Kiteshop", $this->getValue("editval[oxshops__oxtitleprefix]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->type("editval[oxshops__oxtitleprefix]", "prefix DE šÄßüл");
        $this->type("editval[oxshops__oxtitlesuffix]", "suffix DE šÄßüл");
        $this->type("editval[oxshops__oxstarttitle]", "title DE šÄßüл");
        $this->clickAndWait("//input[@name='save' and @value='Save']");
        $this->assertEquals("prefix DE šÄßüл", $this->getValue("editval[oxshops__oxtitleprefix]"), "After information was edited and saved, old values before editing are displayed");
        $this->assertEquals("suffix DE šÄßüл", $this->getValue("editval[oxshops__oxtitlesuffix]"), "After information was edited and saved, old values before editing are displayed");
        $this->assertEquals("title DE šÄßüл", $this->getValue("editval[oxshops__oxstarttitle]"), "After information was edited and saved, old values before editing are displayed");
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->type("editval[oxshops__oxtitleprefix]", "prefix EN");
        $this->type("editval[oxshops__oxtitlesuffix]", "suffix EN");
        $this->type("editval[oxshops__oxstarttitle]", "title EN");
        $this->clickAndWait("//input[@name='save' and @value='Save']");
        $this->assertEquals("prefix EN", $this->getValue("editval[oxshops__oxtitleprefix]"));
        $this->assertEquals("suffix EN", $this->getValue("editval[oxshops__oxtitlesuffix]"));
        $this->assertEquals("title EN", $this->getValue("editval[oxshops__oxstarttitle]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("prefix DE šÄßüл", $this->getValue("editval[oxshops__oxtitleprefix]"));
        $this->assertEquals("suffix DE šÄßüл", $this->getValue("editval[oxshops__oxtitlesuffix]"));
        $this->assertEquals("title DE šÄßüл", $this->getValue("editval[oxshops__oxstarttitle]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("prefix EN", $this->getValue("editval[oxshops__oxtitleprefix]"));
        $this->assertEquals("suffix EN", $this->getValue("editval[oxshops__oxtitlesuffix]"));
        $this->assertEquals("title EN", $this->getValue("editval[oxshops__oxstarttitle]"));
        $this->type("confstrs[sSEOSeparator]", "+");
        $this->type("confstrs[sSEOuprefix]", "pre");
        $this->clickAndWait("//input[@name='save' and @value='Save']");
        $this->assertEquals("+", $this->getValue("confstrs[sSEOSeparator]"));
        $this->assertEquals("pre", $this->getValue("confstrs[sSEOuprefix]"));
        $this->type("confstrs[sSEOSeparator]", "");
        $this->type("confstrs[sSEOuprefix]", "");
        $this->clickAndWait("//input[@name='save' and @value='Save']");
        $this->assertEquals("", $this->getValue("confstrs[sSEOSeparator]"));
        $this->assertEquals("", $this->getValue("confstrs[sSEOuprefix]"));
        $this->openTab("Main");
        $this->assertEquals("English", $this->getSelectedLabel("subjlang"));
        $this->assertEquals("Your order at OXID eShop", $this->getValue("editval[oxshops__oxordersubject]"));
        $this->selectAndWait("subjlang", "label=Deutsch");
        $this->assertEquals("Ihre Bestellung bei OXID eSales", $this->getValue("editval[oxshops__oxordersubject]"));
        //resetting seo ID's'
        $this->openTab("SEO");
        $this->clickAndConfirm("//input[@name='save' and @value='Update SEO URLs']");
    }

    /**
     * seo: Distributors -> SEO
     *
     * @group seo
     */
    public function testSeoDistributors()
    {
        $this->loginAdmin("Master Settings", "Distributors");
        $this->changeAdminListLanguage("Deutsch");
        $this->clickAndWait("link=Title");
        $this->clickAndWaitFrame("link=1 DE distributor šÄßüл", "edit");
        $this->openTab("SEO");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Lieferant/1-DE-distributor-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->uncheck("blShowSuffix");
        $this->type("aSeoData[oxseourl]", "Nach-Lieferant/1-DE-distributor/DE/");
        $this->type("aSeoData[oxkeywords]", "keywords DE");
        $this->type("aSeoData[oxdescription]", "description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Lieferant/1-DE-distributor/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("en/By-distributor/last-EN-distributor-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("blShowSuffix");
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "By-Distributor/last-EN-distributor/EN/");
        $this->type("aSeoData[oxkeywords]", "keywords EN");
        $this->type("aSeoData[oxdescription]", "description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("By-Distributor/last-EN-distributor/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Lieferant/1-DE-distributor/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("By-Distributor/last-EN-distributor/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
    }

    /**
     * seo: Manufacturers -> SEO
     *
     * @group seo
     */
    public function testSeoManufacturers()
    {
        $this->loginAdmin("Master Settings", "Brands/Manufacturers");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->clickAndWaitFrame("link=1 DE manufacturer šÄßüл", "edit");
        $this->openTab("SEO");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Hersteller/1-DE-manufacturer-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->uncheck("blShowSuffix");
        $this->type("aSeoData[oxseourl]", "Nach-Hersteller/Hersteller/1-DE-manufacturer/DE/");
        $this->type("aSeoData[oxkeywords]", "keywords DE");
        $this->type("aSeoData[oxdescription]", "description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Hersteller/Hersteller/1-DE-manufacturer/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("en/By-manufacturer/last-EN-manufacturer-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("blShowSuffix");
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "By-Manufacturer/Manufacturer/last-EN-manufacturer/EN/");
        $this->type("aSeoData[oxkeywords]", "keywords EN");
        $this->type("aSeoData[oxdescription]", "description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("By-Manufacturer/Manufacturer/last-EN-manufacturer/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("Nach-Hersteller/Hersteller/1-DE-manufacturer/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("By-Manufacturer/Manufacturer/last-EN-manufacturer/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
    }

    /**
     * seo: Products -> SEO
     *
     * @group seo
     */
    public function testSeoProducts()
    {
        $this->loginAdmin("Administer Products", "Products");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Prod.No.");
        $this->clickAndWaitFrame("link=[DE 4] Test product 0 šÄßüл", "edit");
        $this->openTab("SEO");
        //seo checking when category is selected
        $this->assertEquals("Test category 0 [DE] šÄßüл (main category)", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Test-category-0-DE-Aessue/DE-4-Test-product-0-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "Test-category-0-DE/DE-4-Test-product-0-de.html");
        $this->type("aSeoData[oxkeywords]", "keywords DE");
        $this->type("aSeoData[oxdescription]", "description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Test-category-0-DE/DE-4-Test-product-0-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Test category 0 [EN] šÄßüл (main category)", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("en/Test-category-0-EN-Aessue/Test-product-0-EN-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "Test-category-0-EN/Test-product-0-EN-en.html");
        $this->type("aSeoData[oxkeywords]", "keywords EN");
        $this->type("aSeoData[oxdescription]", "description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Test-category-0-EN/Test-product-0-EN-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("Test category 0 [DE] šÄßüл (main category)", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Test-category-0-DE/DE-4-Test-product-0-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Test category 0 [EN] šÄßüл (main category)", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Test-category-0-EN/Test-product-0-EN-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        //checking when distributor is selected
        $this->selectAndWait("aSeoData[oxparams]", "label=Distributor [DE] šÄßüл");
        $this->assertEquals("Distributor [DE] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Nach-Lieferant/Distributor-DE-Aessue/DE-4-Test-product-0-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "distributor-de.html");
        $this->type("aSeoData[oxkeywords]", "distributor keywords DE");
        $this->type("aSeoData[oxdescription]", "distributor description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("distributor-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("en/By-distributor/Distributor-EN-Aessue/Test-product-0-EN-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "distributor-en.html");
        $this->type("aSeoData[oxkeywords]", "distributor keywords EN");
        $this->type("aSeoData[oxdescription]", "distributor description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("distributor-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("Distributor [DE] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("distributor-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Distributor [EN] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("distributor-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        //checking manufacturers
        $this->selectAndWait("aSeoData[oxparams]", "label=Manufacturer [DE] šÄßüл");
        $this->assertEquals("Manufacturer [DE] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("Nach-Hersteller/Manufacturer-DE-Aessue/DE-4-Test-product-0-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "manufacturer-de.html");
        $this->type("aSeoData[oxkeywords]", "manufacturer keywords DE");
        $this->type("aSeoData[oxdescription]", "manufacturer description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("manufacturer-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("manufacturer keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("manufacturer description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("en/By-manufacturer/Manufacturer-EN-Aessue/Test-product-0-EN-Aessue.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("distributor keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("distributor description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "manufacturer-en.html");
        $this->type("aSeoData[oxkeywords]", "manufacturer keywords EN");
        $this->type("aSeoData[oxdescription]", "manufacturer description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("manufacturer-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("manufacturer keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("manufacturer description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("Manufacturer [DE] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("manufacturer-de.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("manufacturer keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("manufacturer description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("Manufacturer [EN] šÄßüл", $this->getSelectedLabel("aSeoData[oxparams]"));
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("manufacturer-en.html", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("manufacturer keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("manufacturer description EN", $this->getValue("aSeoData[oxdescription]"));
    }

    /**
     * seo: Categories -> SEO
     *
     * @group seo
     */
    public function testSeoCategories()
    {
        $this->loginAdmin("Administer Products", "Categories");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->clickAndWaitFrame("link=1 [DE] category šÄßüл", "edit");
        $this->openTab("SEO");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("1-DE-category-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->uncheck("blShowSuffix");
        $this->type("aSeoData[oxseourl]", "1-DE-category/DE/");
        $this->type("aSeoData[oxkeywords]", "keywords DE");
        $this->type("aSeoData[oxdescription]", "description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("1-DE-category/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("off", $this->getValue("blShowSuffix"));
        $this->assertEquals("en/last-EN-category-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("blShowSuffix");
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "last-EN-category/EN/");
        $this->type("aSeoData[oxkeywords]", "keywords EN");
        $this->type("aSeoData[oxdescription]", "description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("last-EN-category/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("1-DE-category/DE/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("on", $this->getValue("blShowSuffix"));
        $this->assertEquals("last-EN-category/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        //resetting seo ids and checking if statick ones are not resetted
        $this->selectMenu("Master Settings", "Core Settings");
        $this->openTab("SEO");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("confstrs[iDefSeoLang]"));
        $this->select("confstrs[iDefSeoLang]", "label=English");
        $this->clickAndWait("//input[@name='save' and @value='Save']");
        $this->assertEquals("English", $this->getSelectedLabel("confstrs[iDefSeoLang]"));
        //resetting seo ID's'
        $this->clickAndConfirm("//input[@name='save' and @value='Update SEO URLs']");
        $this->selectMenu("Administer Products", "Categories");
        $this->assertEquals("Deutsch", $this->getSelectedLabel("changelang"));
        $this->clickAndWait("link=Title");
        $this->clickAndWaitFrame("link=1 [DE] category šÄßüл", "edit");
        $this->openTab("SEO");
        $this->assertEquals("English", $this->getSelectedLabel("test_editlanguage"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("de/1-DE-category-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("last-EN-category/EN/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
    }

    /**
     * seo: CMS pages -> SEO
     *
     * @group seo
     */
    public function testSeoCmsPages()
    {
        $this->loginAdmin("Customer Info", "CMS Pages");
        $this->assertEquals("English", $this->getSelectedLabel("changelang"));
        $this->changeAdminListLanguage('Deutsch');
        $this->clickAndWait("link=Title");
        $this->clickAndWaitFrame("link=1 [DE] content šÄßüл", "edit");
        $this->openTab("SEO");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("Deutsch", $this->getSelectedLabel("test_editlanguage"));
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("1-DE-content-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "1-DE-content-de/");
        $this->type("aSeoData[oxkeywords]", "keywords DE");
        $this->type("aSeoData[oxdescription]", "description DE");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("1-DE-content-de/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("en/last-EN-content-Aessue/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("", $this->getValue("aSeoData[oxdescription]"));
        $this->check("aSeoData[oxfixed]");
        $this->type("aSeoData[oxseourl]", "last-EN-content-en/");
        $this->type("aSeoData[oxkeywords]", "keywords EN");
        $this->type("aSeoData[oxdescription]", "description EN");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("last-EN-content-en/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
        $this->selectAndWait("test_editlanguage", "label=Deutsch");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("1-DE-content-de/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords DE", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description DE", $this->getValue("aSeoData[oxdescription]"));
        $this->uncheck("aSeoData[oxfixed]");
        $this->clickAndWait("saveArticle");
        $this->assertEquals("off", $this->getValue("aSeoData[oxfixed]"));
        $this->selectAndWait("test_editlanguage", "label=English");
        $this->assertEquals("on", $this->getValue("aSeoData[oxfixed]"));
        $this->assertEquals("last-EN-content-en/", $this->getValue("aSeoData[oxseourl]"));
        $this->assertEquals("keywords EN", $this->getValue("aSeoData[oxkeywords]"));
        $this->assertEquals("description EN", $this->getValue("aSeoData[oxdescription]"));
    }
}
