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
 * This script clears shop cache
 */
class SubShopHandler implements ShopServiceInterface
{
    /**
     * Assigns element to subshop
     */
    public function init()
    {
        $oxConfig = oxRegistry::getConfig();
        $sElementTable = $oxConfig->getRequestParameter("elementtable");
        $sShopId = $oxConfig->getRequestParameter("shopid");
        $sParentShopId = $oxConfig->getRequestParameter("parentshopid");
        $sElementId = $oxConfig->getRequestParameter("elementid");
        if ( $sElementId ) {
            $this->assignElementToSubShop($sElementTable, $sShopId, $sElementId);
        } else {
            $this->assignAllElementsToSubShop($sElementTable, $sShopId, $sParentShopId);
        }
    }

    /**
     * Assigns element to subshop
     *
     * @param string  $sElementTable Name of element table
     * @param integer $sShopId       Subshop id
     * @param integer $sElementId    Element id
     *
     * @return null
     */
    public function assignElementToSubShop($sElementTable, $sShopId, $sElementId)
    {
        $oBase = new oxBase();
        $oBase->init($sElementTable);
        if ( $oBase->load($sElementId) ) {
            $oElement2ShopRelations = new oxElement2ShopRelations($sElementTable);
            $oElement2ShopRelations->setShopIds($sShopId);
            $oElement2ShopRelations->addToShop($oBase->getId());
        }
    }

    /**
     * Assigns element to subshop
     *
     * @param string  $sElementTable Name of element table
     * @param integer $sShopId       Subshop id
     * @param integer $sParentShopId Parent subshop id
     *
     * @return null
     */
    public function assignAllElementsToSubShop($sElementTable, $sShopId, $sParentShopId = 1)
    {
        $oElement2ShopRelations = new oxElement2ShopRelations($sElementTable);
        $oElement2ShopRelations->setShopIds($sShopId);
        $oElement2ShopRelations->inheritFromShop($sParentShopId);
    }


}


