<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;

class ModuleMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        if (Registry::getRequest()->getRequestEscapedParameter("moduleId")) {
            $sModuleId = Registry::getRequest()->getRequestEscapedParameter("moduleId");
        } else {
            $sModuleId = $this->getEditObjectId();
        }

        $oModule = oxNew(\OxidEsales\Eshop\Core\Module\Module::class);

        if ($sModuleId) {
            if ($oModule->load($sModuleId)) {
                $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getTplLanguage();

                $this->_aViewData["oModule"] = $oModule;
                $this->_aViewData["sModuleName"] = basename($oModule->getInfo("title", $iLang));
                $this->_aViewData["sModuleId"] = $oModule->getId();
            } else {
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay(new \OxidEsales\Eshop\Core\Exception\StandardException('EXCEPTION_MODULE_NOT_LOADED'));
            }
        }

        parent::render();

        return 'module_main';
    }

    /**
     * Activate module
     *
     * @return null
     */
    public function activateModule()
    {
        if (Registry::getConfig()->isDemoShop()) {
            Registry::getUtilsView()->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');
            return;
        }

        try {
            ContainerFacade::get(ModuleActivationBridgeInterface::class)
                ->activate(
                    $this->getEditObjectId(),
                    Registry::getConfig()->getShopId()
                );

            $this->_aViewData['updatenav'] = '1';
        } catch (\Exception $exception) {
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
        if (Registry::getConfig()->isDemoShop()) {
            Registry::getUtilsView()->addErrorToDisplay('MODULE_ACTIVATION_NOT_POSSIBLE_IN_DEMOMODE');
            return;
        }

        try {
            ContainerFacade::get(ModuleActivationBridgeInterface::class)
                ->deactivate(
                    $this->getEditObjectId(),
                    Registry::getConfig()->getShopId()
                );

            $this->_aViewData['updatenav'] = '1';
        } catch (\Exception $exception) {
            Registry::getUtilsView()->addErrorToDisplay($exception);
            Registry::getLogger()->error($exception->getMessage(), [$exception]);
        }
    }
}
