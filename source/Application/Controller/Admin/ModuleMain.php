<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
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
class ModuleMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates deliveryset category tree,
     * passes data to Smarty engine and returns name of template file "deliveryset_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("moduleId")) {
            $sModuleId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("moduleId");
        } else {
            $sModuleId = $this->getEditObjectId();
        }

        $oModule = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        if ($sModuleId) {
            if ($oModule->load($sModuleId)) {
                $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage();

                $this->_aViewData["oModule"] = $oModule;
                $this->_aViewData["sModuleName"] = basename($oModule->getInfo("title", $iLang));
                $this->_aViewData["sModuleId"] = str_replace("/", "_", $oModule->getModulePath());
            } else {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(new \OxidEsales\Eshop\Core\Exception\StandardException('EXCEPTION_MODULE_NOT_LOADED'));
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
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');

            return;
        }

        $sModule = $this->getEditObjectId();
        /** @var \OxidEsales\Eshop\Core\Module\Module $oModule */
        $oModule = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        if (!$oModule->load($sModule)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(new \OxidEsales\Eshop\Core\Exception\StandardException('EXCEPTION_MODULE_NOT_LOADED'));

            return;
        }
        try {
            /** @var \OxidEsales\Eshop\Core\Module\ModuleCache $oModuleCache */
            $oModuleCache = oxNew('oxModuleCache', $oModule);
            /** @var \OxidEsales\Eshop\Core\Module\ModuleInstaller $oModuleInstaller */
            $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

            if ($oModuleInstaller->activate($oModule)) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception);
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
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
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');

            return;
        }

        $sModule = $this->getEditObjectId();
        /** @var \OxidEsales\Eshop\Core\Module\Module $oModule */
        $oModule = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);
        if (!$oModule->load($sModule)) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(new \OxidEsales\Eshop\Core\Exception\StandardException('EXCEPTION_MODULE_NOT_LOADED'));

            return;
        }
        try {
            /** @var \OxidEsales\Eshop\Core\Module\ModuleCache $oModuleCache */
            $oModuleCache = oxNew('oxModuleCache', $oModule);
            /** @var \OxidEsales\Eshop\Core\Module\ModuleInstaller $oModuleInstaller */
            $oModuleInstaller = oxNew('oxModuleInstaller', $oModuleCache);

            if ($oModuleInstaller->deactivate($oModule)) {
                $this->_aViewData["updatenav"] = "1";
            }
        } catch (\OxidEsales\Eshop\Core\Exception\StandardException $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception);
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }
}
