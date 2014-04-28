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

/**
 * Class oxCategory2ShopRelations
 */
class oxCategory2ShopRelations implements oxIShopRelations
{

    /**
     * Adds item to shop or list of shops.
     *
     * @param int    $iItemId   Item ID to be added to shop.
     * @param string $sItemType Item type.
     */
    public function addToShop($iItemId, $sItemType)
    {
        // TODO: Implement addToShop() method.
    }

    /**
     * Copies inheritance information from one object to another for specified type.
     *
     * @param int    $iSourceItemId      Source item id to copy inheritance from.
     * @param int    $iDestinationItemId Destination item id to copy inheritance for.
     * @param string $sItemType          Item type.
     */
    public function copyInheritance($iSourceItemId, $iDestinationItemId, $sItemType)
    {
        // TODO: Implement copyInheritance() method.
    }

    /**
     * Inherits items by type to sub shop(-s) from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param string $sItemType     Item type
     */
    public function inheritFromShop($iParentShopId, $sItemType)
    {
        // TODO: Implement inheritFromShop() method.
    }

    /**
     * Removes item from all shops. It will remove all relations of a single object from database table.
     * Usage example: removeFromAllShops('abc', 'oxarticles');
     * Will remove all relations data for given object.
     *
     * Shop id is not required for the oxshoprelations object in this case.
     * Use only with tables that can be inherited.
     *
     * @param int    $iItemId   Item ID to be removed.
     * @param string $sItemType Item type.
     */
    public function removeFromAllShops($iItemId, $sItemType)
    {
        // TODO: Implement removeFromAllShops() method.
    }

    /**
     * Removes item from shop or list of shops.
     *
     * @param int    $iItemId   Item ID to be removed.
     * @param string $sItemType Item type.
     */
    public function removeFromShop($iItemId, $sItemType)
    {
        // TODO: Implement removeFromShop() method.
    }

    /**
     * Removes items by type from sub shop(-s) that were inherited from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param string $sItemType     Item type
     */
    public function removeInheritedFromShop($iParentShopId, $sItemType)
    {
        // TODO: Implement removeInheritedFromShop() method.
    }

    /**
     * Checks if item is in one of the set shops.
     *
     * @param int    $iItemId   Item ID to check.
     * @param string $sItemType Item type
     *
     * @return bool
     */
    public function isInShop($iItemId, $sItemType)
    {
        // TODO: Implement isInShop() method.
    }

    /**
     * Executes stacked commands to add/remove item to shop.
     */
    public function execute()
    {
        // TODO: Implement execute() method.
    }

    /**
     * Sets shop ID or list of shop IDs.
     *
     * @param int|array $aShopIds Shop ID or list of shop IDs.
     */
    public function setShopIds($aShopIds)
    {
        // TODO: Implement setShopIds() method.
    }
}
