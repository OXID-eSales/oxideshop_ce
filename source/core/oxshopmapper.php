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
     * Adds object to shop or list of shops.
     *
     * @param oxBase    $oItem  Object to add to shop
     * @param int|array $mShops Shop id or list of shop ids.
     *
     * @return bool
     */
    public function addObjectToShops(oxBase $oItem, $mShops)
    {
        return $this->addItemToShops($oItem->getId(), $oItem->getCoreTableName(), $mShops);
    }

    /**
     * Removes object from shop or list of shops.
     *
     * @param oxBase    $oItem  Object to remove from shop
     * @param int|array $mShops Shop id or list of shop ids.
     *
     * @return bool
     */
    public function removeObjectFromShops(oxBase $oItem, $mShops)
    {
        return $this->removeItemFromShops($oItem->getId(), $oItem->getCoreTableName(), $mShops);
    }

    /**
     * Adds item to shop or list of shops.
     *
     * @param int       $iItemId   Item ID
     * @param string    $sItemType Item type
     * @param int|array $aShops    Shop id or list of shop ids.
     *
     * @return bool
     */
    public function addItemToShops($iItemId, $sItemType, $aShops)
    {
        if (!is_array($aShops)) {
            $aShops = array($aShops);
        }

        foreach ($aShops as $iShopId) {
            $this->getDbGateway()->addItemToShop($iItemId, $sItemType, $iShopId);
        }

        return true;
    }

    /**
     * Removes item from shop or list of shops.
     *
     * @param int       $iItemId   Item ID
     * @param string    $sItemType Item type
     * @param int|array $aShops    Shop id or list of shop ids.
     *
     * @return bool
     */
    public function removeItemFromShops($iItemId, $sItemType, $aShops)
    {
        if (!is_array($aShops)) {
            $aShops = array($aShops);
        }

        foreach ($aShops as $iShopId) {
            $this->getDbGateway()->removeItemFromShop($iItemId, $sItemType, $iShopId);
        }

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
