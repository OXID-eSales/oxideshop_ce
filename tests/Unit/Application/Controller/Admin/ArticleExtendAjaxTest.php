<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Article_Extend_Ajax class
 */
class ArticleExtendAjaxTest extends \OxidTestCase
{
    protected $_sCategoriesView = 'oxv_oxcategories_1_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';
    protected $_sShopId = '1';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        if ($this->getConfig()->getEdition() !== 'EE') {
            $this->setCategoriesViewTable('oxv_oxcategories_de');
            $this->setObject2CategoryViewTable('oxobject2category');

            $this->addToDatabase("insert into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='" . $this->getShopIdTest() . "'", 'oxcategories');
            $this->addToDatabase("insert into oxobject2category set oxid='_testObject2Category', oxcatnid='_testCategory', oxobjectid = '_testObject'", 'oxobject2category');
        } else {
            $this->addToDatabase("insert into oxcategories set oxid='_testCategory', oxtitle='_testCategory'", 'oxcategories');
            $this->addToDatabase("insert into oxobject2category set oxid='_testObject2Category', oxshopid='" . $this->getShopIdTest() . "', oxcatnid='_testCategory', oxobjectid = '_testObject'", 'oxobject2category');
        }

        $this->addToDatabase("insert into oxcategories set oxid='_testCategory1', oxtitle='_testCategory1', oxshopid='" . $this->getShopIdTest() . "'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testCategory2', oxtitle='_testCategory2', oxshopid='" . $this->getShopIdTest() . "'", 'oxcategories');
        $this->addToDatabase("insert into oxcategories set oxid='_testCategory3', oxtitle='_testCategory3', oxshopid='" . $this->getShopIdTest() . "'", 'oxcategories');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove1', oxcatnid='_testCategory1', oxobjectid = '_testObjectRemove'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemove2', oxcatnid='_testCategory2', oxobjectid = '_testObjectRemove'", 'oxobject2category');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemoveAll1', oxcatnid='_testCategory1', oxobjectid = '_testObjectRemoveAll'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemoveAll2', oxcatnid='_testCategory2', oxobjectid = '_testObjectRemoveAll'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryRemoveAll3', oxcatnid='_testCategory3', oxobjectid = '_testObjectRemoveAll'", 'oxobject2category');

        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryUpdateDate', oxcatnid='_testCategory', oxobjectid = '_testObjectUpdateDate'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryDefault1', oxcatnid='_testCategory1', oxobjectid = '_testObjectDefault'", 'oxobject2category');
        $this->addToDatabase("insert into oxobject2category set oxid='_testObject2CategoryDefault2', oxcatnid='_testCategory2', oxobjectid = '_testObjectDefault'", 'oxobject2category');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->addTeardownSql("delete from oxcategories where oxid like '%_testCategory%'");
        $this->addTeardownSql("delete from oxobject2category where oxid='_testObject2Category'");
        $this->addTeardownSql("delete from oxobject2category where oxobjectid like '%_testObject%'");

        parent::tearDown();
    }

    public function setCategoriesViewTable($sParam)
    {
        $this->_sCategoriesView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function setShopIdTest($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getCategoriesViewTable()
    {
        return $this->_sCategoriesView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }

    public function getShopIdTest()
    {
        return $this->_sShopId;
    }

    /**
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('article_extend_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable() . " where " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from " . $this->getObject2CategoryViewTable() . " left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxcatnid  where " . $this->getObject2CategoryViewTable() . ".oxobjectid = '' and " . $this->getCategoriesViewTable() . ".oxid is not null ) and " . $this->getCategoriesViewTable() . ".oxpriceto = '0'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('article_extend_ajax');
        $this->assertEquals("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxcatnid  where " . $this->getObject2CategoryViewTable() . ".oxobjectid = '$sOxid' and " . $this->getCategoriesViewTable() . ".oxid is not null", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleExtendAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('article_extend_ajax');
        $this->assertEquals("from " . $this->getCategoriesViewTable() . " where " . $this->getCategoriesViewTable() . ".oxid not in (  select " . $this->getCategoriesViewTable() . ".oxid from " . $this->getObject2CategoryViewTable() . " left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxcatnid  where " . $this->getObject2CategoryViewTable() . ".oxobjectid = '$sSynchoxid' and " . $this->getCategoriesViewTable() . ".oxid is not null ) and " . $this->getCategoriesViewTable() . ".oxpriceto = '0'", trim($oView->UNITgetQuery()));
    }

    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */
    public function testGetDataFields()
    {
        $aResult = array(array('_0' => '_testCategory',
                               '_1' => false,
                               '_3' => '_testObject2Category',
                               '_4' => 0,
                               '_5' => '_testCategory'
                         )
        );

        $oView = oxNew('article_extend_ajax');
        $this->assertEquals($aResult, $oView->UNITgetDataFields("select  " . $this->getCategoriesViewTable() . ".oxtitle as _0, " . $this->getCategoriesViewTable() . ".oxdesc as _1, oxobject2category.oxid as _3, oxobject2category.oxtime as _4, " . $this->getCategoriesViewTable() . ".oxid as _5  from oxobject2category left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '_testObject' and " . $this->getCategoriesViewTable() . ".oxid is not null  order by _0 asc  limit 0, 25 "));
    }

    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */
    public function testGetDataFieldsOxid()
    {
        $this->setRequestParameter("oxid", true);
        $aResult = array(array('_0' => '_testCategory',
                               '_1' => false,
                               '_3' => 0,
                               '_4' => 0,
                               '_5' => '_testCategory'
                         )
        );

        $oView = oxNew('article_extend_ajax');
        $this->assertEquals($aResult, $oView->UNITgetDataFields("select  " . $this->getCategoriesViewTable() . ".oxtitle as _0, " . $this->getCategoriesViewTable() . ".oxdesc as _1, oxobject2category.oxid as _3, oxobject2category.oxtime as _4, " . $this->getCategoriesViewTable() . ".oxid as _5  from oxobject2category left join " . $this->getCategoriesViewTable() . " on " . $this->getCategoriesViewTable() . ".oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '_testObject' and " . $this->getCategoriesViewTable() . ".oxid is not null  order by _0 asc  limit 0, 25 "));
    }

    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */
    public function testGetDataFieldsFalse()
    {
        $oView = oxNew('article_extend_ajax');
        $this->assertEquals(array(array('FALSE' => 0)), $oView->UNITgetDataFields('select false'));
    }

    /**
     * ArticleExtendAjax::_getDataFields() test case
     *
     * @return null
     */
    public function testGetDataFieldsOxidFalse()
    {
        $this->setRequestParameter("oxid", true);
        $oView = oxNew('article_extend_ajax');
        $this->assertEquals(array(array('FALSE' => 0, '_3' => 0)), $oView->UNITgetDataFields('select false'));
    }

    /**
     * ArticleExtendAjax::removeCat() test case
     *
     * @return null
     */
    public function testRemoveCat()
    {
        $sOxid = '_testObjectRemove';
        $this->setRequestParameter("oxid", $sOxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtendAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testCategory1', '_testCategory2')));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sOxid'"));

        $oView->removeCat();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sOxid'"));
    }

    /**
     * ArticleExtendAjax::removeCat() test case
     *
     * @return null
     */
    public function testRemoveCatAll()
    {
        $sOxid = '_testObjectRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(3, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sOxid'"));

        $oView = oxNew('article_extend_ajax');
        $oView->removeCat();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sOxid'"));
    }

    /**
     * ArticleExtendAjax::addCat() test case
     *
     * @return null
     */
    public function testAddCat()
    {
        $sSynchoxid = '_testObjectAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtendAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testCategoryAdd1', '_testCategoryAdd2')));
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'"));

        $oView->addCat();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'"));
    }

    /**
     * ArticleExtendAjax::addCat() test case
     *
     * @return null
     */
    public function testAddCatAll()
    {
        $sSynchoxid = '_testObjectAdd';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        if ($this->getConfig()->getEdition() === 'EE') {
            $iCount = oxDb::getDb()->getOne("select count(oxv_oxcategories_1_de.oxid)  from oxv_oxcategories_1_de where oxv_oxcategories_1_de.oxid not in (  select oxv_oxcategories_1_de.oxid from oxv_oxobject2category_1 left join oxv_oxcategories_1_de on oxv_oxcategories_1_de.oxid=oxv_oxobject2category_1.oxcatnid  where oxv_oxobject2category_1.oxobjectid = '$sSynchoxid' and oxv_oxcategories_1_de.oxid is not null ) and oxv_oxcategories_1_de.oxpriceto = '0'");
        } else {
            $iCount = oxDb::getDb()->getOne("select count(oxv_oxcategories_de.oxid)  from oxv_oxcategories_de where oxv_oxcategories_de.oxid not in (  select oxv_oxcategories_de.oxid from oxobject2category left join oxv_oxcategories_de on oxv_oxcategories_de.oxid=oxobject2category.oxcatnid  where oxobject2category.oxobjectid = '$sSynchoxid' and oxv_oxcategories_de.oxid is not null ) and oxv_oxcategories_de.oxpriceto = '0'");
        }

        $oView = oxNew('article_extend_ajax');
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'"));

        $oView->addCat();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2category where oxobjectid='$sSynchoxid'"));
    }

    /**
     * ArticleExtendAjax::_updateOxTime() test case
     *
     * @return null
     */
    public function testUpdateOxTime()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testObjectUpdateDate';

        $oView = oxNew('article_extend_ajax');

        $sO2CView = $oView->UNITgetViewName('oxobject2category');

        // updating oxtime values
        $sQ = "update oxobject2category set oxtime = 1 where oxobjectid = '$sOxid' ";
        $oDb->execute($sQ);

        $oView->UNITupdateOxTime($sOxid);
        $this->assertEquals(1, $oDb->getOne("select count(oxid) from oxobject2category where oxtime=0 and oxobjectid = '$sOxid' limit 1"));
    }

    /**
     * ArticleExtendAjax::setAsDefault() test case
     *
     * @return null
     */
    public function testSetAsDefault()
    {
        $sOxid = '_testObjectDefault';
        $sDefCat = '_testCategory1';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("defcat", $sDefCat);

        $oView = oxNew('article_extend_ajax');

        $sShopCheck = "";

        $oDb = oxDb::getDb();
        $oDb->execute("update oxobject2category set oxtime = 1 where oxobjectid = '$sOxid' ");

        $oView->setAsDefault();

        $this->assertEquals(11, $oDb->getOne("select oxtime from oxobject2category where oxobjectid='$sOxid' and oxcatnid!='$sDefCat'"));
        $this->assertEquals(0, $oDb->getOne("select oxtime from oxobject2category where oxobjectid='$sOxid' and oxcatnid='$sDefCat' $sShopCheck"));
    }
}
