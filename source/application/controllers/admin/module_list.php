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
class Module_List extends oxAdminList
{

    /**
     * @var array Loaded modules array
     *
     */
    protected $_aModules = array();


    /**
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $sModulesDir = $this->getConfig()->getModulesDir();

        $oModuleList = oxNew( "oxModuleList" );
        $aModules = $oModuleList->getModulesFromDir( $sModulesDir );

        parent::render();

        // assign our list
        $this->_aViewData['mylist'] = $aModules;

        return 'module_list.tpl';
    }
}
