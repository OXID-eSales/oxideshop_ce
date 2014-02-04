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
class Module_Main extends oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        if ( oxConfig::getParameter("moduleId") ) {
            $sModuleId = oxConfig::getParameter("moduleId");
        } else {
            $sModuleId = $this->getEditObjectId();
        }

        $oModule = oxNew('oxModule');

        if ( $sModuleId ) {
            if ( $oModule->load( $sModuleId ) ) {
                $iLang = oxRegistry::getLang()->getTplLanguage();

                $this->_aViewData["oModule"]     =  $oModule;
                $this->_aViewData["sModuleName"] = basename( $oModule->getInfo( "title", $iLang ) );
                $this->_aViewData["sModuleId"]   = str_replace( "/", "_", $oModule->getModulePath() );
            } else {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( new oxException('EXCEPTION_MODULE_NOT_LOADED') );
            }
        }

        parent::render();

        return 'module_main.tpl';
    }

    /**
     * Activate module
     *
     * @return null
     */
    public function activateModule()
    {
        $sModule = $this->getEditObjectId();
        $oModule = oxNew('oxModule');
        if (!$oModule->load($sModule)) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( new oxException('EXCEPTION_MODULE_NOT_LOADED') );
            return;
        }
        try {
            if ( $oModule->activate() ) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (oxException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $oEx->debugOut();
        }
    }

    /**
     * Deactivate module
     *
     * @return null
     */
    public function deactivateModule()
    {
        $sModule = $this->getEditObjectId();
        $oModule = oxNew('oxModule');
        if (!$oModule->load($sModule)) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( new oxException('EXCEPTION_MODULE_NOT_LOADED') );
            return;
        }
        try {
            if ( $oModule->deactivate() ) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (oxException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $oEx->debugOut();
        }
    }

    /**
     * Enables modules that dont have metadata file activation/deactivation by
     * writing to "aLegacyModules" config variable classes that current module
     * extedens
     *
     * @return bool
     */
    public function saveLegacyModule()
    {
        $aModuleInfo = explode( "\n", trim( oxConfig::getParameter("aExtendedClasses") ) );
        $sModuleLegacyId = trim( $this->getEditObjectId() );
        $sModuleId = trim( oxConfig::getParameter("moduleId") );
        $sModuleName = trim( oxConfig::getParameter("moduleName") );

        $oModule = oxNew('oxModule');
        $sModuleId = $oModule->saveLegacyModule($sModuleId, $sModuleName, $aModuleInfo);

        if ( $sModuleLegacyId != $sModuleId ) {
            $oModule->updateModuleIds( $sModuleLegacyId, $sModuleId );
            $this->setEditObjectId($sModuleId);
        }
    }
}
