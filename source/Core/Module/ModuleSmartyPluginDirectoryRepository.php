<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Module\ModuleSmartyPluginDirectories  as EshopModuleSmartyPluginDirectories;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator         as EshopModuleVariablesLocator;
use OxidEsales\Eshop\Core\Module\Module                         as EshopModule;

/**
 * Class ModuleSmartyPluginDirectoryRepository
 *
 * @internal Do not make a module extension for this class.
 *
 * @ignore   This class will not be included in documentation.
 */
class ModuleSmartyPluginDirectoryRepository
{
    /**
     * @var string The key under which the value will be stored.
     */
    const STORAGE_KEY = 'moduleSmartyPluginDirectories';

    /**
     * @var EshopModuleVariablesLocator
     *
     * Necessary for caching
     */
    private $moduleVariablesLocator;

    /**
     * ModuleSmartyPluginDirectoryRepository constructor.
     *
     * @param EshopModuleVariablesLocator $moduleVariablesLocator For caching
     */
    public function __construct(
        EshopModuleVariablesLocator $moduleVariablesLocator
    ) {
        $this->moduleVariablesLocator = $moduleVariablesLocator;
    }

    /**
     * @return EshopModuleSmartyPluginDirectories
     */
    public function get()
    {
        $smartyPluginDirectories = oxNew(EshopModuleSmartyPluginDirectories::class);

        $smartyPluginDirectories->set(
            $this->getSmartyPluginDirectoriesFromModuleVariablesLocator()
        );

        return $smartyPluginDirectories;
    }

    /**
     * @return array
     */
    private function getSmartyPluginDirectoriesFromModuleVariablesLocator()
    {
        $directories = $this->moduleVariablesLocator->getModuleVariable(self::STORAGE_KEY);

        return $directories ? $directories : [];
    }
}
