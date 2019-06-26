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
class AttributeMainAjaxTest extends \OxidTestCase
{
    protected $_sArticleView = 'oxv_oxarticles_1_de';
    protected $_sObject2AttributeView = 'oxv_oxobject2attribute_de';
    protected $_sObject2CategoryView = 'oxv_oxobject2category_de';
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
            $this->setArticleViewTable('oxv_oxarticles_de');
            $this->setObject2AttributeViewTable('oxv_oxobject2attribute_de');
            $this->setObject2CategoryViewTable('oxv_oxobject2category_de');
        }
        $this->addToDatabase("replace into oxarticles set oxid='_testArticleRemoveAll', oxshopid='1', oxtitle='_testArticleRemoveAll'", 'oxarticles');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttribute', oxtitle='_testAttribute'", 'oxattribute');
        $this->addToDatabase("replace into oxattribute set oxid='_testAttributeAddAll', oxtitle='_testAttributeAddAll'", 'oxattribute');
        $this->addTeardownSql("delete from oxarticles where oxid='_testArticleRemoveAll'");
        $this->addTeardownSql("delete from oxattribute where oxid like '%_testAttribute%'");

        $this->addToDatabase("replace into oxobject2attribute set oxid='_testOxid1', oxobjectid='_testObjectRemove', oxattrid='_testRemove'", 'oxobject2attribute');
        $this->addToDatabase("replace into oxobject2attribute set oxid='_testOxid2', oxobjectid='_testObjectRemove', oxattrid='_testRemove'", 'oxobject2attribute');

        $this->addToDatabase("replace into oxobject2attribute set oxid='_testOxid3', oxobjectid='_testArticleRemoveAll', oxattrid='_testRemoveAll'", 'oxobject2attribute');
        $this->addToDatabase("replace into oxobject2attribute set oxid='_testOxid4', oxobjectid='_testArticleRemoveAll', oxattrid='_testRemoveAll'", 'oxobject2attribute');
        $this->addTeardownSql("delete from oxobject2attribute where oxid  like '%_testOxid%'");
    }

    public function setArticleViewTable($sParam)
    {
        $this->_sArticleView = $sParam;
    }

    public function setObject2AttributeViewTable($sParam)
    {
        $this->_sObject2AttributeView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function setShopId($sParam)
    {
        $this->_sShopId = $sParam;
    }

    public function getArticleViewTable()
    {
        return $this->_sArticleView;
    }

    public function getObject2AttributeViewTable()
    {
        return $this->_sObject2AttributeView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }

    public function getShopId()
    {
        return $this->_sShopId;
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuery()
    {
        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testOxid';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("from " . $this->getObject2AttributeViewTable() . " left join " . $this->getArticleViewTable() . " on " . $this->getArticleViewTable() . ".oxid=" . $this->getObject2AttributeViewTable() . ".oxobjectid where " . $this->getObject2AttributeViewTable() . ".oxattrid = '$sOxid' and " . $this->getArticleViewTable() . ".oxid is not null", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getObject2AttributeViewTable() . ".oxobjectid from " . $this->getObject2AttributeViewTable() . " where " . $this->getObject2AttributeViewTable() . ".oxattrid = '$sSynchoxid' )", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeMainAjax::_getQuery() test case
     *
     * @return null
     */
    public function testGetQuerySynchoxidVariantsSelectionTrue()
    {
        $sSynchoxid = '_testSynchoxid';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxid not in ( select " . $this->getObject2AttributeViewTable() . ".oxobjectid from " . $this->getObject2AttributeViewTable() . " where " . $this->getObject2AttributeViewTable() . ".oxattrid = '$sSynchoxid' )", trim($oView->UNITgetQuery()));
    }

    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilter()
    {
        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("", trim($oView->UNITaddFilter('')));
    }

    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("group by " . $this->getArticleViewTable() . ".oxid", trim($oView->UNITaddFilter('')));
    }

    /**
     * AttributeMainAjax::_addFilter() test case
     *
     * @return null
     */
    public function testAddFilterVariantsSelection2()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $oView = oxNew('attribute_main_ajax');
        $this->assertEquals("select count( * ) from ( select count( * ) group by " . $this->getArticleViewTable() . ".oxid  ) as _cnttable", trim($oView->UNITaddFilter('select count( * )')));
    }

    /**
     * AttributeMainAjax::removeAttrArticle() test case
     *
     * @return null
     */
    public function testRemoveAttrArticle()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testOxid1', '_testOxid2')));
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='_testObjectRemove'"));

        $oView->removeAttrArticle();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxobjectid='_testObjectRemove'"));
    }

    /**
     * AttributeMainAjax::removeAttrArticle() test case
     *
     * @return null
     */
    public function testRemoveAttrArticleAll()
    {
        $sOxid = '_testRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='_testRemoveAll'"));

        $oView = oxNew('attribute_main_ajax');
        $oView->removeAttrArticle();
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='_testRemoveAll'"));
    }

    /**
     * AttributeMainAjax::addAttrArticle() test case
     *
     * @return null
     */
    public function testAddAttrArticle()
    {
        $sSynchoxid = '_testAttribute';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AttributeMainAjax::class, array("_getActionIds"));
        $oView->expects($this->any())->method('_getActionIds')->will($this->returnValue(array('_testOxidAdd1', '_testOxidAdd2')));

        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='$sSynchoxid'"));

        $oView->addAttrArticle();
        $this->assertEquals(2, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='$sSynchoxid'"));
    }

    /**
     * AttributeMainAjax::addAttrArticle() test case
     *
     * @return null
     */
    public function testAddAttrArticleAll()
    {
        $sSynchoxid = '_testAttributeAddAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        $iCount = oxDb::getDb()->getOne("select count(oxid) from " . $this->getArticleViewTable() . " where 1  and " . $this->getArticleViewTable() . ".oxparentid = ''  and " . $this->getArticleViewTable() . ".oxid not in (  select oxaccessoire2article.oxobjectid from oxaccessoire2article  where oxaccessoire2article.oxarticlenid = '$sSynchoxid'  )");
        $this->assertGreaterThan(0, $iCount);
        $this->assertEquals(0, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='$sSynchoxid'"));

        $oView = oxNew('attribute_main_ajax');
        $oView->addAttrArticle();
        $this->assertEquals($iCount, oxDb::getDb()->getOne("select count(oxid) from oxobject2attribute where oxattrid='$sSynchoxid'"));
    }
}
