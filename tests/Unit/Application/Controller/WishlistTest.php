<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxDb;
use \oxRegistry;

class WishlistTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        $this->tearDown();
        parent::setUp();

        $this->_oUser = oxNew('oxuser');
        $this->_oUser->setId('_testId');
        $this->_oUser->oxuser__oxusername = new oxField('testUserName', oxField::T_RAW);
        $this->_oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $this->_oUser->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxuserbaskets');
        $this->cleanUpTable('oxuserbasketitems');

        parent::tearDown();
    }

    /*
     * Checking getting wishlist user
     */
    public function testGetWishUser()
    {
        $this->setRequestParameter('wishid', '_testId');
        $oWishList = oxNew("Wishlist");

        $oWishUser = $oWishList->getWishUser();

        $this->assertEquals($this->_oUser->getId(), $oWishUser->getId());
        $this->assertEquals('_testId', oxRegistry::getSession()->getVariable('wishid'));
    }

    /*
     * Checking getting wishlist
     */
    public function testGetWishList()
    {
        $this->setRequestParameter('wishid', '_testId');
        $oWishList = oxNew("Wishlist");
        $myDB = oxDb::getDB();

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle, oxpublic ) values ( "_testBasketId1", "' . $this->_oUser->getId() . '", "wishlist", 1 ) ';
        $myDB->Execute($sQ);

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute($sQ);

        $oList = $oWishList->getWishList();
        $this->assertEquals(1, count($oList));
        $oArticle = array_pop($oList);
        $this->assertEquals('1126', $oArticle->getId());
    }


    /*
     * Checking getting wishlist
     */
    public function testGetWishListIactive()
    {
        $this->setRequestParameter('wishid', '_testId');
        $oWishList = oxNew("Wishlist");
        $myDB = oxDb::getDB();

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle, oxpublic ) values ( "_testBasketId1", "' . $this->_oUser->getId() . '", "wishlist", 0 ) ';
        $myDB->Execute($sQ);

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute($sQ);

        $oList = $oWishList->getWishList();
        $this->assertFalse($oList);
    }

    /*
     * Checking searching wishlist of another user
     */
    public function testSearchForWishList()
    {
        $this->setRequestParameter('search', 'testUserName');

        $oWishList = $this->getProxyClass("Wishlist");
        $myDB = oxDb::getDB(oxDB::FETCH_MODE_ASSOC);

        // adding article to basket
        $sQ = 'insert into oxuserbaskets ( oxid, oxuserid, oxtitle ) values ( "_testBasketId1", "' . $this->_oUser->getId() . '", "wishlist" ) ';
        $myDB->Execute($sQ);

        $sQ = 'insert into oxuserbasketitems ( oxid, oxbasketid, oxartid, oxamount ) values ( "_testId1", "_testBasketId1", "1126", "1" ) ';
        $myDB->Execute($sQ);

        $oWishList->searchForWishList();
        $oUsersList = $oWishList->getNonPublicVar('_oWishListUsers');
        $oUser = $oUsersList->current();
        $this->assertEquals('_testId', $oUser->getId());
        $this->assertEquals('testUserName', $oWishList->getNonPublicVar('_sSearchParam'));
    }

    /*
     * Checking getter of wishlist user
     */
    public function testGetWishListUsers()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $oWishList->setNonPublicVar('_oWishListUsers', 'testValue');
        $this->assertEquals('testValue', $oWishList->getWishListUsers());
    }

    /*
     * Checking getter of wishlist search parameter
     */
    public function testGetWishListSearchParam()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $oWishList->setNonPublicVar('_sSearchParam', 'testValue');
        $this->assertEquals('testValue', $oWishList->getWishListSearchParam());
    }

    /*
     * Testing render method
     */
    public function testRender()
    {
        $oWishList = $this->getProxyClass("Wishlist");

        $this->assertEquals('page/wishlist/wishlist.tpl', $oWishList->render());
    }

    /**
     * Testing Account_RecommList::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oWishList = oxNew('Wishlist');
        $aResults = array();
        $aResult = array();

        $aResult["title"] = oxRegistry::getLang()->translateString('PUBLIC_GIFT_REGISTRIES', oxRegistry::getLang()->getBaseLanguage(), false);
        $aResult["link"] = $oWishList->getLink();

        $aResults[] = $aResult;

        $this->assertEquals($aResults, $oWishList->getBreadCrumb());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithUser()
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxfname = new oxField('fName');
        $oUser->oxuser__oxlname = new oxField('lName');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WishListController::class, array('getWishUser'));
        $oView->expects($this->any())->method('getWishUser')->will($this->returnValue($oUser));

        $this->assertEquals(oxRegistry::getLang()->translateString('GIFT_REGISTRY_OF_3', oxRegistry::getLang()->getBaseLanguage(), false) . ' fName lName', $oView->getTitle());
    }

    /**
     * Test get title.
     */
    public function testGetTitleWithoutUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\WishListController::class, array('getWishUser'));
        $oView->expects($this->any())->method('getWishUser')->will($this->returnValue(null));

        $this->assertEquals(oxRegistry::getLang()->translateString('PUBLIC_GIFT_REGISTRIES', oxRegistry::getLang()->getBaseLanguage(), false), $oView->getTitle());
    }
}
