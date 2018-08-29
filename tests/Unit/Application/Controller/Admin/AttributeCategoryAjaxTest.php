<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;

/**
 * Tests for Attribute_Category_Ajax class
 */
class AttributeCategoryAjaxTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        $this->addToDatabase("replace into oxcategories set oxid='_testCategory', oxtitle='_testCategory', oxshopid='" . $this->getShopIdTest() . "', oxactive=1", 'oxcategories');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttribute', oxtitle='_testAttribute', oxshopid='" . $this->getShopIdTest() . "'", 'oxattribute');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttributeAll', oxtitle='_testAttributeAll', oxshopid='" . $this->getShopIdTest() . "'", 'oxattribute');
        $this->addTeardownSql("delete from oxcategories where oxid='_testCategory'");
        $this->addTeardownSql("delete from oxattribute where oxid like '%_testAttribute%'");
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid1', oxobjectid='_testRemove'", 'oxcategory2attribute');
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid2', oxobjectid='_testRemove'", 'oxcategory2attribute');

        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid3', oxobjectid='_testCategory', oxattrid='_testRemoveAll'", 'oxcategory2attribute');
        $this->addToDatabase("replace into oxcategory2attribute set oxid='_testOxid4', oxobjectid='_testCategory', oxattrid='_testRemoveAll'", 'oxcategory2attribute');
        $this->addTeardownSql("delete from oxcategory2attribute where oxid like '%_testOxid%'");
    }

    public function getCategoryViewTable()
    {
        return $this->getTestConfig()->getShopEdition() == 'EE' ? 'oxv_oxcategories_1_de' : 'oxv_oxcategories_de';
    }

    public function getShopIdTest()
    {
        return '1';
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'  and " . $this->getCategoryViewTable() . ".oxid not in ( select " . $this->getCategoryViewTable() . ".oxid from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sSynchoxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1' )", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('attribute_category_ajax');
        $this->assertEquals("from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sOxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeCategoryAjax::removeCatFromAttr() test case
     *
     * @return null
     */
    public function testRemoveCatFromAttr()
    {
        $oDb = oxDb::getDb();

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeCategoryAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testOxid1', '_testOxid2')));

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxobjectid='_testRemove'"));
        $oView->removeCatFromAttr();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxobjectid='_testRemove'"));
    }

    /**
     * AttributeCategoryAjax::removeCatFromAttr() test case
     *
     * @return null
     */
    public function testRemoveCatFromAttrAll()
    {
        $oDb = oxDb::getDb();
        $sOxid = '_testRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $this->assertEquals(2, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxattrid='_testRemoveAll' "));
        $oView = oxNew('attribute_category_ajax');
        $oView->removeCatFromAttr();
        $this->assertEquals(0, $oDb->getOne("select count(oxid) from oxcategory2attribute where oxattrid='_testRemoveAll' "));
    }

    /**
     * AttributeCategoryAjax::addCatToAttr() test case
     *
     * @return null
     */
    public function testAddCatToAttr()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeCategoryAjax::class, array("_getActionIds"));
        $sSynchoxid = '_testAttribute';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testAdd1', '_testAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
        $oView->addCatToAttr();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
    }

    /**
     * AttributeCategoryAjax::addCatToAttr() test case
     *
     * @return null
     */
    public function testAddCatToAttrAll()
    {
        $sSynchoxid = '_testAttributeAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getCategoryViewTable() . ".oxid)  from " . $this->getCategoryViewTable() . " where " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1'  and " . $this->getCategoryViewTable() . ".oxid not in ( select " . $this->getCategoryViewTable() . ".oxid from " . $this->getCategoryViewTable() . " left join oxcategory2attribute on " . $this->getCategoryViewTable() . ".oxid=oxcategory2attribute.oxobjectid  where oxcategory2attribute.oxattrid = '$sSynchoxid' and " . $this->getCategoryViewTable() . ".oxshopid = '" . $this->getShopIdTest() . "'  and " . $this->getCategoryViewTable() . ".oxactive = '1' )");

        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
        $oView = oxNew('attribute_category_ajax');
        $oView->addCatToAttr();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxcategory2attribute where oxattrid='$sSynchoxid'"));
    }
}
