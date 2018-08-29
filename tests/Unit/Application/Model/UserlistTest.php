<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxField;

/**
 * Testing oxuserlist class
 */
class UserlistTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $oUser = oxNew('oxuser');
        $oUser->setId('user1');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('user1', oxField::T_RAW);
        $oUser->save();


        $oUser = oxNew('oxuser');
        $oUser->setId('user2');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(2, oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('user2', oxField::T_RAW);
        $oUser->save();

        $oBasket = oxNew('OxUserBasket');
        $oBasket->setId("testUserBasket");
        $oBasket->oxuserbaskets__oxuserid = new oxField('user2', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxtitle = new oxField('wishlist', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        $oBasket->save();

        $oBasketItem = oxNew('OxUserBasketItem');
        $oBasketItem->setId("testUserBasketItem");
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField('testUserBasket', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField(1, oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('test', oxField::T_RAW);
        $oBasketItem->save();

        $oBasket = oxNew('OxUserBasket');
        $oBasket->setId("testUserBasket2");
        $oBasket->oxuserbaskets__oxuserid = new oxField('user1', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxtitle = new oxField('wishlist', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        $oBasket->save();

        $oBasketItem = oxNew('OxUserBasketItem');
        $oBasketItem->setId("testUserBasketItem2");
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField('testUserBasket2', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField(1, oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('test', oxField::T_RAW);
        $oBasketItem->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    public function tearDown()
    {
        $oUser = oxNew('oxuser');
        $oUser->delete('user1');
        $oUser->delete('user2');
        $oUserBasket = oxNew('oxUserBasket');
        $oUserBasket->delete("testUserBasket");
        $oUserBasket->delete("testUserBasket2");
        $oUserBasket = oxNew('OxUserBasketItem');
        $oUserBasket->delete("testUserBasketItem");
        $oUserBasket->delete("testUserBasketItem2");

        parent::tearDown();
    }

    public function testUserListLoadingDisabledShopcheck()
    {
        $iUserCount = '3';
        if ($this->getConfig()->getEdition() === 'EE') {
            $iUserCount = '8';
        }

        $oUser = oxNew('oxuser');
        $oUserList = oxNew('oxuserlist');
        $oUserList->selectString($oUser->buildSelectString());

        $this->assertEquals($iUserCount, $oUserList->count());
    }

    public function testLoadWishlistUsersExactUser()
    {
        // selecting count from DB
        $oUserList = oxNew('oxuserlist');
        $oUserList->loadWishlistUsers('user2');
        $this->assertEquals(1, $oUserList->count());
    }

    public function testLoadWishlistUsers()
    {
        // selecting count from DB
        $oUserList = oxNew('oxuserlist');
        $oUserList->loadWishlistUsers('user');
        $this->assertEquals(2, $oUserList->count());
    }

    public function testLoadWishlistUsersEmptySearch()
    {
        // selecting count from DB
        $oUserList = oxNew('oxuserlist');
        $oUserList->loadWishlistUsers(null);
        $this->assertEquals(0, $oUserList->count());
    }
}
