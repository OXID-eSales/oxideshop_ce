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

class oxShopMapper
{

    /**
     * Database gateway.
     *
     * @var oxShopMapperDbGateway
     */
    protected $_oDbGateway = null;

    /**
     * Sets database gateway.
     *
     * @param oxShopMapperDbGateway $oDb Database gateway.
     */
    public function setDbGateway($oDb)
    {
        $this->_oDbGateway = $oDb;
    }

    /**
     * Gets database gateway.
     *
     * @return oxShopMapperDbGateway
     */
    public function getDbGateway()
    {
        if (is_null($this->_oDbGateway)) {
            $oShopMapperDbGateway = oxNew('oxShopMapperDbGateway');
            $this->setDbGateway($oShopMapperDbGateway);
        }

        return $this->_oDbGateway;
    }

    /**
     * Adds object to shop.
     *
     * @param oxBase $oItem   Object to add to shop
     * @param int    $iShopId Shop id
     *
     * @return bool
     */
    public function addObjectToShop(oxBase $oItem, $iShopId)
    {
        return $this->addItemToShop($oItem->getId(), $oItem->getCoreTableName(), $iShopId);
    }

    /**
     * Removes object from shop.
     *
     * @param oxBase $oItem   Object to remove from shop
     * @param int    $iShopId Shop id
     *
     * @return bool
     */
    public function removeObjectFromShop(oxBase $oItem, $iShopId)
    {
        return $this->removeItemFromShop($oItem->getId(), $oItem->getCoreTableName(), $iShopId);
    }

    /**
     * Adds item to shop.
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop id
     *
     * @return bool
     */
    public function addItemToShop($iItemId, $sItemType, $iShopId)
    {
        return true;
    }

    /**
     * Removes item from shop.
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop id
     *
     * @return bool
     */
    public function removeItemFromShop($iItemId, $sItemType, $iShopId)
    {
        return true;
    }

    /**
     * Adds object to list of shops.
     *
     * @param oxBase $oItem  Object to add to shops
     * @param array  $aShops Shops
     *
     * @return bool
     */
    public function addObjectToListOfShops(oxBase $oItem, $aShops)
    {
        return $this->addItemToListOfShops($oItem->getId(), $oItem->getCoreTableName(), $aShops);
    }

    /**
     * Removes object from list of shops.
     *
     * @param oxBase $oItem  Object to remove from shops
     * @param array  $aShops Shops
     *
     * @return bool
     */
    public function removeObjectFromListOfShops(oxBase $oItem, $aShops)
    {
        return $this->removeItemFromListOfShops($oItem->getId(), $oItem->getCoreTableName(), $aShops);
    }

    /**
     * Adds item to list of shops.
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param array  $aShops    Shops
     *
     * @return bool
     */
    public function addItemToListOfShops($iItemId, $sItemType, $aShops)
    {
        return true;
    }

    /**
     * Removes item from list of shops
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param array  $aShops    Shops
     *
     * @return bool
     */
    public function removeItemFromListOfShops($iItemId, $sItemType, $aShops)
    {
        return true;
    }

    /**
     * Adds item group to shop.
     *
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop id
     *
     * @return bool
     */
    public function addItemGroupToShop($sItemType, $iShopId)
    {
        return true;
    }

    /**
     * Removes item group from shop.
     *
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop id
     *
     * @return bool
     */
    public function removeItemGroupFromShop($sItemType, $iShopId)
    {
        return true;
    }
}
