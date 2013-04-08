<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   admin
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: deliveryset_main.php 25466 2010-02-01 14:12:07Z alfonsas $
 */

/**
 * Extentions sorting list handler.
 * Admin Menu: Extentions -> Module -> Installed Shop Modules.
 * @package admin
 */
class Module_Sortlist extends oxAdminDetails
{

    /**
     * Executes parent method parent::render(), loads active and disabled extentions,
     * checks if there are some delted and registered modules and returns name of template file "module_sortlist.tpl".
     *
     * @return string
     */
    public function render()
    {
        $sOxId = $this->getEditObjectId();

        parent::render();

        $oModulelist = oxNew( "oxModulelist" );

        $this->_aViewData["aExtClasses"] = $this->getConfig()->getAllModules();

        $this->_aViewData["aDisabledModules"] = $oModulelist->getDisabledModuleClasses();

        // checking if there are any deleted extensions
        if ( oxSession::getVar( "blSkipDeletedExtCheking" ) == false ) {
            $aDeletedExt = $oModulelist->getDeletedExtensions();
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
        $aModule = oxConfig::getParameter("aModules");

        $aModules = json_decode( $aModule, true );

        $oModule = oxNew( "oxModule" );
        $aModules = $oModule->buildModuleChains( $aModules );

        $this->getConfig()->saveShopConfVar( "aarr", "aModules", $aModules );

    }

    /**
     * Removes extension metadata from eshop
     *
     * @return null
     */
    public function remove()
    {
        //if user selected not to update modules, skipping all updates
        if ( oxConfig::getParameter( "noButton" )) {
            oxRegistry::getSession()->setVar( "blSkipDeletedExtCheking", true );
            return;
        }

        $oModulelist = oxNew( "oxModulelist" );
        $oModulelist->cleanup();
    }

}
