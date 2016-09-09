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
 * Tests for Discount_Item_Ajax class
 */
class DiscountItemAjaxTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxdiscount set oxid='_testO2DRemove1', oxitmartid = '_testObjectRemove1', oxsort = '1900'");
        oxDb::getDb()->execute("insert into oxdiscount set oxid='_testO2DRemove2', oxitmartid = '_testObjectRemove2', oxsort = '1910'");
        oxDb::getDb()->execute("insert into oxdiscount set oxid='_testO2DRemove3', oxitmartid = '_testObjectRemove3', oxsort = '1920'");
        oxDb::getDb()->execute("insert into oxdiscount set oxid='_testO2DRemove4', oxitmartid = ''");
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute("delete from oxdiscount where oxid like '_test%'");

        parent::tearDown();
    }

    /**
     * DiscountItemAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $sArticleTable = getViewName("oxarticles");
        $sDiscTable = getViewName('oxdiscount');

        $oView = oxNew('discount_item_ajax');
        $sQuery = "from $sDiscTable left join $sArticleTable on $sArticleTable.oxid=$sDiscTable.oxitmartid ";
        $sQuery .= " where $sDiscTable.oxid = '_testOxid' and $sDiscTable.oxitmartid != ''";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountItemAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setConfigParam('blVariantParentBuyable', false);
        $sArticleTable = getViewName("oxarticles");
        $sO2CView = getViewName("oxobject2category");
        $sDiscTable = getViewName('oxdiscount');

        $oView = oxNew('discount_item_ajax');
        $sQuery = "from $sO2CView left join $sArticleTable on  $sArticleTable.oxid=$sO2CView.oxobjectid ";
        $sQuery .= " where $sO2CView.oxcatnid = '_testOxid' and $sArticleTable.oxid is not null  and ";
        $sQuery .= "$sArticleTable.oxvarcount = 0 and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from $sDiscTable, $sArticleTable where $sArticleTable.oxid=$sDiscTable.oxitmartid ";
        $sQuery .= " and $sDiscTable.oxid = '_testSynchoxid' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }


    /**
     * DiscountItemAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxidParentIsBuyable()
    {
        $sOxid = '_testOxid';
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setConfigParam('blVariantParentBuyable', true);
        $sArticleTable = getViewName("oxarticles");
        $sO2CView = getViewName("oxobject2category");
        $sDiscTable = getViewName('oxdiscount');

        $oView = oxNew('discount_item_ajax');
        $sQuery = "from $sO2CView left join $sArticleTable on  $sArticleTable.oxid=$sO2CView.oxobjectid ";
        $sQuery .= " where $sO2CView.oxcatnid = '_testOxid' and $sArticleTable.oxid is not null  and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from $sDiscTable, $sArticleTable where $sArticleTable.oxid=$sDiscTable.oxitmartid ";
        $sQuery .= " and $sDiscTable.oxid = '_testSynchoxid' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountItemAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setConfigParam('blVariantParentBuyable', false);
        $sArticleTable = getViewName("oxarticles");
        $sDiscTable = getViewName('oxdiscount');

        $oView = oxNew('discount_item_ajax');
        $sQuery = "from $sArticleTable where 1 and $sArticleTable.oxparentid = '' and $sArticleTable.oxvarcount = 0 and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from $sDiscTable, $sArticleTable where $sArticleTable.oxid=$sDiscTable.oxitmartid ";
        $sQuery .= " and $sDiscTable.oxid = '_testSynchoxid' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountItemAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidParentIsBuyable()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setConfigParam('blVariantParentBuyable', true);
        $sArticleTable = getViewName("oxarticles");
        $sDiscTable = getViewName('oxdiscount');

        $oView = oxNew('discount_item_ajax');
        $sQuery = "from $sArticleTable where 1 and $sArticleTable.oxparentid = ''  and ";
        $sQuery .= " $sArticleTable.oxid not in (  select $sArticleTable.oxid from $sDiscTable, $sArticleTable where $sArticleTable.oxid=$sDiscTable.oxitmartid ";
        $sQuery .= " and $sDiscTable.oxid = '_testSynchoxid' )";
        $this->assertEquals($sQuery, trim($oView->UNITgetQuery()));
    }

    /**
     * DiscountItemAjax::removeDiscArt() test case
     *
     * @return null
     */
    public function testRemoveDiscArt()
    {
        $this->setRequestParameter("oxid", '_testO2DRemove1');
        $oView = $this->getMock("discount_item_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testObjectRemove1', '_testObjectRemove2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxdiscount where oxid like '_test%' and oxitmartid != ''"));

        $oView->removeDiscArt();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxdiscount where oxid like '_test%' and oxitmartid != ''"));
    }

    /**
     * DiscountItemAjax::addDiscArt() test case
     *
     * @return null
     */
    public function testAddDiscArt()
    {
        $sSynchoxid = '_testO2DRemove4';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock("discount_item_ajax", array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testArticleAdd1', '_testArticleAdd2')));
        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxdiscount where oxid like '_test%' and oxitmartid != ''"));

        $oView->addDiscArt();
        $this->assertEquals(4, oxDb::getDb()->getOne("select count(oxid) from oxdiscount where oxid like '_test%' and oxitmartid != ''"));
    }

    /**
     * DiscountItemAjax::_getQueryCols() test case
     *
     * @return null
     */
    public function testGetQueryCols()
    {
        $this->setRequestParameter("aCols", null);
        $this->setConfigParam('blVariantsSelection', false);

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );
        $sTableName = getViewName("oxarticles");
        $sQ = " $sTableName.oxartnum as _0, $sTableName.oxtitle as _1, $sTableName.oxean as _2, $sTableName.oxmpn as _3, $sTableName.oxprice as _4, $sTableName.oxstock as _5, $sTableName.oxid as _6 ";

        $oComponent = $this->getMock("discount_item_ajax", array("_getColNames"));
        $oComponent->expects($this->any())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals($sQ, $oComponent->UNITgetQueryCols());
    }

    /**
     * DiscountItemAjax::_getQueryCols() test case
     *
     * @return null
     */
    public function testGetQueryColsWithVariants()
    {
        $this->setRequestParameter("aCols", null);
        $this->setConfigParam('blVariantsSelection', true);

        $aColNames = array( // field , table,         visible, multilanguage, ident
            array('oxartnum', 'oxarticles', 1, 0, 0),
            array('oxtitle', 'oxarticles', 1, 1, 0),
            array('oxean', 'oxarticles', 1, 0, 0),
            array('oxmpn', 'oxarticles', 0, 0, 0),
            array('oxprice', 'oxarticles', 0, 0, 0),
            array('oxstock', 'oxarticles', 0, 0, 0),
            array('oxid', 'oxarticles', 0, 0, 1)
        );
        $sTableName = getViewName("oxarticles");
        $sQ = " $sTableName.oxartnum as _0,  IF( $sTableName.oxtitle != '', $sTableName.oxtitle, CONCAT((select oxart.oxtitle from $sTableName as oxart where oxart.oxid = $sTableName.oxparentid),', ',$sTableName.oxvarselect)) as _1, $sTableName.oxean as _2, $sTableName.oxmpn as _3, $sTableName.oxprice as _4, $sTableName.oxstock as _5, $sTableName.oxid as _6 ";

        $oComponent = $this->getMock("discount_item_ajax", array("_getColNames"));
        $oComponent->expects($this->any())->method('_getColNames')->will($this->returnValue($aColNames));
        $this->assertEquals($sQ, $oComponent->UNITgetQueryCols());
    }

}
