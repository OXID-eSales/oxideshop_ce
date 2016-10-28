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

/**
 * Extensions sorting list handler.
 * Admin Menu: Extensions -> Module -> Installed Shop Modules.
 */
class ModuleSortList extends \oxAdminDetails
{

    /**
     * It is unsave to use a backslash as HTML id in conjunction with UI.sortable, so it will be replaced in the
     * view and restored in the controller
     */
    const BACKSLASH_REPLACEMENT = '---';

    /**
     * Executes parent method parent::render(), loads active and disabled extensions,
     * checks if there are some deleted and registered modules and returns name of template file "module_sortlist.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oModuleList = oxNew("oxModuleList");

        $extendClass = $this->getConfig()->getModulesWithExtendedClass();
        $sanitizedExtendClass = [];
        foreach ($extendClass as $key => $value) {
            $sanitizedKey = str_replace("\\", self::BACKSLASH_REPLACEMENT, $key);
            $sanitizedExtendClass[$sanitizedKey] = $value;
        }
        $this->_aViewData["aExtClasses"] = $sanitizedExtendClass;
        $this->_aViewData["aDisabledModules"] = $oModuleList->getDisabledModuleClasses();

        // checking if there are any deleted extensions
        if (oxRegistry::getSession()->getVariable("blSkipDeletedExtChecking") == false) {
            $aDeletedExt = $oModuleList->getDeletedExtensions();
        }

        if (!empty($aDeletedExt)) {
            $this->_aViewData["aDeletedExt"] = $aDeletedExt;
        }

        return 'module_sortlist.tpl';
    }

    /**
     * Saves updated aModules config var
     */
    public function save()
    {
        $aModule = oxRegistry::getConfig()->getRequestParameter("aModules");

        $aModules = [];
        if($tmp = json_decode($aModule, true)){
            foreach ($tmp as $key => $value) {
                $sanitizedKey = str_replace(self::BACKSLASH_REPLACEMENT, "\\", $key);
                $aModules[$sanitizedKey] = $value;
            }
            $oModuleInstaller = oxNew('oxModuleInstaller');
            $aModules = $oModuleInstaller->buildModuleChains($aModules);
        }

        $this->getConfig()->saveShopConfVar("aarr", "aModules", $aModules);
    }

    /**
     * Removes extension metadata from eShop
     *
     * @return null
     */
    public function remove()
    {
        //if user selected not to update modules, skipping all updates
        if (oxRegistry::getConfig()->getRequestParameter("noButton")) {
            oxRegistry::getSession()->setVariable("blSkipDeletedExtChecking", true);

            return;
        }

        $oModuleList = oxNew("oxModuleList");
        $oModuleList->cleanup();
    }
}
