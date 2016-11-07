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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxException;
use oxModule;
use oxModuleCache;
use oxModuleInstaller;

/**
 * Admin article main deliveryset manager.
 * There is possibility to change deliveryset name, article, user
 * and etc.
 * Admin Menu: Shop settings -> Shipping & Handling -> Main Sets.
 */
class ModuleMain extends \oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        if (oxRegistry::getConfig()->getRequestParameter("moduleId")) {
            $sModuleId = oxRegistry::getConfig()->getRequestParameter("moduleId");
        } else {
            $sModuleId = $this->getEditObjectId();
        }

        $oModule = oxNew('oxModule');

        if ($sModuleId) {
            if ($oModule->load($sModuleId)) {
                $iLang = oxRegistry::getLang()->getTplLanguage();

                $this->_aViewData["oModule"] = $oModule;
                $this->_aViewData["sModuleName"] = basename($oModule->getInfo("title", $iLang));
                $this->_aViewData["sModuleId"] = str_replace("/", "_", $oModule->getModulePath());
            } else {
                oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('EXCEPTION_MODULE_NOT_LOADED'));
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
        if ($this->getConfig()->isDemoShop()) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');

            return;
        }

        $sModule = $this->getEditObjectId();
        /** @var oxModule $oModule */
        $oModule = oxNew('oxModule');
        if (!$oModule->load($sModule)) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('EXCEPTION_MODULE_NOT_LOADED'));

            return;
        }
        try {
            /** @var oxModuleCache $oModuleCache */
            $oModuleCache = oxNew('oxModuleCache', $oModule);
            /** @var oxModuleInstaller $oModuleInstaller */
            $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

            if ($oModuleInstaller->activate($oModule)) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx);
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
        if ($this->getConfig()->isDemoShop()) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');

            return;
        }

        $sModule = $this->getEditObjectId();
        /** @var oxModule $oModule */
        $oModule = oxNew('oxModule');
        if (!$oModule->load($sModule)) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay(new oxException('EXCEPTION_MODULE_NOT_LOADED'));

            return;
        }
        try {
            /** @var oxModuleCache $oModuleCache */
            $oModuleCache = oxNew('oxModuleCache', $oModule);
            /** @var oxModuleInstaller $oModuleInstaller */
            $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

            if ($oModuleInstaller->deactivate($oModule)) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (\OxidEsales\EshopCommunity\Core\Exception\StandardException $oEx) {
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx);
            $oEx->debugOut();
        }
    }
}
