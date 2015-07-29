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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Class oxUserAddressList
 */
class oxUserAddressList extends oxList
{

    /**
     * Call parent class constructor
     */
    public function __construct()
    {
        parent::__construct('oxaddress');
    }

    /**
     * Selects and loads all address for particular user.
     *
     * @param string $sUserId user id
     */
    public function load($sUserId)
    {
        $sViewName = getViewName('oxcountry');
        $oBaseObject = $this->getBaseObject();
        $sSelectFields = $oBaseObject->getSelectFields();

        $sSelect = "
                SELECT {$sSelectFields}, `oxcountry`.`oxtitle` AS oxcountry
                FROM oxaddress
                LEFT JOIN {$sViewName} AS oxcountry ON oxaddress.oxcountryid = oxcountry.oxid
                WHERE oxaddress.oxuserid = " . oxDb::getDb()->quote($sUserId);
        $this->selectString($sSelect);
    }
}
