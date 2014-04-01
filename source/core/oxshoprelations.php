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

class oxShopRelations
{

    /**
     * Database gateway.
     *
     * @var oxShopRelationsDbGateway
     */
    protected $_oDbGateway = null;

    /**
     * Sets database gateway.
     *
     * @param oxShopRelationsDbGateway $oDb Database gateway.
     */
    public function setDbGateway($oDb)
    {
        $this->_oDbGateway = $oDb;
    }

    /**
     * Gets database gateway.
     *
     * @return oxShopRelationsDbGateway
     */
    public function getDbGateway()
    {
        if (is_null($this->_oDbGateway)) {
            $oShopRelationsDbGateway = oxNew('oxShopRelationsDbGateway');
            $this->setDbGateway($oShopRelationsDbGateway);
        }

        return $this->_oDbGateway;
    }

    /**
     * Adds object to shop or list of shops.
     *
     * @param oxBase    $oItem    Object to add to shop
     * @param int|array $aShopIds Shop ID or list of shop IDs.
     *
     * @return bool
     */
    public function addObjectToShops(oxBase $oItem, $aShopIds)
    {
        return $this->addItemToShops($oItem->getId(), $oItem->getCoreTableName(), $aShopIds);
    }

    /**
     * Removes object from shop or list of shops.
     *
     * @param oxBase    $oItem    Object to remove from shop
     * @param int|array $aShopIds Shop ID or list of shop IDs.
     *
     * @return bool
     */
    public function removeObjectFromShops(oxBase $oItem, $aShopIds)
    {
        return $this->removeItemFromShops($oItem->getId(), $oItem->getCoreTableName(), $aShopIds);
    }

    /**
     * Adds item to shop or list of shops.
     *
     * @param int       $iItemId   Item ID
     * @param string    $sItemType Item type
     * @param int|array $aShopIds  Shop ID or list of shop IDs.
     *
     * @return bool
     */
    public function addItemToShops($iItemId, $sItemType, $aShopIds)
    {
        if (!is_array($aShopIds)) {
            $aShopIds = array($aShopIds);
        }

        foreach ($aShopIds as $iShopId) {
            $this->getDbGateway()->addItemToShop($iItemId, $sItemType, $iShopId);
        }

        return true;
    }

    /**
     * Removes item from shop or list of shops.
     *
     * @param int       $iItemId   Item ID
     * @param string    $sItemType Item type
     * @param int|array $aShopIds  Shop ID or list of shop IDs.
     *
     * @return bool
     */
    public function removeItemFromShops($iItemId, $sItemType, $aShopIds)
    {
        if (!is_array($aShopIds)) {
            $aShopIds = array($aShopIds);
        }

        foreach ($aShopIds as $iShopId) {
            $this->getDbGateway()->removeItemFromShop($iItemId, $sItemType, $iShopId);
        }

        return true;
    }

    /**
     * Inherits items by type to sub shop(-s) from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param int    $aSubShops     Sub shop ID or list of IDs to inherit into
     * @param string $sItemType     Item type
     *
     * @return bool
     */
    public function inheritItemsFromShops($iParentShopId, $aSubShops, $sItemType)
    {
        if (!is_array($aSubShops)) {
            $aSubShops = array($aSubShops);
        }

        foreach ($aSubShops as $iSubShopId) {
            $this->getDbGateway()->inheritItemsFromShop($iParentShopId, $iSubShopId, $sItemType);
        }

        return true;
    }

    /**
     * Removes items by type from sub shop(-s) that were inherited from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param int    $aSubShops     Sub shop ID or list of IDs to remove inheritance
     * @param string $sItemType     Item type
     *
     * @return bool
     */
    public function removeInheritedItemsFromShops($iParentShopId, $aSubShops, $sItemType)
    {
        if (!is_array($aSubShops)) {
            $aSubShops = array($aSubShops);
        }

        foreach ($aSubShops as $iSubShopId) {
            $this->getDbGateway()->removeInheritedItemsFromShop($iParentShopId, $iSubShopId, $sItemType);
        }

        return true;
    }
}
