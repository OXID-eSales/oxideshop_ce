<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Application\Model\Article;
use OxidEsales\EshopCommunity\Application\Model\CategoryList;
use \oxField;
use \oxDb;
use OxidEsales\EshopCommunity\Application\Model\ManufacturerList;
use \oxTestModules;
use oxRegistry;
use OxidEsales\EshopCommunity\Application\Model\VendorList;

/**
 * Tests for Article_Main class
 */
class ArticleMainTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Setup fixture
     */
    public function setup(): void
    {
        $this->addToDatabase("replace into oxcategories set oxid='_testCategory1', oxshopid='1', oxtitle='_testCategory1'", 'oxcategories');
        $this->addToDatabase("replace into oxarticles set oxid='_testArticle1', oxshopid='" . $this->getShopId() . "', oxtitle='_testArticle1'", 'oxarticles');
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $oArt = oxNew('oxArticle');
        $oArt->delete('_testArtId');

        $oArt = oxNew('oxArticle');
        $oArt->delete('_testArtId2');

        $myDB = oxDb::getDB();
        $myDB->execute('delete from oxobject2selectlist');
        $this->addTableForCleanup('oxobject2category');
        parent::tearDown();
    }

    /**
     * Copying article
     */
    public function testCopyArticleAdditionalTest()
    {
        oxTestModules::addFunction('oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'save', '{ return true; }');
        $this->getConfig()->setConfigParam("blDisableDublArtOnCopy", true);

        $aTasks = ["copyCategories", "copyAttributes", "copySelectlists", "copyCrossseling", "copyAccessoires", "copyStaffelpreis", "copyArtExtends"];

        $aTasks[] = "resetContentCache";

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, $aTasks);
        $oView->expects($this->once())->method('copyCategories');
        $oView->expects($this->once())->method('copyAttributes');
        $oView->expects($this->once())->method('copySelectlists');
        $oView->expects($this->once())->method('copyCrossseling');
        $oView->expects($this->once())->method('copyAccessoires');
        $oView->expects($this->once())->method('copyStaffelpreis');
        $oView->expects($this->once())->method('copyArtExtends');

        $oView->expects($this->once())->method('resetContentCache');

        $oDb = oxDb::getDb();
        $sProdId = $oDb->getOne("select oxid from oxarticles where oxparentid !=''");
        $sParentId = $oDb->getOne(sprintf('select oxparentid from oxarticles where oxid =\'%s\'', $sProdId));

        $oView->copyArticle($sProdId, "_testArtId", $sParentId);
    }

    /**
     * Copying attributes assignments
     */
    public function testCopyCategories()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sO2CView = $tableViewNameGenerator->getViewName('oxobject2category');

        $this->addToDatabase(sprintf('INSERT INTO `%s` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES (\'', $sO2CView) . $oUtils->generateUId() . "', '_testArtId', '_testCatId', '0', '0');", 'oxobject2category');
        $this->addToDatabase(sprintf('INSERT INTO `%s` (`OXID`, `OXOBJECTID`, `OXCATNID`, `OXPOS`, `OXTIME`) VALUES (\'', $sO2CView) . $oUtils->generateUId() . "', '_testArtId', '_testCatId2', '0', '0');", 'oxobject2category');
        $this->addTeardownSql(sprintf('delete from `%s` where OXOBJECTID = \'_testArtId\'', $sO2CView));
        $oView = oxNew('Article_Main');
        $oView->copyCategories("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne(sprintf('select count(*) from %s where oxobjectid = \'_testArtId2\'', $sO2CView)));
    }

    /**
     * Copying attributes assignments
     */
    public function testCopyAttributes()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $this->getConfig()->getShopId();

        // creating few oxprice2article records
        $oDb->execute("INSERT INTO `oxobject2attribute` (OXID,OXOBJECTID,OXATTRID,OXVALUE,OXPOS,OXVALUE_1,OXVALUE_2,OXVALUE_3) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId', '0', '0', '0', '0', '0' );");
        $oDb->execute("INSERT INTO `oxobject2attribute` (OXID,OXOBJECTID,OXATTRID,OXVALUE,OXPOS,OXVALUE_1,OXVALUE_2,OXVALUE_3) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId', '0', '0', '0', '0', '0' );");

        $oView = oxNew('Article_Main');
        $oView->copyAttributes("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("select count(*) from oxobject2attribute where oxobjectid = '_testArtId2'"));
    }

    /**
     * Copying selectlists assignments
     */
    public function testCopySelectlists()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $this->getConfig()->getShopId();

        // creating few oxprice2article records
        $oDb->execute("INSERT INTO `oxobject2selectlist` (OXID,OXOBJECTID,OXSELNID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId', 0);");
        $oDb->execute("INSERT INTO `oxobject2selectlist` (OXID,OXOBJECTID,OXSELNID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId', 0);");

        $oView = oxNew('Article_Main');
        $oView->copySelectlists("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("select count(*) from oxobject2selectlist where oxobjectid = '_testArtId2'"));
    }

    /**
     * Copying files
     */
    public function testCopyFiles()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $this->getConfig()->getShopId();

        // creating few files records
        $oDb->execute("INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId');");
        $oDb->execute("INSERT INTO `oxfiles` (`OXID`, `OXARTID`, `OXFILENAME`) VALUES ('" . $oUtils->generateUId() . "', '_testArtId', '_testObjId');");

        $oView = oxNew('Article_Main');
        $oView->copyFiles("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("SELECT COUNT(*) FROM `oxfiles` WHERE `oxartid` = '_testArtId2'"));
    }

    /**
     * Copying crossseling assignments
     */
    public function testCopyCrossseling()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $this->getConfig()->getShopId();

        // creating few oxprice2article records
        $oDb->execute("INSERT INTO `oxobject2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testObjId', '_testArtId', 0);");
        $oDb->execute("INSERT INTO `oxobject2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testObjId', '_testArtId', 0);");

        $oView = oxNew('Article_Main');
        $oView->copyCrossseling("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("select count(*) from oxobject2article where oxarticlenid = '_testArtId2'"));
    }

    /**
     * Copying accessoires assignments
     */
    public function testCopyAccessoires()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $this->getConfig()->getShopId();

        // creating few oxprice2article records
        $oDb->execute("INSERT INTO `oxaccessoire2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testObjId', '_testArtId', 0);");
        $oDb->execute("INSERT INTO `oxaccessoire2article` (OXID,OXOBJECTID,OXARTICLENID,OXSORT) VALUES ('" . $oUtils->generateUId() . "', '_testObjId', '_testArtId', 0);");

        $oView = oxNew('Article_Main');
        $oView->copyAccessoires("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("select count(*) from oxaccessoire2article where oxarticlenid = '_testArtId2'"));
    }

    /**
     * Copying staffelpreis assignments
     */
    public function testCopyStaffelpreis()
    {
        $oDb = oxDb::getDb();
        $oUtils = oxRegistry::getUtilsObject();
        $iShopId = $this->getConfig()->getShopId();

        // creating few oxprice2article records
        $oDb->execute("INSERT INTO `oxprice2article` (OXID,OXSHOPID,OXARTID,OXADDABS,OXADDPERC,OXAMOUNT,OXAMOUNTTO) VALUES ('" . $oUtils->generateUId() . sprintf('\', \'%s\', \'_testArtId\', 1, 0, 2, 3);', $iShopId));
        $oDb->execute("INSERT INTO `oxprice2article` (OXID,OXSHOPID,OXARTID,OXADDABS,OXADDPERC,OXAMOUNT,OXAMOUNTTO) VALUES ('" . $oUtils->generateUId() . sprintf('\', \'%s\', \'_testArtId\', 0.5, 0, 4, 5);', $iShopId));

        $oView = oxNew('Article_Main');
        $oView->copyStaffelpreis("_testArtId", "_testArtId2");

        $this->assertEquals(2, $oDb->getOne("select count(*) from oxprice2article where oxartid = '_testArtId2'"));
    }

    /**
     * Copying article extends
     */
    public function testCopyArtExtends()
    {
        oxTestModules::addFunction('oxbase', 'save', '{ throw new Exception( "save" ); }');

        try {
            $oView = oxNew('Article_Main');
            $oView->copyArtExtends("old", "new");
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in Article_Main::copyArtExtends()");

            return;
        }

        $this->fail("error in Article_Main::copyArtExtends()");
    }

    /**
     * Testing Article_Main::saveinnlang()
     */
    public function testSaveinnlang()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, ["save"]);
        $oView->expects($this->once())->method('save');
        $oView->saveinnlang();
    }

    /**
     * Testing Article_Main::saveinnlang()
     */
    public function testSaveinnlangDefaultId()
    {
        oxTestModules::addFunction('oxarticle', 'setLanguage', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'load', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'assign', '{ return true; }');
        oxTestModules::addFunction('oxarticle', 'save', '{ throw new Exception( "save" ); }');

        $this->setRequestParameter("oxid", "123");
        $this->setRequestParameter("oxparentid", "testPArentId");
        $this->setRequestParameter("editval", ['oxarticles__oxvat' => '', 'oxarticles__oxprice' => 999]);

        // testing..
        try {
            $aTasks[] = 'resetContentCache';

            $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, $aTasks);

            $oView->expects($this->once())->method('resetContentCache');

            $oView->saveinnlang();
        } catch (Exception $exception) {
            $this->assertEquals("save", $exception->getMessage(), "error in Article_Main::saveinnlang()");

            return;
        }

        $this->fail("error in Article_Main::saveinnlang()");
    }

    /**
     * Testing Article_Main::render()
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $sOxid = oxDb::getDb()->getOne("select oxid from oxarticles where oxparentid !='' ");
        $this->setRequestParameter("oxid", $sOxid);
        //$this->setRequestParameter( "voxid", "-1" );
        //$this->setRequestParameter( "oxparentid", oxDb::getDb()->getOne( "select oxparentid from oxarticles where oxid ='{$sOxid}' " ) );

        $oView = $this->getProxyClass("Article_Main");
        $oView->setNonPublicVar("_sSavedId", $sOxid);

        $this->assertEquals("article_main", $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData['edit'] instanceof Article);
    }

    /**
     * Testing Article_Main::render()
     */
    public function testRenderLoadingParentArticle()
    {
        $oDb = oxDb::getDb();
        oxTestModules::addFunction('oxarticle', 'isDerived', '{ return true; }');
        $sOxid = $oDb->getOne("select oxid from oxarticles where oxparentid !='' ");
        $sParentOxid = $oDb->getOne(sprintf('select oxparentid from oxarticles where oxid =\'%s\' ', $sOxid));
        $this->setRequestParameter("voxid", "-1");
        $this->setRequestParameter("oxparentid", $sParentOxid);

        $oView = oxNew('Article_Main');
        $this->assertEquals("article_main", $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue($aViewData['edit'] instanceof Article);
        $this->assertTrue($aViewData['parentarticle'] instanceof Article);
        $this->assertEquals($sParentOxid, $aViewData['oxparentid']);
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Testing Article_Main::addDefaultValues()
     */
    public function testAddDefaultValues()
    {
        $oView = oxNew('Article_Main');
        $this->assertEquals("aaa", $oView->addDefaultValues("aaa"));
    }

    /**
     * Testing Article_Main::getTitle()
     */
    public function testGetTitle()
    {
        $oO1 = oxNew('oxArticle');
        $oO1->oxarticles__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ["__get"]);
        $oO1->oxarticles__oxtitle->expects($this->once())->method('__get')->will($this->returnValue("oxtitle"));

        $oO2 = oxNew('oxArticle');
        $oO2->oxarticles__oxtitle = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ["__get"]);
        $oO2->oxarticles__oxtitle->expects($this->once())->method('__get')->will($this->returnValue(null));
        $oO2->oxarticles__oxvarselect = $this->getMock(\OxidEsales\Eshop\Core\Field::class, ["__get"]);
        $oO2->oxarticles__oxvarselect->expects($this->once())->method('__get')->will($this->returnValue("oxvarselect"));

        $oView = oxNew('Article_Main');
        $this->assertEquals("oxtitle", $oView->getTitle($oO1));
        $this->assertEquals("oxvarselect", $oView->getTitle($oO2));
    }

    /**
     * Testing Article_Main::getCategoryList()
     */
    public function testGetCategoryList()
    {
        $iListSize = oxDb::getDb()->getOne("select count(*) from oxcategories");

        $oView = oxNew('Article_Main');
        $oList = $oView->getCategoryList();
        $this->assertTrue($oList instanceof CategoryList);
        $this->assertEquals($iListSize, $oList->count());
    }

    /**
     * Testing Article_Main::getVendorList()
     */
    public function testGetVendorList()
    {
        $iListSize = oxDb::getDb()->getOne("select count(*) from oxvendor");

        $oView = oxNew('Article_Main');
        $oList = $oView->getVendorList();
        $this->assertTrue($oList instanceof VendorList);
        $this->assertEquals($iListSize, $oList->count());
    }

    /**
     * Testing Article_Main::getManufacturerList()
     */
    public function testGetManufacturerList()
    {
        $iListSize = oxDb::getDb()->getOne("select count(*) from oxmanufacturers");

        $oView = oxNew('Article_Main');
        $oList = $oView->getManufacturerList();
        $this->assertTrue($oList instanceof ManufacturerList);
        $this->assertEquals($iListSize, $oList->count());
    }

    /**
     * Testing blWarnOnSameArtNums option ( FS#2489 )
     */
    public function testCopyArticle()
    {
        $this->getConfig()->setConfigParam('blWarnOnSameArtNums', true);
        $this->setRequestParameter('fnc', 'copyArticle');
        $oArtView = $this->getProxyClass("article_main");
        $oArtView->copyArticle('2000', '_testArtId');

        $aViewData = $oArtView->getNonPublicVar('_aViewData');
        $this->assertEquals(1, $aViewData["errorsavingatricle"]);
    }

    /**
     * Testing if before saving spaces are trimed from article title
     */
    public function testSaveTrimsArticleTitle()
    {
        $this->getConfig();
        $this->setRequestParameter('oxid', '_testArtId');
        $aParams['oxid'] = '_testArtId';
        $aParams['oxarticles__oxtitle'] = ' _testArticleTitle   ';

        $oArticle = oxNew('oxArticle');
        $oArticle->setId($aParams['oxid']);

        $oArticle->oxarticles__oxtitle = new oxField($aParams['oxarticles__oxtitle']);
        $oArticle->save();

        $this->setRequestParameter("editval", $aParams);

        $oArtView = $this->getProxyClass("article_main");
        $oArtView->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArtId');
        $this->assertEquals('_testArticleTitle', $oArticle->oxarticles__oxtitle->value);
    }

    /**
     * Testing error case when calling OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleMain::save
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ return true; }');
        oxTestModules::addFunction('article_main', 'saveAdditionalArticleData', '{ throw new Exception( "saveAdditionalArticleData" ); }');

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("oxparentid", "-1");
        $this->setRequestParameter("editval", ["oxarticles__oxparentid" => "-1", "oxarticles__oxartnum" => "123", "oxarticles__oxprice" => "123", "oxarticles__oxactive" => 1]);

        $oArtView = oxNew('article_main');

        try {
            $oArtView->save();
        } catch (Exception $exception) {
            $this->assertEquals("saveAdditionalArticleData", $exception->getMessage(), "error in Article_Main::save()");

            return;
        }

        $this->fail("error in Article_Main::save()");
    }

    /**
     * Testing error case when calling OxidEsales\EshopCommunity\Application\Controller\Admin\ArticleMain::save
     */
    public function testSaveNoParent()
    {
        oxTestModules::addFunction('oxarticle', 'save', '{ return true; }');
        oxTestModules::addFunction('article_main', 'saveAdditionalArticleData', '{ throw new Exception( "saveAdditionalArticleData" ); }');

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("oxparentid", "132");
        $this->setRequestParameter("editval", ["oxarticles__oxparentid" => "-1", "oxarticles__oxartnum" => "123", "oxarticles__oxprice" => "123", "oxarticles__oxactive" => 1]);
        $this->setRequestParameter("art_category", -1);

        $oArtView = oxNew('article_main');
        $oArtView->setCategoryId('_testCategoryId');

        try {
            $oArtView->save();
        } catch (Exception $exception) {
            $this->assertEquals("saveAdditionalArticleData", $exception->getMessage(), "error in Article_Main::save()");

            return;
        }

        $this->fail("error in Article_Main::save()");
    }

    /**
     * Testing article_main::formJumpList();
     */
    public function testFormJumpListParent()
    {
        $oVar1 = oxNew('oxArticle');
        $oVar1->oxarticles__oxid = new oxField("testId1");

        $oVar2 = oxNew('oxArticle');
        $oVar2->oxarticles__oxid = new oxField("testId2");

        $oParentVariants = oxNew('oxList');
        $oParentVariants->offsetSet("var1", $oVar1);
        $oParentVariants->offsetSet("var2", $oVar2);

        $oParentArticle = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, ["getAdminVariants"]);
        $oParentArticle->expects($this->once())->method('getAdminVariants')->will($this->returnValue($oParentVariants));
        $oParentArticle->oxarticles__oxid = new oxField("testParentId");

        $oVariants = oxNew('oxList');
        $oVariants->offsetSet("var1", $oVar1);
        $oVariants->offsetSet("var2", $oVar2);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getAdminVariants"]);
        $oArticle->expects($this->once())->method('getAdminVariants')->will($this->returnValue($oVariants));
        $oArticle->oxarticles__oxid = new oxField("testId2");

        $aData = [["testParentId", "testTitle"], ["testId1", " - testTitle"], ["testId2", " - testTitle"], ["testId1", " -- testTitle"], ["testId2", " -- testTitle"]];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, ["getTitle"]);
        $oView->expects($this->atLeastOnce())->method('getTitle')->will($this->returnValue("testTitle"));
        $oView->formJumpList($oArticle, $oParentArticle);
        $this->assertEquals($aData, $oView->getViewDataElement("thisvariantlist"));
    }

    /**
     * Testing article_main::formJumpList();
     */
    public function testFormJumpList()
    {
        $oVar1 = oxNew('oxArticle');
        $oVar1->oxarticles__oxid = new oxField("testId1");

        $oVar2 = oxNew('oxArticle');
        $oVar2->oxarticles__oxid = new oxField("testId2");

        $oVariants = oxNew('oxList');
        $oVariants->offsetSet("var1", $oVar1);
        $oVariants->offsetSet("var2", $oVar2);

        $oArticle = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, ["getAdminVariants"]);
        $oArticle->expects($this->once())->method('getAdminVariants')->will($this->returnValue($oVariants));
        $oArticle->oxarticles__oxid = new oxField("testId2");

        $aData = [["testId2", "testTitle"], ["testId1", " - testTitle"], ["testId2", " - testTitle"]];

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleMain::class, ["getTitle"]);
        $oView->expects($this->atLeastOnce())->method('getTitle')->will($this->returnValue("testTitle"));
        $oView->formJumpList($oArticle, null);
        $this->assertEquals($aData, $oView->getViewDataElement("thisvariantlist"));
    }

    /**
     * Testing if rating set to 0 when article will copied;
     */
    public function testCopyArticleSkipsRating()
    {
        $oArt = oxNew('oxArticle');
        $oArt->setId("_testArtId");

        $oArt->oxarticles__oxrating = new oxField(10);
        $oArt->oxarticles__oxratingcnt = new oxField(110);
        $oArt->save();

        $oV = oxNew('Article_Main');
        $oV->copyArticle('_testArtId', '_testArtId2');

        $oArt = oxNew('oxArticle');
        $this->assertTrue($oArt->load('_testArtId2'));
        $this->assertEquals(0, $oArt->oxarticles__oxrating->value);
        $this->assertEquals(0, $oArt->oxarticles__oxratingcnt->value);
    }

    /**
     * Tests that addToCategory method generates only one entry in shop database
     */
    public function testAddToCategoryGenerateOneEntry()
    {
        $iCount = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("select count(*) from oxobject2category where OXCATNID = '_testCategory1' AND OXOBJECTID = '_testArticle1'");
        $this->assertEquals(0, $iCount, sprintf('expected no entries oxobject2category, but got %s.', $iCount));

        $oV = oxNew('Article_Main');
        $oV->addToCategory('_testCategory1', '_testArticle1');

        $iCount = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getOne("select count(*) from oxobject2category where OXCATNID = '_testCategory1' AND OXOBJECTID = '_testArticle1'");
        $this->assertEquals(1, $iCount, sprintf('expected only one entry in oxobject2category, but got %s.', $iCount));
    }
}
