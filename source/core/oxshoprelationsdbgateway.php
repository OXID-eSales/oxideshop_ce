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

class oxShopRelationsDbGateway
{

    /**
     * Database class object.
     *
     * @var oxLegacyDb
     */
    protected $_oDb = null;

    /**
     * Sets database class object.
     *
     * @param oxLegacyDb $oDb Database gateway.
     */
    public function setDbGateway($oDb)
    {
        $this->_oDb = $oDb;
    }

    /**
     * Gets database class object.
     *
     * @return oxLegacyDb
     */
    public function getDbGateway()
    {
        if (is_null($this->_oDb)) {
            $this->setDbGateway(oxDb::getDb());
        }

        return $this->_oDb;
    }

    /**
     * Adds item to shop.
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop ID
     *
     * @return bool
     */
    public function addItemToShop($iItemId, $sItemType, $iShopId)
    {
        $sMappingTable = $this->getMappingTable($sItemType);

        $sSQL = "insert into $sMappingTable (OXMAPSHOPID, OXMAPOBJECTID) values (?, ?)";

        $blResult = (bool) $this->execute($sSQL, array($iShopId, $iItemId));

        return $blResult;
    }

    /**
     * Removes item from shop.
     *
     * @param int    $iItemId   Item ID
     * @param string $sItemType Item type
     * @param int    $iShopId   Shop ID
     *
     * @return bool
     */
    public function removeItemFromShop($iItemId, $sItemType, $iShopId)
    {
        $sMappingTable = $this->getMappingTable($sItemType);

        $sSQL = "delete from $sMappingTable where OXMAPSHOPID = ? and OXMAPOBJECTID = ?";

        $blResult = (bool) $this->execute($sSQL, array($iShopId, $iItemId));

        return $blResult;
    }

    /**
     * Executes database query.
     *
     * @param string     $sSQL    SQL query.
     * @param array|bool $aParams Array of parameters
     *
     * @return object
     */
    public function execute($sSQL, $aParams = false)
    {
        return $this->getDbGateway()->execute($sSQL, $aParams);
    }

    /**
     * Inherits items by type to sub shop from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param int    $iSubShopId    Sub shop ID
     * @param string $sItemType     Item type
     *
     * @return bool
     */
    public function inheritItemsFromShop($iParentShopId, $iSubShopId, $sItemType)
    {
        $sMappingTable = $this->getMappingTable($sItemType);

        $sSQL = "insert into $sMappingTable (OXMAPSHOPID, OXMAPOBJECTID) "
                . "select ?, OXMAPOBJECTID from $sMappingTable where OXMAPSHOPID = ?";

        $blResult = (bool) $this->execute($sSQL, array($iSubShopId, $iParentShopId));

        return $blResult;
    }

    /**
     * Removes items by type from sub shop that were inherited from parent shop.
     *
     * @param int    $iParentShopId Parent shop ID
     * @param int    $iSubShopId    Sub shop ID
     * @param string $sItemType     Item type
     *
     * @return bool
     */
    public function removeInheritedItemsFromShop($iParentShopId, $iSubShopId, $sItemType)
    {
        $sMappingTable = $this->getMappingTable($sItemType);

        $sSQL = "delete s from $sMappingTable as s "
                . "left join $sMappingTable as p on (s.OXMAPOBJECTID = p.OXMAPOBJECTID)"
                . "where s.OXMAPSHOPID = ? "
                . "and p.OXMAPSHOPID = ?";

        $blResult = (bool) $this->execute($sSQL, array($iSubShopId, $iParentShopId));

        return $blResult;
    }

    /**
     * Gets mapping table of item type.
     *
     * @param string $sItemType Item type.
     *
     * @return string
     */
    protected function getMappingTable($sItemType)
    {
        return $sItemType . '2shop';
    }
}
