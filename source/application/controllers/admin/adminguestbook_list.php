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
 * Guestbook records list manager.
 * Returns template, that arranges guestbook records list.
 * Admin Menu: User information -> Guestbook.
 * @package admin
 */
class AdminGuestbook_List extends oxAdminList
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'adminguestbook_list.tpl';

    /**
     * List item object type
     *
     * @var string
     */
    protected $_sListClass = 'oxgbentry';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxcreate';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_blDesc = true;

    /**
     * Executes parent method parent::render(), gets entries with authors
     * and returns template file name "admin_guestbook.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oList = $this->getItemList();
        if ( $oList && $oList->count() ) {

            $oDb = oxDb::getDb();
            foreach ( $oList as $oEntry ) {
                // preloading user info ..
                if ( isset( $oEntry->oxgbentries__oxuserid ) && $oEntry->oxgbentries__oxuserid->value ) {
                    $oEntry->oxuser__oxlname = new oxField( $oDb->getOne( "select oxlname from oxuser where oxid=".$oDb->quote( $oEntry->oxgbentries__oxuserid->value ), false, false ));
                }
            }
        }

        $this->_aViewData["mylist"] = $oList;
        return $this->_sThisTemplate;
    }

}
