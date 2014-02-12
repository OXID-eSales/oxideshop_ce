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
 * CVS export manager.
 * Performs export function according to user chosen categories.
 * Admin Menu: Maine Menu -> Im/Export -> Export.
 * @package admin
 */
class Tools_Main extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), passes data to Smarty engine
     * and returns name of template file "imex_export.tpl".
     *
     * @return string
     */
    public function render()
    {
        if ($this->getConfig()->isDemoShop()) {
            oxRegistry::getUtils()->showMessageAndExit( "Access denied !" );
        }

        parent::render();

        $oAuthUser = oxNew( 'oxuser' );
        $oAuthUser->loadAdminUser();
        $this->_aViewData["blIsMallAdmin"] = $oAuthUser->oxuser__oxrights->value == "malladmin";
        
        $blShowUpdateViews = $this->getConfig()->getConfigParam( 'blShowUpdateViews' );
        $this->_aViewData['showViewUpdate'] = ( isset( $blShowUpdateViews ) && !$blShowUpdateViews ) ? false : true;

        return "tools_main.tpl";
    }
}
