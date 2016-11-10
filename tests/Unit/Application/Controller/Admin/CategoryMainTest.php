<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Application\Model\Category;
use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Category_Main class
 */
class CategoryMainTest extends \OxidTestCase
{
    /**
     * @var oxCategory
     */
    private $_oCategory = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
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
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxcategories');
        oxDb::getDb()->execute("DELETE FROM oxcategories WHERE OXTITLE = 'Test category title for unit' ");

        parent::tearDown();
    }

    /**
     * Category_Main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction("oxcategory", "isDerived", "{return true;}");

        // testing..
        $oView = oxNew('Category_Main');
        $this->assertEquals('category_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['edit']));
        $this->assertTrue($aViewData['edit'] instanceof Category);
    }

    /**
     * Content_Main::Render() test case
     *
     * @return null
     */
    public function testRenderNoRealObjectId()
    {
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Main');
        $this->assertEquals('category_main.tpl', $oView->render());
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData['oxid']));
        $this->assertEquals("-1", $aViewData['oxid']);
    }

    /**
     * Category_Main::Save() test case when oxactive = 0
     *
     * @return null
     */
    public function testSaveActiveSet0()
    {
        $aParams = array("oxcategories__oxactive"   => 0,
                         "oxcategories__oxparentid" => "oxrootid",
                         "oxcategories__oxtitle"    => "Test category title for unit");

        $this->setRequestParameter("oxid", -1);
        $this->setRequestParameter("editval", $aParams);

        $oView = oxNew('Category_Main');
        $oView->save();

        $sActive = oxDb::getDb()->getOne("SELECT OXACTIVE FROM oxcategories WHERE OXTITLE='Test category title for unit'");
        $this->assertEquals(0, $sActive);
    }

    /**
     * Category_Main::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ return true; }');
        $this->setRequestParameter("oxid", "testId");

        // testing..
        $oView = oxNew('Category_Main');
        $oView->save();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Save() test case
     *
     * @return null
     */
    public function testSaveDefaultOxid()
    {
        oxTestModules::addFunction('oxcategory', 'save', '{ $this->oxcategories__oxid = new oxField( "testId" ); return true; }');
        $this->setRequestParameter("oxid", "-1");

        // testing..
        $oView = oxNew('Category_Main');
        $oView->save();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlang()
    {
        $this->setRequestParameter("oxid", "testId");
        oxTestModules::addFunction('oxcategory', 'save', '{ return true; }');

        // testing..
        $oView = oxNew('Category_Main');
        $oView->saveinnlang();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Category_Main::Saveinnlang() test case
     *
     * @return null
     */
    public function testSaveinnlangDefaultOxid()
    {
        $this->setRequestParameter("oxid", "-1");
        oxTestModules::addFunction('oxcategory', 'save', '{ $this->oxcategories__oxid = new oxField( "testId" ); return true; }');

        // testing..
        $oView = oxNew('Category_Main');
        $oView->saveinnlang();

        $this->assertEquals("1", $oView->getViewDataElement("updatelist"));
    }

    /**
     * Test get sortable fields.
     *
     * @return null
     */
    public function testGetSortableFields()
    {
        $oCatMain = oxNew('Category_Main');

        $aFields = $oCatMain->getSortableFields();
        $this->assertTrue(in_array('OXTITLE', $aFields));
        $this->assertFalse(in_array('OXAMITEMID', $aFields));
    }

    /**
     * Category_Main::_deleteCatPicture() test case - deleting invalid field
     *
     * @return null
     */
    public function testDeletePicture_deletingInvalidField()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxtitle = new oxField('Test_title');
        $this->_oCategory->save();
        $this->assertEquals('Test_title', $oDb->getOne("select oxtitle from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var Category_Main $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->UNITdeleteCatPicture($this->_oCategory, 'oxtitle');
        $this->assertEquals('Test_title', $oDb->getOne("select oxtitle from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::_deleteCatPicture() test case - deleting thumb
     *
     * @return null
     */
    public function testDeletePicture_deletingPromoIcon()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxpromoicon = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertEquals('testIcon.jpg', $oDb->getOne("select oxpromoicon from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var Category_Main $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->UNITdeleteCatPicture($this->_oCategory, 'oxpromoicon');
        $this->assertEquals('', $oDb->getOne("select oxpromoicon from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::_deleteCatPicture() test case - deleting thumb
     *
     * @return null
     */
    public function testDeletePicture_deletingThumb()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxthumb = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertEquals('testIcon.jpg', $oDb->getOne("select oxthumb from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var Category_Main $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->UNITdeleteCatPicture($this->_oCategory, 'oxthumb');
        $this->assertEquals('', $oDb->getOne("select oxthumb from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::_deleteCatPicture() test case - deleting icon
     *
     * @return null
     */
    public function testDeletePicture_deletingIcon()
    {
        $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);

        $this->_oCategory->oxcategories__oxicon = new oxField('testIcon.jpg');
        $this->_oCategory->save();
        $this->assertEquals('testIcon.jpg', $oDb->getOne("select oxicon from oxcategories where oxid='_testCatId' "), 'Category save operation failed');

        /** @var Category_Main $oView */
        $oView = $this->getProxyClass('Category_Main');
        $oView->UNITdeleteCatPicture($this->_oCategory, 'oxicon');
        $this->assertEquals('', $oDb->getOne("select oxicon from oxcategories where oxid='_testCatId' "));
    }

    /**
     * Category_Main::deletePicture() - in demo shop mode
     *
     * @return null
     */
    public function testDeletePicture_demoShopMode()
    {
        $oConfig = $this->getMock("oxConfig", array("isDemoShop"));
        $oConfig->expects($this->once())->method('isDemoShop')->will($this->returnValue(true));

        oxRegistry::getSession()->deleteVariable("Errors");

        /** @var Category_Main $oView */
        $oView = $this->getProxyClass("Category_Main");
        $oView->setConfig($oConfig);
        $oView->deletePicture();

        $aEx = oxRegistry::getSession()->getVariable("Errors");
        $oEx = unserialize($aEx["default"][0]);
        $sExpMsg = oxRegistry::getLang()->translateString('CATEGORY_PICTURES_UPLOADISDISABLED');

        $this->assertFalse(empty($sExpMsg), 'no translation for CATEGORY_PICTURES_UPLOADISDISABLED');
        $this->assertEquals($sExpMsg, $oEx->getOxMessage());
    }
}
