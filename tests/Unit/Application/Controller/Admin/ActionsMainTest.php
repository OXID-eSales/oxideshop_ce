<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Actions_Main class
 */
class ActionsMainTest extends \OxidTestCase
{

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", -1);

        // testing..
        $oView = oxNew('Actions_Main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertEquals('-1', $aViewData["oxid"]);
        $this->assertEquals("actions_main.tpl", $sTplName);
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRenderWithExistingAction()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxactions"));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("_createCategoryTree"));
        $oView->expects($this->any())->method('_createCategoryTree')->will($this->returnValue(false));
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertEquals("actions_main.tpl", $sTplName);
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRenderForCategory()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxcategories"));
        $this->setRequestParameter("aoc", 1);

        // testing..
        $oView = oxNew('Actions_Main');
        $sTplName = $oView->render();

        $this->assertEquals("popups/actions_main.tpl", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertNotNull($aViewData["artcattree"]);
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRenderForArticlePromotions()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions WHERE oxid = 'd51545e80843be666a9326783a73e91d'");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", 'article');

        $oArticle = oxNew('oxarticle');
        $oArticle->oxarticles__oxartnum = new oxField("testArtNr");
        $oArticle->oxarticles__oxtitle = new oxField("testArtTitle");

        $oPromotion = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, array("getBannerArticle"));
        $oPromotion->expects($this->once())->method('getBannerArticle')->will($this->returnValue($oArticle));
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("getViewDataElement", "_createCategoryTree"));
        $oView->expects($this->once())->method('getViewDataElement')->will($this->returnValue($oPromotion));
        $oView->expects($this->once())->method('_createCategoryTree');
        $sTplName = $oView->render();


        $this->assertEquals("popups/actions_article.tpl", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertEquals("testArtNr", $aViewData["actionarticle_artnum"]);
        $this->assertEquals("testArtTitle", $aViewData["actionarticle_title"]);
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRenderForGroupPromotions()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions WHERE oxid = 'd51545e80843be666a9326783a73e91d'");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", 'groups');

        $oPromotion = oxNew('oxActions');
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("getViewDataElement", "_createCategoryTree"));
        $oView->expects($this->once())->method('getViewDataElement')->will($this->returnValue($oPromotion));
        $oView->expects($this->never())->method('_createCategoryTree');
        $sTplName = $oView->render();


        $this->assertEquals("popups/actions_groups.tpl", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertNull($aViewData["actionarticle_artnum"]);
        $this->assertNull($aViewData["actionarticle_title"]);
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testRenderForPromotionsEditor()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions where oxtype=2");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", null);

        $oPromotion = oxNew('oxActions');
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("getViewDataElement", "_generateTextEditor"));
        $oView->expects($this->once())->method('getViewDataElement')->will($this->returnValue($oPromotion));
        $oView->expects($this->once())->method('_generateTextEditor')->will($this->returnValue("sHtmlEditor"));
        $sTplName = $oView->render();

        $this->assertEquals("actions_main.tpl", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNull($aViewData["oxajax"]);
        $this->assertEquals("sHtmlEditor", $aViewData["editor"]);
    }

    /**
     * Actions_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');

        $this->setRequestParameter("oxid", "xxx");
        $this->setRequestParameter("editval", array("xxx"));
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
    }

    /**
     * Actions_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("save"));
        $oView->expects($this->once())->method("save");
        $oView->saveinnlang();
    }

    /**
     * Actions_Main::Render() test case
     *
     * @return null
     */
    public function testPromotionsRender()
    {
        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("saved_oxid", -1);

        $oPromotion = oxNew('oxActions');
        $oPromotion->oxactions__oxtype = new oxField(2);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, array("getViewDataElement", "_generateTextEditor"));
        $oView->expects($this->once())->method('getViewDataElement')->with($this->equalTo("edit"))->will($this->returnValue($oPromotion));
        $oView->expects($this->once())->method('_generateTextEditor')->with($this->equalTo("100%"), $this->equalTo(300), $this->equalTo($oPromotion), $this->equalTo("oxactions__oxlongdesc"), $this->equalTo("details.tpl.css"));

        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertEquals('-1', $aViewData["oxid"]);
        $this->assertEquals("actions_main.tpl", $sTplName);
    }

    /**
     * Actions_Main::Save() test case
     *
     * @return null
     */
    public function testPromotionsSave()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');

        $this->setRequestParameter("oxid", "xxx");
        $this->setRequestParameter("editval", array("xxx"));
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
        $this->assertNull(oxRegistry::getSession()->getVariable("saved_oxid"));
    }

    /**
     * Actions_Main::Save() test case
     *
     * @return null
     */
    public function testSaveInsertingNewPromo()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'getId', '{ return "testId"; }');

        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", array("xxx"));
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["updatelist"]));
        $this->assertEquals(1, $aViewData["updatelist"]);
    }
}
