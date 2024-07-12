<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Application\Model\Category;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Category_Main class
 */
class CategoryMainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var oxCategory
     */
    private $_oCategory;

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var oxCategory $oCategory */
        $oCategory = oxNew('oxCategory');
        $oCategory->setId('_testCatId');

        $oCategory->oxcategories__oxparentid = new oxField('oxrootid');
        $oCategory->save();

        $this->_oCategory = $oCategory;
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->cleanUpTable('oxcategories');
        oxDb::getDb()->execute("DELETE FROM oxcategories WHERE OXTITLE = 'Test category title for unit' ");

        parent::tearDown();
    }

    /**
     * Category_Main::Render() test case
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction("oxcategory", "isDerived", "{return true;}");

        // testing..
        $oView = oxNew('Category_Main');
        $this->assertSame('category_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('edit', $aViewData);
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Model\Category::class, $aViewData['edit']);
    }

    /**
     * Content_Main::Render() test case
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Main');
        $this->assertSame('category_main', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey('oxid', $aViewData);
        $this->assertSame("-1", $aViewData['oxid']);
    }

    /**
     * Category_Main::Save() test case when oxactive = 0
     */
    public function testSaveActiveSet0()
    {
        $aParams = ["oxcategories__oxactive"   => 0, "oxcategories__oxparentid" => "oxrootid", "oxcategories__oxtitle"    => "Test category title for unit"];

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("editval", $aParams);

        $oView = oxNew('Category_Main');
        $oView->save();

        $sActive = oxDb::getDb()->getOne("SELECT OXACTIVE FROM oxcategories WHERE OXTITLE='Test category title for unit'");
        $this->assertSame(0, $sActive);
    }

    /**
     * Category_Main::Save() test case
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ return true; }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Category_Main');
        $oView->save();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Save() test case
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ $this->oxcategories__oxid = new oxField( "testId" ); return true; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Main');
        $oView->save();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Saveinnlang() test case
     */
    public function testSaveinnlang()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxcategory', 'save', '{ return true; }');

        // testing..
        $oView = oxNew('Category_Main');
        $oView->saveinnlang();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Saveinnlang() test case
     */
    public function testSaveinnlangDefaultOxid()
    {
        $this->setRequestParameter("oxid", "-1");
        oxTestModules::addFunction('oxcategory', 'save', '{ $this->oxcategories__oxid = new oxField( "testId" ); return true; }');

        // testing..
        $oView = oxNew('Category_Main');
        $oView->saveinnlang();

        $this->assertSame("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Test get sortable fields.
     */
    public function testGetSortableFields()
    {
        $oCatMain = oxNew('Category_Main');

        $aFields = $oCatMain->getSortableFields();
        $this->assertContains('OXTITLE', $aFields);
        $this->assertNotContains('OXAMITEMID', $aFields);
    }

    /**
     * Category_Main::deleteCatPicture() test case - deleting invalid field
     */
    public function testDeletePicture_deletingInvalidField()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxtitle = new oxField('Test_title');
        $this->_oCategory->save();
        $this->assertSame('Test_title', $oDb->getOne("select oxtitle from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMain $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->deleteCatPicture($this->_oCategory, 'oxtitle');
        $this->assertSame('Test_title', $oDb->getOne("select oxtitle from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::deleteCatPicture() test case - deleting thumb
     */
    public function testDeletePicture_deletingPromoIcon()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxpromoicon = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertSame('testIcon.jpg', $oDb->getOne("select oxpromoicon from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMain $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->deleteCatPicture($this->_oCategory, 'oxpromoicon');
        $this->assertSame('', $oDb->getOne("select oxpromoicon from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::deleteCatPicture() test case - deleting thumb
     */
    public function testDeletePicture_deletingThumb()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxthumb = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertSame('testIcon.jpg', $oDb->getOne("select oxthumb from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMain $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->deleteCatPicture($this->_oCategory, 'oxthumb');
        $this->assertSame('', $oDb->getOne("select oxthumb from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::deleteCatPicture() test case - deleting icon
     */
    public function testDeletePicture_deletingIcon()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxicon = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertSame('testIcon.jpg', $oDb->getOne("select oxicon from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMain $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->deleteCatPicture($this->_oCategory, 'oxicon');
        $this->assertSame('', $oDb->getOne("select oxicon from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::deletePicture() - in demo shop mode
     */
    public function testDeletePicture_demoShopMode()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isDemoShop"]);
        $oConfig->expects($this->once())->method('isDemoShop')->willReturn(true);

        oxRegistry::getSession()->deleteVariable("Errors");

        /** @var \OxidEsales\Eshop\Application\Controller\Admin\CategoryMain $oView */
        $oView = $this->getProxyClass("Category_Main");
        Registry::set(Config::class, $oConfig);
        $oView->deletePicture();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sExpMsg = oxRegistry::getLang()->translateString('CATEGORY_PICTURES_UPLOADISDISABLED');

        $this->assertNotEmpty($sExpMsg, 'no translation for CATEGORY_PICTURES_UPLOADISDISABLED');
        $this->assertEquals($sExpMsg, $oEx->getOxMessage());
    }
}
