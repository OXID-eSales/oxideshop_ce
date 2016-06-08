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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Discount_Categories_Ajax class
 */
class DiscountCategoriesAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove1', oxtitle='_testCat1', oxshopid='1'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove2', oxtitle='_testCat2', oxshopid='1'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testObjectRemove3', oxtitle='_testCat3', oxshopid='1'", 'oxcategories');

        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove1', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove1', oxtype = 'oxcategories'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove2', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove2', oxtype = 'oxcategories'", 'oxobject2discount');
        $this->addToDatabase("insert into oxobject2discount set oxid='_testO2DRemove3', oxdiscountid='_testDiscount', oxobjectid = '_testObjectRemove3', oxtype = 'oxcategories'", 'oxobject2discount');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxobject2discount');
        $this->cleanUpTable('oxcategories');

        parent::tearDown();
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sCategoryTable = getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = "from $sCategoryTable";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sCategoryTable = getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = "from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testOxid' and oxobject2discount.oxtype = 'oxcategories'  and ";
        $sQuery .= " $sCategoryTable.oxid not in (  select $sCategoryTable.oxid from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountCategoriesAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sCategoryTable = getViewName("oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $sQuery = "from $sCategoryTable where ";
        $sQuery .= " $sCategoryTable.oxid not in (  select $sCategoryTable.oxid from oxobject2discount, $sCategoryTable where $sCategoryTable.oxid=oxobject2discount.oxobjectid ";
        $sQuery .= " and oxobject2discount.oxdiscountid = '_testSynchoxid' and oxobject2discount.oxtype = 'oxcategories'  )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountArticlesAjax::removeDiscCat() test case
     *
     * @return null
     */
    public function testRemoveDiscCat()
    {
        $oView = $this->getMock("discount_categories_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testO2DRemove1', '_testO2DRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->removeDiscCat();
        $this->assertEquals(1, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::removeDiscArt() test case
     *
     * @return null
     */
    public function testRemoveDiscCatAll()
    {
        $sOxid = '_testDiscount';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView = oxNew('discount_categories_ajax');
        $oView->removeDiscCat();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     *
     * @return null
     */
    public function testAddDiscCat()
    {
        $sSynchoxid = '_testDiscount';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock("discount_categories_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testCatAdd1', '_testCatAdd1')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));

        $oView->addDiscCat();
        $this->assertEquals(5, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='_testDiscount'"));
    }

    /**
     * DiscountArticlesAjax::addDiscCat() test case
     *
     * @return null
     */
    public function testAddDiscCatAll()
    {
        $this->cleanUpTable('oxobject2discount', 'oxdiscountid');
        $sSynchoxid = '_testDiscountNew';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from oxcategories");

        $oView = oxNew('discount_categories_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));

        $oView->addDiscCat();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2discount where oxdiscountid='$sSynchoxid'"));
    }
}
