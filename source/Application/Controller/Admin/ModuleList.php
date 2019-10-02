<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;

/**
 * Admin actionss manager.
 * Sets list template, list object class ('oxactions') and default sorting
 * field ('oxactions.oxtitle').
 * Admin Menu: Manage Products -> Actions.
 */
class ModuleList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * @var array Loaded modules array
     *
     */
    protected $_aModules = [];


    /**
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['mylist'] = $this->getInstalledModules();

        return 'module_list.tpl';
    }

    /**
     * @return array
     */
    private function getInstalledModules(): array
    {
        $container = ContainerFactory::getInstance()->getContainer();
        $shopConfiguration = $container->get(ShopConfigurationDaoBridgeInterface::class)->get();

        $modules = [];

        foreach ($shopConfiguration->getModuleConfigurations() as $moduleConfiguration) {
            $module = oxNew(Module::class);
            $module->load($moduleConfiguration->getId());
            $modules[] = $module;
        }

        $modules = $this->sortModulesByTitleAlphabetically($modules);
        $modules = $this->convertModulesToAssociativeArray($modules);

        return $modules;
    }

    /**
     * @param array $modules
     * @return array
     */
    private function sortModulesByTitleAlphabetically(array $modules): array
    {
        usort($modules, function ($a, $b) {
            return strcmp($a->getTitle(), $b->getTitle());
        });

        return $modules;
    }

    /**
     * @param array $modules
     * @return array
     */
    private function convertModulesToAssociativeArray(array $modules): array
    {
        $modulesAssociativeArray = [];

        foreach ($modules as $module) {
            $modulesAssociativeArray[$module->getId()] = $module;
        }

        return $modulesAssociativeArray;
    }
}
