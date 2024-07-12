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
class ActionsMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Actions_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", -1);

        // testing..
        $oView = oxNew('Actions_Main');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertSame('-1', $aViewData["oxid"]);
        $this->assertSame("actions_main", $sTplName);
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testRenderWithExistingAction()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxactions"));

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["createCategoryTree"]);
        $oView->method('createCategoryTree')->willReturn(false);
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertSame("actions_main", $sTplName);
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testRenderForCategory()
    {
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxcategories"));
        $this->setRequestParameter("aoc", 1);

        // testing..
        $oView = oxNew('Actions_Main');
        $sTplName = $oView->render();

        $this->assertSame("popups/actions_main", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertNotNull($aViewData["artcattree"]);
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testRenderForArticlePromotions()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions WHERE oxid = 'd51545e80843be666a9326783a73e91d'");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", 'article');

        $oArticle = oxNew('oxarticle');
        $oArticle->oxarticles__oxartnum = new oxField("testArtNr");
        $oArticle->oxarticles__oxtitle = new oxField("testArtTitle");

        $oPromotion = $this->getMock(\OxidEsales\Eshop\Application\Model\Actions::class, ["getBannerArticle"]);
        $oPromotion->expects($this->once())->method('getBannerArticle')->willReturn($oArticle);
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["getViewDataElement", "createCategoryTree"]);
        $oView->expects($this->once())->method('getViewDataElement')->willReturn($oPromotion);
        $oView->expects($this->once())->method('createCategoryTree');
        $sTplName = $oView->render();


        $this->assertSame("popups/actions_article", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertSame("testArtNr", $aViewData["actionarticle_artnum"]);
        $this->assertSame("testArtTitle", $aViewData["actionarticle_title"]);
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testRenderForGroupPromotions()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions WHERE oxid = 'd51545e80843be666a9326783a73e91d'");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", 'groups');

        $oPromotion = oxNew('oxActions');
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["getViewDataElement", "createCategoryTree"]);
        $oView->expects($this->once())->method('getViewDataElement')->willReturn($oPromotion);
        $oView->expects($this->never())->method('createCategoryTree');
        $sTplName = $oView->render();


        $this->assertSame("popups/actions_groups", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNotNull($aViewData["oxajax"]);
        $this->assertNull($aViewData["actionarticle_artnum"]);
        $this->assertNull($aViewData["actionarticle_title"]);
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testRenderForPromotionsEditor()
    {
        $sPromotion = oxDb::getDb()->getOne("select oxid from oxactions where oxtype=2");
        $this->setRequestParameter("oxid", $sPromotion);
        $this->setRequestParameter("oxpromotionaoc", null);

        $oPromotion = oxNew('oxActions');
        $oPromotion->load($sPromotion);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["getViewDataElement", "generateTextEditor"]);
        $oView->expects($this->once())->method('getViewDataElement')->willReturn($oPromotion);
        $oView->expects($this->once())->method('generateTextEditor')->willReturn("sHtmlEditor");
        $sTplName = $oView->render();

        $this->assertSame("actions_main", $sTplName);
        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertNotNull($aViewData["edit"]);
        $this->assertNull($aViewData["oxajax"]);
        $this->assertSame("sHtmlEditor", $aViewData["editor"]);
    }

    /**
     * Actions_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');

        $this->setRequestParameter("oxid", "xxx");
        $this->setRequestParameter("editval", ["xxx"]);
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey("updatelist", $aViewData);
        $this->assertSame(1, $aViewData["updatelist"]);
    }

    /**
     * Actions_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["save"]);
        $oView->expects($this->once())->method("save");
        $oView->saveinnlang();
    }

    /**
     * Actions_Main::Render() test case
     */
    public function testPromotionsRender()
    {
        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("saved_oxid", -1);

        $oPromotion = oxNew('oxActions');
        $oPromotion->oxactions__oxtype = new oxField(2);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ActionsMain::class, ["getViewDataElement", "generateTextEditor"]);
        $oView->expects($this->once())->method('getViewDataElement')->with("edit")->willReturn($oPromotion);
        $oView->expects($this->once())->method('generateTextEditor')->with("100%", 300, $oPromotion, "oxactions__oxlongdesc", "details.css");

        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertSame('-1', $aViewData["oxid"]);
        $this->assertSame("actions_main", $sTplName);
    }

    /**
     * Actions_Main::Save() test case
     */
    public function testPromotionsSave()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');

        $this->setRequestParameter("oxid", "xxx");
        $this->setRequestParameter("editval", ["xxx"]);
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey("updatelist", $aViewData);
        $this->assertSame(1, $aViewData["updatelist"]);
        $this->assertNull(oxRegistry::getSession()->getVariable("saved_oxid"));
    }

    /**
     * Actions_Main::Save() test case
     */
    public function testSaveInsertingNewPromo()
    {
        oxTestModules::addFunction('oxActions', 'load', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'save', '{ return true; }');
        oxTestModules::addFunction('oxActions', 'getId', '{ return "testId"; }');

        $this->setRequestParameter("oxid", "-1");
        $this->setRequestParameter("editval", ["xxx"]);
        $this->setConfigParam("blAllowSharedEdit", true);

        $oView = oxNew('Actions_Main');
        $oView->save();

        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey("updatelist", $aViewData);
        $this->assertSame(1, $aViewData["updatelist"]);
    }
}
