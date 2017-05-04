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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Testing oxuserlist class
 */
class Unit_Core_oxuserlistTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $oUser = new oxuser();
        $oUser->setId('user1');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(oxRegistry::getConfig()->getBaseShopId(), oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('user1', oxField::T_RAW);
        $oUser->save();


        $oUser = new oxuser();
        $oUser->setId('user2');
        $oUser->oxuser__oxactive = new oxField(1, oxField::T_RAW);
        $oUser->oxuser__oxshopid = new oxField(2, oxField::T_RAW);
        $oUser->oxuser__oxusername = new oxField('user2', oxField::T_RAW);
        $oUser->save();

        $oBasket = new OxUserBasket();
        $oBasket->setId("testUserBasket");
        $oBasket->oxuserbaskets__oxuserid = new oxField('user2', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxtitle = new oxField('wishlist', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        $oBasket->save();

        $oBasketItem = new OxUserBasketItem();
        $oBasketItem->setId("testUserBasketItem");
        $oBasketItem->oxuserbasketitems__oxbasketid = new oxField('testUserBasket', oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxamount = new oxField(1, oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('test', oxField::T_RAW);
        $oBasketItem->save();

        $oBasket = new OxUserBasket();
        $oBasket->setId("testUserBasket2");
        $oBasket->oxuserbaskets__oxuserid = new oxField('user1', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxtitle = new oxField('wishlist', oxField::T_RAW);
        $oBasket->oxuserbaskets__oxpublic = new oxField(1, oxField::T_RAW);
        $oBasket->save();

        $oBasketItem = new OxUserBasketItem();
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
        $oUser = new oxuser();
        $oUser->delete('user1');
        $oUser->delete('user2');
        $oUserBasket = new oxUserBasket();
        $oUserBasket->delete("testUserBasket");
        $oUserBasket->delete("testUserBasket2");
        $oUserBasket = new OxUserBasketItem();
        $oUserBasket->delete("testUserBasketItem");
        $oUserBasket->delete("testUserBasketItem2");

        parent::tearDown();
    }

    /**
     * Checking if object is loaded and if type is valid
     */
    public function testUserListLoadingEnabledShopCheck()
    {
        // selecting count from DB

        $sQ = 'select count(*) from oxuser';

        $iUserCount = oxDB::getDB()->getOne($sQ);

        $oUser = new oxuser();
        $oUserList = oxNew('oxuserlist');
        $oUserList->selectString($oUser->buildSelectString());

        $this->assertEquals($iUserCount, $oUserList->count());
    }

    public function testUserListLoadingDisabledShopcheck()
    {
        // selecting count from DB
        $sQ = 'select count(*) from oxuser';
        $iUserCount = oxDB::getDB()->getOne($sQ);

        $oUser = new oxuser();
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
