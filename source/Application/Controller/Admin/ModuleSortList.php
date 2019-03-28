<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Extensions sorting list handler.
 * Admin Menu: Extensions -> Module -> Installed Shop Modules.
 */
class ModuleSortList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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

        $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);

        $extendClass = $this->getConfig()->getModulesWithExtendedClass();
        $sanitizedExtendClass = [];
        foreach ($extendClass as $key => $value) {
            $sanitizedKey = str_replace("\\", self::BACKSLASH_REPLACEMENT, $key);
            $sanitizedExtendClass[$sanitizedKey] = $value;
        }
        $this->_aViewData["aExtClasses"] = $sanitizedExtendClass;
        $this->_aViewData["aDisabledModules"] = $oModuleList->getDisabledModuleClasses();

        // checking if there are any deleted extensions
        if (\OxidEsales\Eshop\Core\Registry::getSession()->getVariable("blSkipDeletedExtChecking") == false) {
            $aDeletedExt = $oModuleList->getDeletedExtensions();

            if (!empty($aDeletedExt)) {
                $this->_aViewData["aDeletedExt"] = $aDeletedExt;
            }
        }

        return 'module_sortlist.tpl';
    }

    /**
     * Saves updated aModules config var
     */
    public function save()
    {
        $aModule = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("aModules");

        $aModules = [];
        if ($tmp = json_decode($aModule, true)) {
            foreach ($tmp as $key => $value) {
                $sanitizedKey = str_replace(self::BACKSLASH_REPLACEMENT, "\\", $key);
                $aModules[$sanitizedKey] = $value;
            }
            $oModuleInstaller = oxNew(\OxidEsales\Eshop\Core\Module\ModuleInstaller::class);
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
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("noButton")) {
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("blSkipDeletedExtChecking", true);

            return;
        }

        $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $oModuleList->cleanup();
    }
}
