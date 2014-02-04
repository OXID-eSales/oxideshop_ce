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
 * Extensions sorting list handler.
 * Admin Menu: Extensions -> Module -> Installed Shop Modules.
 * @package admin
 */
class Module_SortList extends oxAdminDetails
{

    /**
     * Executes parent method parent::render(), loads active and disabled extensions,
     * checks if there are some deleted and registered modules and returns name of template file "module_sortlist.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oModuleList = oxNew( "oxModuleList" );

        $this->_aViewData["aExtClasses"] = $this->getConfig()->getAllModules();
        $this->_aViewData["aDisabledModules"] = $oModuleList->getDisabledModuleClasses();

        // checking if there are any deleted extensions
        if ( oxRegistry::getSession()->getVariable( "blSkipDeletedExtChecking" ) == false ) {
            $aDeletedExt = $oModuleList->getDeletedExtensions();
        }

        if ( !empty($aDeletedExt) ) {
            $this->_aViewData["aDeletedExt"] = $aDeletedExt;
        }

        return 'module_sortlist.tpl';
    }

    /**
     * Saves updated aModules config var
     *
     * @return null
     */
    public function save()
    {
        $aModule = oxRegistry::getConfig()->getRequestParameter( "aModules" );

        $aModules = json_decode( $aModule, true );

        $oModule = oxNew( "oxModule" );
        $aModules = $oModule->buildModuleChains( $aModules );

        $this->getConfig()->saveShopConfVar( "aarr", "aModules", $aModules );

    }

    /**
     * Removes extension metadata from eShop
     *
     * @return null
     */
    public function remove()
    {
        //if user selected not to update modules, skipping all updates
        if ( oxRegistry::getConfig()->getRequestParameter( "noButton" )) {
            oxRegistry::getSession()->setVariable( "blSkipDeletedExtChecking", true );
            return;
        }

        $oModuleList = oxNew( "oxModuleList" );
        $oModuleList->cleanup();
    }

}
