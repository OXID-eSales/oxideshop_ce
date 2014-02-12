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
 * Admin actionss manager.
 * Sets list template, list object class ('oxactions') and default sorting
 * field ('oxactions.oxtitle').
 * Admin Menu: Manage Products -> Actions.
 * @package admin
 */
class Actions_List extends oxAdminList
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'actions_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxactions';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxtitle';

    /**
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        // passing display type back to view
        $this->_aViewData["displaytype"] = oxConfig::getParameter( "displaytype" );

        return $this->_sThisTemplate;
    }

    /**
     * Adds active promotion check
     *
     * @param array  $aWhere  SQL condition array
     * @param string $sqlFull SQL query string
     *
     * @return $sQ
     */
    protected function _prepareWhereQuery( $aWhere, $sqlFull )
    {
        $sQ = parent::_prepareWhereQuery( $aWhere, $sqlFull );
        $sDisplayType = (int) oxConfig::getParameter( 'displaytype' );
        $sTable = getViewName( "oxactions" );

        //searchong for empty oxfolder fields
        if ( $sDisplayType ) {

            $sNow   = date( 'Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() );

            switch ( $sDisplayType ) {
                case 1: // active
                    $sQ .= " and {$sTable}.oxactivefrom < '{$sNow}' and {$sTable}.oxactiveto > '{$sNow}' ";
                    break;
                case 2: // upcoming
                    $sQ .= " and {$sTable}.oxactivefrom > '{$sNow}' ";
                    break;
                case 3: // expired
                    $sQ .= " and {$sTable}.oxactiveto < '{$sNow}' and {$sTable}.oxactiveto != '0000-00-00 00:00:00' ";
                    break;
            }
        }


        return $sQ;
    }
}