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
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 * @package admin
 */
class Theme_Main extends oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $soxId = $this->getEditObjectId();

        $oTheme = oxNew('oxTheme');

        if (!$soxId) {
            $soxId = $oTheme->getActiveThemeId();
        }

        if ($oTheme->load($soxId)) {
            $this->_aViewData["oTheme"] =  $oTheme;
        } else {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( oxNew( "oxException", 'EXCEPTION_THEME_NOT_LOADED') );
        }

        parent::render();

        if ( $this->themeInConfigFile() ) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_THEME_SHOULD_BE_ONLY_IN_DATABASE' );
        }

        return 'theme_main.tpl';
    }
    
    /**
     * Check if theme config is in config file.
     * 
     * @return bool
     */
    public function themeInConfigFile()
    {
        $blThemeSet = isset( $this->getConfig()->sTheme );
        $blCustomThemeSet = isset( $this->getConfig()->sCustomTheme );
        
        if ( $blThemeSet || $blCustomThemeSet ) {
            return true;
        }
        return false;
    }


    /**
     * Set theme
     *
     * @return null
     */
    public function setTheme()
    {
        $sTheme = $this->getEditObjectId();
        $oTheme = oxNew('oxtheme');
        if (!$oTheme->load($sTheme)) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( oxNew( "oxException", 'EXCEPTION_THEME_NOT_LOADED') );
            return;
        }
        try {
            $oTheme->activate();
            $this->resetContentCache();
        } catch (oxException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $oEx->debugOut();
        }
    }
}
