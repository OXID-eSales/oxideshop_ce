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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(".") . '/unit/OxidTestCase.php';
require_once realpath(".") . '/unit/test_config.inc.php';

/**
 * Testing oxArticle class.
 */
class Unit_Core_oxShopMapperTest extends OxidTestCase
{

    /**
     * Tests add object to shop.
     */
    public function testAddObjectToShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $sShopId   = 45;

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('addItemToShop'));
        $oShopMapper->expects($this->once())->method('addItemToShop')->with($iItemId, $sItemType, $sShopId)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->addObjectToShop($oItem, $sShopId));
    }

    /**
     * Tests remove object from shop.
     */
    public function testRemoveObjectFromShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $sShopId   = 45;

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('removeItemFromShop'));
        $oShopMapper->expects($this->once())->method('removeItemFromShop')->with($iItemId, $sItemType, $sShopId)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->removeObjectFromShop($oItem, $sShopId));
    }

    /**
     * Tests add item to shop.
     */
    public function testAddItemToShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $sShopId   = 45;

        $oShopMapper = new oxShopMapper();

        $this->assertTrue($oShopMapper->addItemToShop($iItemId, $sItemType, $sShopId));
    }

    /**
     * Tests remove item from shop.
     */
    public function testRemoveItemFromShop()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $sShopId   = 45;

        $oShopMapper = new oxShopMapper();

        $this->assertTrue($oShopMapper->removeItemFromShop($iItemId, $sItemType, $sShopId));
    }

    /**
     * Tests add object to list of shops.
     */
    public function testAddObjectToListOfShops()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $aShops    = array(4, 5, 6);

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('addItemToListOfShops'));
        $oShopMapper->expects($this->once())->method('addItemToListOfShops')->with($iItemId, $sItemType, $aShops)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->addObjectToListOfShops($oItem, $aShops));
    }

    /**
     * Tests remove object from list of shops.
     */
    public function testRemoveObjectFromListOfShops()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $aShops    = array(4, 5, 6);

        $oItem = new oxBase();
        $oItem->init($sItemType);
        $oItem->setId($iItemId);

        /** @var oxShopMapper|PHPUnit_Framework_MockObject_MockObject $oShopMapper */
        $oShopMapper = $this->getMock('oxShopMapper', array('removeItemFromListOfShops'));
        $oShopMapper->expects($this->once())->method('removeItemFromListOfShops')->with($iItemId, $sItemType, $aShops)->will($this->returnValue(true));

        $this->assertTrue($oShopMapper->removeObjectFromListOfShops($oItem, $aShops));
    }

    /**
     * Tests add item to list of shops.
     */
    public function testAddItemToListOfShops()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $aShops    = array(4, 5, 6);

        $oShopMapper = new oxShopMapper();

        $this->assertTrue($oShopMapper->addItemToListOfShops($iItemId, $sItemType, $aShops));
    }

    /**
     * Tests remove item from list of shops.
     */
    public function testRemoveItemFromListOfShops()
    {
        $iItemId   = 123;
        $sItemType = 'oxarticles';
        $aShops    = array(4, 5, 6);

        $oShopMapper = new oxShopMapper();

        $this->assertTrue($oShopMapper->removeItemFromListOfShops($iItemId, $sItemType, $aShops));
    }
}
