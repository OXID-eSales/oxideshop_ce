<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxDb;
use OxidEsales\Facts\Facts;

/**
 * Tests for Delivery_Articles_Ajax class
 */
class DeliveryArticlesAjaxTest extends \PHPUnit\Framework\TestCase
{
    protected $_sArticlesView = 'oxv_oxarticles_1_de';

    protected $_sCategoriesView = 'oxv_oxcategories_1_de';

    protected $_sObject2CategoryView = 'oxv_oxobject2category_1';

    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDelivery1', oxobjectid='_testObjectId'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDelivery2', oxobjectid='_testObjectId'");
        //for delete all
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryDeleteAll1', oxdeliveryid='_testDelieveryRemoveAll', oxobjectid='_testObjectId', oxtype='oxarticles'");
        oxDb::getDb()->execute("insert into oxobject2delivery set oxid='_testDeliveryDeleteAll2', oxdeliveryid='_testDelieveryRemoveAll', oxobjectid='_testObjectId', oxtype='oxarticles'");

        $shopId = '1';

        oxDb::getDb()->execute("insert into oxarticles set oxid='_testArticle1', oxshopid='" . $shopId . "', oxtitle='_testArticle1'");
        oxDb::getDb()->execute("insert into oxarticles set oxid='_testArticle2', oxshopid='" . $shopId . "', oxtitle='_testArticle2'");

        if ((new Facts())->getEdition() !== 'EE') {
            $this->setArticlesViewTable('oxv_oxarticles_de');
            $this->setCategoriesViewTable('oxv_oxcategories_de');
            $this->setObject2CategoryViewTable('oxobject2category');
        }
    }

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDelivery1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDelivery2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryDeleteAll1'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxid='_testDeliveryDeleteAll2'");

        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle1'");
        oxDb::getDb()->execute("delete from oxarticles where oxid='_testArticle2'");

        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddArt'");
        oxDb::getDb()->execute("delete from oxobject2delivery where oxdeliveryid='_testActionAddArtAll'");

        parent::tearDown();
    }

    public function setArticlesViewTable($sParam)
    {
        $this->_sArticlesView = $sParam;
    }

    public function setCategoriesViewTable($sParam)
    {
        $this->_sCategoriesView = $sParam;
    }

    public function setObject2CategoryViewTable($sParam)
    {
        $this->_sObject2CategoryView = $sParam;
    }

    public function getArticlesViewTable()
    {
        return $this->_sArticlesView;
    }

    public function getCategoriesViewTable()
    {
        return $this->_sCategoriesView;
    }

    public function getObject2CategoryViewTable()
    {
        return $this->_sObject2CategoryView;
    }


    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQuery()
    {
        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = ''", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQueryVariantsSelectionTrue()
    {
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getArticlesViewTable() . " where 1", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQuerySynchoxid()
    {
        $sSynchoxid = '_testAction';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = '' and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQuerySynchoxidSelectionTrue()
    {
        $sSynchoxid = '_testAction';

        $this->getConfig()->setConfigParam("blVariantsSelection", true);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQueryOxid()
    {
        $sOxid = '_testAction';
        $this->setRequestParameter("oxid", $sOxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from oxobject2delivery left join " . $this->getArticlesViewTable() . " on " . $this->getArticlesViewTable() . ".oxid=oxobject2delivery.oxobjectid where oxobject2delivery.oxdeliveryid = '" . $sOxid . "' and oxobject2delivery.oxtype = \"oxarticles\"", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxid()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticlesViewTable() . " on  " . $this->getArticlesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "'and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::getQuery() test case
     */
    public function testGetQueryOxidSynchoxidVariantsSelection()
    {
        $sOxid = '_testAction';
        $sSynchoxid = '_testActionSynch';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->getConfig()->setConfigParam("blVariantsSelection", true);

        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame("from " . $this->getObject2CategoryViewTable() . " left join " . $this->getArticlesViewTable() . " on  ( " . $this->getArticlesViewTable() . ".oxid=" . $this->getObject2CategoryViewTable() . ".oxobjectid or " . $this->getArticlesViewTable() . ".oxparentid=" . $this->getObject2CategoryViewTable() . ".oxobjectid)where " . $this->getObject2CategoryViewTable() . ".oxcatnid = '" . $sOxid . "'and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = \"oxarticles\" )", trim((string) $oView->getQuery()));
    }

    /**
     * DeliveryArticlesAjax::removeArtFromDel() test case
     */
    public function testRemoveArtFromDel()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryArticlesAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testDelivery1', '_testDelivery2']);

        $sSql = "select count(oxid) from oxobject2delivery where oxid in ('_testDelivery1', '_testDelivery2')";
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
        $oView->removeArtFromDel();
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::removeArtFromDel() test case
     */
    public function testRemoveArtFromDelAll()
    {
        $sOxid = '_testDelieveryRemoveAll';
        $this->setRequestParameter("oxid", $sOxid);
        $this->setRequestParameter("all", true);

        $sSql = "select count(oxobject2delivery.oxid) from oxobject2delivery left join " . $this->getArticlesViewTable() . " on " . $this->getArticlesViewTable() . ".oxid=oxobject2delivery.oxobjectid where oxobject2delivery.oxdeliveryid = '_testDelieveryRemoveAll' and oxobject2delivery.oxtype = 'oxarticles'";
        $oView = oxNew('delivery_articles_ajax');
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
        $oView->removeArtFromDel();
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::addArtToDel() test case
     */
    public function testAddArtToDel()
    {
        $sSynchoxid = '_testActionAddArt';
        $this->setRequestParameter("synchoxid", $sSynchoxid);

        $sSql = sprintf("select count(oxid) from oxobject2delivery where oxdeliveryid='%s'", $sSynchoxid);
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryArticlesAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testActionAdd1', '_testActionAdd2']);

        $oView->addArtToDel();
        $this->assertSame(2, oxDb::getDb()->getOne($sSql));
    }

    /**
     * DeliveryArticlesAjax::addArtToDel() test case
     */
    public function testAddArtToDelAll()
    {
        $sSynchoxid = '_testActionAddArtAll';
        $this->setRequestParameter("synchoxid", $sSynchoxid);
        $this->setRequestParameter("all", true);

        //count how much articles gets filtered
        $iCount = oxDb::getDb()->getOne("select count(" . $this->getArticlesViewTable() . ".oxid) from " . $this->getArticlesViewTable() . " where 1 and " . $this->getArticlesViewTable() . ".oxparentid = '' and " . $this->getArticlesViewTable() . ".oxid not in ( select oxobject2delivery.oxobjectid from oxobject2delivery where oxobject2delivery.oxdeliveryid = '" . $sSynchoxid . "' and oxobject2delivery.oxtype = 'oxarticles' )");

        $sSql = sprintf("select count(oxid) from oxobject2delivery where oxdeliveryid='%s'", $sSynchoxid);
        $this->assertSame(0, oxDb::getDb()->getOne($sSql));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\DeliveryArticlesAjax::class, ["getActionIds"]);
        $oView->method('getActionIds')->willReturn(['_testActionAdd1', '_testActionAdd2']);

        $oView->addArtToDel();
        $this->assertEquals($iCount, oxDb::getDb()->getOne($sSql));
    }
}
