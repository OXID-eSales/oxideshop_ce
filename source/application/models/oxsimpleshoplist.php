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
 * Simple shop list
 * Organizes list of shop objects.
 */
class oxSimpleShopList
{

    /**
     * Base query for getting all shops
     *
     * @var string
     */
    protected $_sBaseQuery = 'SELECT `OXID`, `OXNAME` FROM `oxshops`';

    /**
     * Loads only necesarry list data into a simple object.
     * Takes only OXID and OXNAME from retrieved table data.
     *
     * @param string $sWhere WHERE statement for the shop selection query. Ex.: 'oxactive = 1'.
     *
     * @return array
     */
    public function getList($sWhere = null)
    {
        $sSql = $this->_sBaseQuery;
        if (!empty($sWhere)) {
            $sSql .= " WHERE $sWhere";
        }

        $aShopList = array();
        $aResults = oxDb::getDb(oxDb::FETCH_MODE_ASSOC)->getAll($sSql);
        foreach ($aResults as $aRow) {
            $iShopId = $aRow['OXID'];
            $aShopList[$iShopId] = new StdClass();
            $aShopList[$iShopId]->oxshops__oxid = new oxField($aRow['OXID']);
            $aShopList[$iShopId]->oxshops__oxname = new oxField($aRow['OXNAME']);
        }
        return $aShopList;
    }
}
