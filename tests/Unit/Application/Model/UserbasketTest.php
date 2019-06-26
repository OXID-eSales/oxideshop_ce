<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;
use \oxDb;
use \oxRegistry;
use \oxTestModules;

class UserbasketTest extends \OxidTestCase
{
    private $_oUserBasket;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oBasket = oxNew('OxUserBasket');
        $oBasket->setId("testUserBasket");
        $oBasket->save();

        $oBasketItem = oxNew('oxUserBasketItem');
        $oBasketItem->setId('testitem');
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField($oBasket->getId(), oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('2000', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField('5', oxField::T_RAW);
        $oBasketItem->save();

        $oSel = oxNew('oxbase');
        $oSel->init('oxselectlist');
        $oSel->setId('xxx');
        $oSel->oxselectlist__oxvaldesc = new oxField('S, 10!P!10__@@M, 20!P!20__@@L, 30!P!30__@@', oxField::T_RAW);
        $oSel->save();

        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init('oxobject2selectlist');
        $oO2Sel->setId('xxx');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxField('2000', oxField::T_RAW);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxField('xxx', oxField::T_RAW);
        $oO2Sel->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuserbaskets');

        $oUserBasket = oxNew('oxUserBasket');
        $oUserBasket->delete("testUserBasket");
        $oUserBasket->delete("testUserBasket2");

        $oUserBasketItem = oxNew('oxUserBasketItem');
        $oUserBasketItem->delete("testitem");

        $oSel = oxNew('oxbase');
        $oSel->init('oxselectlist');
        $oSel->delete('xxx');

        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init('oxobject2selectlist');
        $oO2Sel->delete('xxx');

        parent::tearDown();
    }

    /**
     * Testing use case:
     *
     * Previously gift registry was public by default, now it is not. Bug or feature?
     */
    public function testIfNewlyCreatedBasketIsByDefaultPublic()
    {
        // deleting for lighter teardown
        $oUserBasket = oxNew('oxUserBasket');
        $oUserBasket->delete("testUserBasket");

        //
        $oNewBasket = oxNew('oxUserBasket');
        $oNewBasket->setId("testUserBasket");
        $oNewBasket->save();

        $oLoadedBasket = oxNew('oxUserBasket');
        $oLoadedBasket->load("testUserBasket");
        $this->assertEquals(1, $oLoadedBasket->oxuserbaskets__oxpublic->value);
    }

    /**
     * Testing use case:
     *
     * Make gift registry public. Remove all products from gift registry. Then add some
     * new ones to it. Gift registry is marked as not public. (consequence of bug #196)
     */
    public function testRemovedLastBasketItemTestingIfStatusIsKept()
    {
        // deleting for lighter teardown
        $oBasket = oxNew('OxUserBasket');
        $oBasket->load("testUserBasket");
        $oBasket->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        $oBasket->addItemToBasket('2000', 0, null, true);

        $oOldBasket = oxNew('OxUserBasket');
        $oOldBasket->load("testUserBasket");
        $this->assertEquals(1, $oOldBasket->oxuserbaskets__oxpublic->value);
        $this->assertEquals(0, $oOldBasket->getItemCount());
    }

    /**
     * Testing new userbasket status getter, setter
     */
    public function testInsertIsNewBasket()
    {
        $oBasket = $this->getProxyClass('oxUserBasket');
        $this->assertFalse($oBasket->getNonPublicVar('_blNewBasket'));

        $oBasket->setIsNewBasket();
        $this->assertTrue($oBasket->getNonPublicVar('_blNewBasket'));
    }

    /**
     * Testing if after user basket create oxcreate field contains date
     * of creation (M:1710)
     */
    public function testInsert_creationTime()
    {
        $iTime = 999991;

        oxAddClassModule('modOxUtilsDate', 'oxUtilsDate');
        \OxidEsales\Eshop\Core\Registry::getUtilsDate()->UNITSetTime($iTime);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->setId("_testUserBasketId");
        $oBasket->save();
        $this->assertEquals($iTime, $oBasket->oxuserbaskets__oxupdate->value);
    }

    /**
     * Getting articles of non loaded basket
     */
    public function testGetArticlesNoArticles()
    {
        $oBasket = oxNew('oxUserBasket');
        $this->assertEquals(0, count($oBasket->getArticles()));
    }

    public function testGetArticlesOneArticle()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $this->assertEquals(1, count($oBasket->getArticles()));
    }

    /**
     * Testin basket item getter
     */
    //  simple getter, empty select list info
    public function testGetItems()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $aItems = $oBasket->getItems();

        $this->assertEquals(1, count($aItems));

        $oItem = current($aItems);
        $oArticle = $oItem->getArticle('xxx');

        $this->assertEquals('testitem', $oItem->getId());
        $this->assertEquals('2000', $oArticle->getId());
        $this->assertNull($oItem->getSelList());
        $this->assertNull($oItem->getPersParams());
    }

    public function testGetItemsWithActiveArticleCheck()
    {
        $oA = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getSqlActiveSnippet'));
        $oA->expects($this->once())->method('getSqlActiveSnippet')->will($this->returnValue('1'));

        oxTestModules::addModuleObject('oxarticle', $oA);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $aItems = $oBasket->getItems();
    }

    public function testGetItemsWithoutActiveArticleCheck()
    {
        $oA = $this->getMock(\OxidEsales\Eshop\Application\Model\Article::class, array('getSqlActiveSnippet'));
        $oA->expects($this->never())->method('getSqlActiveSnippet');

        oxTestModules::addModuleObject('oxarticle', $oA);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $aItems = $oBasket->getItems(true, false);
    }

    public function testGetItemsCached()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $aItems = $oBasket->getItems();
        $oItem = current($aItems);
        $oArticle = $oItem->getArticle('xxx');
        $this->assertEquals('2000', $oArticle->getId());

        $sQ = "update oxuserbasketitems set oxartid='test' where oxbasketid = 'testUserBasket' ";
        oxDb::getDb()->execute($sQ);
        $aItems = $oBasket->getItems();
        $oItem = current($aItems);
        $oArticle = $oItem->getArticle('xxx');
        $this->assertEquals('2000', $oArticle->getId());
    }

    /**
     * Verify that the basket was completely deleted.
     */
    public function testDontGetCachedItemsAfterDelete()
    {
        $basket = oxNew('oxUserBasket');
        $basket->load("testUserBasket");
        $items = $basket->getItems();
        $item = current($items);
        $article = $item->getArticle('xxx');
        $this->assertEquals('2000', $article->getId());

        $basket->delete();
        $this->assertEquals(0, count($basket->getItems()));
    }

    public function testGetItemsReload()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $aItems = $oBasket->getItems();
        $oItem = current($aItems);
        $oArticle = $oItem->getArticle('xxx');
        $this->assertEquals('2000', $oArticle->getId());

        $sQ = "update oxuserbasketitems set oxartid='1126' where oxbasketid = 'testUserBasket' ";
        oxDb::getDb()->execute($sQ);
        $aItems = $oBasket->getItems(true);
        $oItem = current($aItems);
        $oArticle = $oItem->getArticle('xxx');
        $this->assertEquals('1126', $oArticle->getId());
    }

    public function testCreateItem()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $sArtId = "2000";

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $oItem = $oBasket->UNITcreateItem($sArtId, null);

        $this->assertEquals($sArtId, $oItem->oxuserbasketitems__oxartid->value);
        $this->assertEquals("testUserBasket", $oItem->oxuserbasketitems__oxbasketid->value);
        $this->assertEquals(serialize(array('0')), $oItem->oxuserbasketitems__oxsellist->value);
        $this->assertEquals(array('0'), $oItem->getSelList());
    }

    public function testCreateItemWithSellist()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $sArtId = "2000";

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $oItem = $oBasket->UNITcreateItem($sArtId, array(0 => '1'));

        $this->assertEquals($sArtId, $oItem->oxuserbasketitems__oxartid->value);
        $this->assertEquals("testUserBasket", $oItem->oxuserbasketitems__oxbasketid->value);
        $this->assertEquals(serialize(array('1')), $oItem->oxuserbasketitems__oxsellist->value);
        $this->assertEquals(array('1'), $oItem->getSelList());
    }

    public function testCreateItemWithPersParam()
    {
        $sArtId = "2000";

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $oItem = $oBasket->UNITcreateItem($sArtId, null, array('param' => 'test'));

        $this->assertEquals($sArtId, $oItem->oxuserbasketitems__oxartid->value);
        $this->assertEquals("testUserBasket", $oItem->oxuserbasketitems__oxbasketid->value);
        $this->assertEquals(serialize(array('param' => 'test')), $oItem->oxuserbasketitems__oxpersparam->value);
        $this->assertEquals(array('param' => 'test'), $oItem->getPersParams());
    }

    /**
     * Testing basket item getter
     */
    // passing article id, which will be used to create item key to fetch item from an array
    public function testGetItem()
    {
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");

        $this->assertEquals("testUserBasket", $oBasket->getItem("2000", null)->oxuserbasketitems__oxbasketid->value);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $this->assertEquals("testUserBasket", $oBasket->getItem(md5("2000" . '|' . serialize(array())), null)->oxuserbasketitems__oxbasketid->value);

        $oBasket = oxNew('oxUserBasket');
        $this->assertEquals("2000", $oBasket->getItem("2000", null)->oxuserbasketitems__oxartid->value);
    }

    public function testGetItemByProductId()
    {
        $oItems = array('123' => '321');

        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, array('getItems', '_getItemKey'));
        $oBasket->expects($this->once())->method('getItems')->will($this->returnValue($oItems));
        $oBasket->expects($this->once())->method('_getItemKey')->with($this->equalTo('123'), $this->equalTo('testsellist'), $this->equalTo('testparam'));

        $oItem = $oBasket->getItem('123', 'testsellist', 'testparam');

        $this->assertEquals('321', $oItem);
    }

    /**
     * Testing item key generator
     */
    public function testGetItemKey()
    {
        $oBasket = oxNew('oxUserBasket');

        $this->assertEquals(md5("123" . '|' . serialize(array(0 => '0')) . '|' . serialize(null)), $oBasket->UNITgetItemKey("123"));
        $this->assertEquals(md5("123" . '|' . serialize(array("b")) . '|' . serialize('xxx')), $oBasket->UNITgetItemKey("123", array("b"), 'xxx'));
    }

    public function testGetItemCount()
    {
        $oBasket = oxNew('oxUserBasket');
        $this->assertEquals(0, $oBasket->getItemCount());

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $this->assertEquals(1, $oBasket->getItemCount());
    }

    /**
     * Testing item basket addition/substraction
     */
    public function testAddItemToBasket()
    {
        $sArtId = "2000";
        $dAmount = 3;
        $aSel = array("A");
        $aParam = array("B");

        $this->setTime(99999);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $oBasket->setIsNewBasket();

        $this->assertNull($oBasket->addItemToBasket());
        $this->assertEquals(3, $oBasket->addItemToBasket($sArtId, $dAmount, $aSel, false, $aParam));
        $this->assertEquals(6, $oBasket->addItemToBasket($sArtId, $dAmount, $aSel, false, $aParam));

        $this->assertEquals(0, $oBasket->addItemToBasket($sArtId, 0, $aSel, true));
        $this->assertEquals(0, $oBasket->addItemToBasket($sArtId, 0, null, true));

        $this->assertEquals(99999, $oBasket->oxuserbaskets__oxupdate->value);
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $this->assertEquals(99999, $oBasket->oxuserbaskets__oxupdate->value);

        // basket is not removed any more after it is emptied, because on deletion we will loose
        // its visibility status
        $this->assertEquals("testUserBasket", $oBasket->getId());
    }

    public function testAddItemToBasketNoticeList()
    {
        $sArtId = "2000";
        $dAmount = 3;

        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $oBasket->setIsNewBasket();

        $this->assertEquals(3, $oBasket->addItemToBasket($sArtId, $dAmount, null, true));
        $this->assertEquals(3, $oBasket->addItemToBasket($sArtId, $dAmount, null, true));
    }

    /**
     * Testing how fine deletion works :)
     */
    public function testDelete()
    {
        $sQ = "select 1 from oxuserbaskets where oxid = 'testUserBasket' ";
        $this->assertEquals(1, oxDb::getDb()->getOne($sQ));
        $sQ = "select 1 from oxuserbasketitems where oxbasketid = 'testUserBasket' ";
        $this->assertEquals(1, oxDb::getDb()->getOne($sQ));
        $oBasket = oxNew('oxUserBasket');
        $this->assertTrue($oBasket->delete("testUserBasket"));

        $sQ = "select 1 from oxuserbaskets where oxid = 'testUserBasket' ";
        $this->assertFalse(oxDb::getDb()->getOne($sQ));

        $sQ = "select 1 from oxuserbasketitems where oxbasketid = 'testUserBasket' ";
        $this->assertFalse(oxDb::getDb()->getOne($sQ));
    }

    public function testDeleteLoaded()
    {
        $sQ = "select 1 from oxuserbaskets where oxid = 'testUserBasket' ";
        $this->assertEquals(1, oxDb::getDb()->getOne($sQ));
        $sQ = "select 1 from oxuserbasketitems where oxbasketid = 'testUserBasket' ";
        $this->assertEquals(1, oxDb::getDb()->getOne($sQ));
        $oBasket = oxNew('oxUserBasket');
        $oBasket->load("testUserBasket");
        $this->assertTrue($oBasket->delete());

        $sQ = "select 1 from oxuserbaskets where oxid = 'testUserBasket' ";
        $this->assertFalse(oxDb::getDb()->getOne($sQ));

        $sQ = "select 1 from oxuserbasketitems where oxbasketid = 'testUserBasket' ";
        $this->assertFalse(oxDb::getDb()->getOne($sQ));
    }

    public function testDeleteBlank()
    {
        $oBasket = oxNew('oxUserBasket');
        $this->assertFalse($oBasket->delete());
    }

    public function testIsVisibleOtherUserActive()
    {
        $oSubj = oxNew('oxUserBasket');
        $oSubj->init();
        $oSubj->oxuserbaskets__oxpublic = new oxField(1);
        $this->assertTrue($oSubj->isVisible());
    }

    public function testIsVisibleOtherUserInactive()
    {
        $oSubj = oxNew('oxUserBasket');
        $oSubj->init();
        $oSubj->oxuserbaskets__oxpublic = new oxField(0);
        $this->assertFalse($oSubj->isVisible());
    }

    public function testIsVisibleSameUser()
    {
        $oSubj = oxNew('oxUserBasket');
        $oSubj->init();
        $oSubj->oxuserbaskets__oxpublic = new oxField(0);
        $oSubj->oxuserbaskets__oxuserid = new oxField('oxdefaultadmin');
        $oUser = oxNew('oxUser');
        $oUser->load('oxdefaultadmin');
        $oSubj->getConfig()->setUser($oUser);
        $this->assertTrue($oSubj->isVisible());
    }

    /**
     * Checking if newly created user basket is empty
     *
     * return null
     */
    public function testIsEmpty_newBasket()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, array('isNewBasket', 'getItemCount'));
        $oBasket->expects($this->once())->method('isNewBasket')->will($this->returnValue(true));
        $oBasket->expects($this->never())->method('getItemCount');

        $this->assertTrue($oBasket->isEmpty());
    }

    /**
     * Checking if user basket with items is not empty
     *
     * return null
     */
    public function testIsEmpty_hasItems()
    {
        $oBasket = $this->getMock(\OxidEsales\Eshop\Application\Model\UserBasket::class, array('isNewBasket', 'getItemCount'));
        $oBasket->expects($this->once())->method('isNewBasket')->will($this->returnValue(false));
        $oBasket->expects($this->once())->method('getItemCount')->will($this->returnValue(1));

        $this->assertFalse($oBasket->isEmpty());
    }


    /**
     * Checking if user basket with items is not empty
     *
     * return null
     */
    public function testSetIsNewBasket()
    {
        $this->setTime(3333);

        $oBasket = oxNew('oxUserBasket');
        $oBasket->setIsNewBasket();

        $this->assertTrue($oBasket->isNewBasket());
        $this->assertEquals(3333, $oBasket->oxuserbaskets__oxupdate->value);
    }
}
