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
 * User list manager.
 *
 * @package model
 */
class oxUserList extends oxList
{
    /**
     * Class constructor
     *
     * @param string $sObjectsInListName Associated list item object type
     *
     * @return null
     */
    public function __construct( $sObjectsInListName = 'oxuser')
    {
        parent::__construct( 'oxuser');
    }


    /**
     * Load searched user list with wishlist
     *
     * @param string $sSearchStr Search string
     *
     * @return null;
     */
    public function loadWishlistUsers( $sSearchStr)
    {
        $sSearchStr = oxDb::getInstance()->escapeString($sSearchStr);
        if (!$sSearchStr) {
            return;
        }

        $sSelect  = "select oxuser.oxid, oxuser.oxfname, oxuser.oxlname from oxuser ";
        $sSelect .= "left join oxuserbaskets on oxuserbaskets.oxuserid = oxuser.oxid ";
        $sSelect .= "where oxuserbaskets.oxid is not null and oxuserbaskets.oxtitle = 'wishlist' ";
        $sSelect .= "and oxuserbaskets.oxpublic = 1 ";
        $sSelect .= "and ( oxuser.oxusername like '%$sSearchStr%' or oxuser.oxlname like '%$sSearchStr%')";
        $sSelect .= "and ( select 1 from oxuserbasketitems where oxuserbasketitems.oxbasketid = oxuserbaskets.oxid limit 1)";

        $this->selectString($sSelect);
    }
}
