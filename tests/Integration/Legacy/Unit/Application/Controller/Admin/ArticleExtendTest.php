<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\CategoryList;
use \oxDb;
use OxidEsales\EshopCommunity\Core\Model\ListModel;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Article_Extend class
 */
class ArticleExtendTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxmediaurls");
        parent::tearDown();
    }

    /**
     * Article_Extend::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $this->setRequestParameter("oxid", oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid !='' "));

        // testing..
        $oView = oxNew('Article_Extend');
        $sTplName = $oView->render();

        // testing view data
        $aViewData = $oView->getViewData();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Article::class, $aViewData["edit"]);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\CategoryList::class, $aViewData["artcattree"]);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Model\ListModel::class, $aViewData["aMediaUrls"]);

        $this->assertSame('article_extend', $sTplName);
    }

    /**
     * Article_Extend::Save() test case
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');
        $this->setRequestParameter("editval", ["oxarticles__oxtprice" => -1]);

        // testing..
        try {
            $oView = oxNew('Article_Extend');
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("save", $exception->getMessage(), "error in Article_Extend::save()");

            return;
        }

        $this->fail("error in Article_Extend::save()");
    }

    /**
     * Article_Extend::Save() test case
     */
    public function testSaveMissingMediaDescription()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{return "EXCEPTION_NODESCRIPTIONADDED";}');
        $this->setRequestParameter("mediaUrl", "testUrl");
        $this->setRequestParameter("mediaDesc", null);

        // testing..
        $oView = oxNew('Article_Extend');
        $this->assertSame("EXCEPTION_NODESCRIPTIONADDED", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     */
    public function testSaveMissingMediaUrlAndFile()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{return "EXCEPTION_NOMEDIAADDED";}');
        oxTestModules::addFunction('oxConfig', 'getUploadedFile', '{return array( "name" => false );}');

        $this->setRequestParameter("mediaUrl", null);
        $this->setRequestParameter("mediaDesc", "testDesc");

        // testing..
        $oView = oxNew('Article_Extend');
        $this->assertSame("EXCEPTION_NOMEDIAADDED", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     */
    public function testSaveUnableToMoveUploadedFile()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return $aA[0]; }');
        oxTestModules::addFunction('oxUtilsFile', 'processFile', '{ throw new Exception("handleUploadedFile"); }');

        $this->setRequestParameter("mediaUrl", "testUrl");
        $this->setRequestParameter("mediaDesc", "testDesc");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "isDemoShop"]);
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->willReturn(["name" => "testName"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(false);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtend::class, ["getConfig", "resetContentCache"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->method('resetContentCache');
        $this->assertSame("handleUploadedFile", $oView->save());
    }

    /**
     * Article_Extend::Save() test case
     */
    public function testSaveMediaFileUpload()
    {
        // testing..
        oxTestModules::addFunction('oxarticle', 'save', '{}');
        oxTestModules::addFunction('oxmediaurl', 'save', '{ throw new Exception( "oxmediaurl.save" ); }');
        oxTestModules::addFunction('oxUtilsFile', 'processFile', '{}');

        $this->setRequestParameter("mediaUrl", "testUrl");
        $this->setRequestParameter("mediaDesc", "testDesc");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "isDemoShop"]);
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->willReturn(["name" => "testName"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(false);

        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtend::class, ["getConfig", "resetContentCache"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->method('resetContentCache');


        // testing..
        try {
            $oView->save();
        } catch (Exception $exception) {
            $this->assertSame("oxmediaurl.save", $exception->getMessage(), "error in Article_Extend::save()");

            return;
        }

        $this->fail("error in Article_Extend::save()");
    }

    /**
     * Article_Extend::Save() test case when demoShop = true and upload file
     */
    public function testSaveDemoShopFileUpload()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getUploadedFile", "isDemoShop"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);
        $oConfig->expects($this->exactly(2))->method('getUploadedFile')->willReturnOnConsecutiveCalls(["name" => ['FL@oxarticles__oxfile' => "testFile"]], ["name" => "testName"]);
        // testing..
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtend::class, ["getConfig", "resetContentCache"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oView->method('resetContentCache');
        $oView->save();
        // testing..
        $aErr = $this->getSession()->getVariable('Errors');
        $oErr = unserialize($aErr['default'][0]);
        $this->assertSame('ARTICLE_EXTEND_UPLOADISDISABLED', $oErr->getOxMessage());
    }

    /**
     * Article_Extend::DeleteMedia() test case
     */
    public function testDeleteMedia()
    {
        $oMediaUrl = oxNew("oxMediaUrl");
        $oMediaUrl->setId("testMediaId");
        $oMediaUrl->save();

        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxmediaurls where oxid = 'testMediaId'"));

        $this->setRequestParameter("oxid", "testId");
        $this->setRequestParameter("mediaid", "testMediaId");

        // testing..
        $oView = oxNew('Article_Extend');
        $oView->deletemedia();

        $this->assertFalse(oxDb::getDb()->getOne("select 1 from oxmediaurls where oxid = 'testMediaId'"));
    }

    /**
     * Article_Extend::AddDefaultValues() test case
     */
    public function testAddDefaultValues()
    {
        $aParams['oxarticles__oxexturl'] = "http://www.delfi.lt";
        $oView = oxNew('Article_Extend');
        $aParams = $oView->addDefaultValues($aParams);

        $this->assertSame("http://www.delfi.lt", $aParams['oxarticles__oxexturl']);
    }

    /**
     * Article_Extend::UpdateMedia() test case
     */
    public function testUpdateMedia()
    {
        $oMediaUrl = oxNew("oxMediaUrl");
        $oMediaUrl->setId("testMediaId");
        $oMediaUrl->save();

        $aValue = ["testMediaId" => ["oxmediaurls__oxurl" => "testUrl", "oxmediaurls__oxdesc" => "testDesc"]];
        $this->setRequestParameter("aMediaUrls", $aValue);

        $oView = oxNew('Article_Extend');
        $oView->updateMedia();

        $this->assertTrue((bool) oxDb::getDb()->getOne("select 1 from oxmediaurls where oxurl = 'testUrl' and oxdesc='testDesc' "));
    }

    /**
     * Test case for Article_Extend::getUnitsArray()
     */
    public function testGetUnitsArray()
    {
        $aArray = oxRegistry::getLang()->getSimilarByKey("_UNIT_", 0, false);
        $oView = oxNew('Article_Extend');

        $this->assertEquals($aArray, $oView->getUnitsArray());
    }
}
