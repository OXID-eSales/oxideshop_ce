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
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 *
 */
class ModuleSmartyPluginDirectoryRepository
{
    /**
     * @var string The key under which the value will be stored.
     */
    const STORAGE_KEY = 'moduleSmartyPluginDirectories';

    /** @var Config  */
    private $config;

    /**
     * @var EshopModuleVariablesLocator
     *
     * Necessary for caching
     */
    private $moduleVariablesLocator;

    /**
     * @var EshopModule
     */
    private $module;

    /**
     * ModuleSmartyPluginDirectoryRepository constructor.
     *
     * @param Config                      $config                 For database connection
     * @param EshopModuleVariablesLocator $moduleVariablesLocator For caching
     * @param EshopModule                 $module
     */
    public function __construct(
        Config $config,
        EshopModuleVariablesLocator $moduleVariablesLocator,
        EshopModule $module
    ) {
        $this->config = $config;
        $this->moduleVariablesLocator = $moduleVariablesLocator;
        $this->module = $module;
    }

    /**
     * @return EshopModuleSmartyPluginDirectories
     */
    public function get()
    {
        $smartyPluginDirectories = oxNew(
            EshopModuleSmartyPluginDirectories::class,
            $this->module
        );

        $smartyPluginDirectories->set(
            $this->getSmartyPluginDirectoriesFromModuleVariablesLocator()
        );

        return $smartyPluginDirectories;
    }

    /**
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to save them.
     *
     * @param EshopModuleSmartyPluginDirectories $moduleSmartyPluginDirectories
     */
    public function save(EshopModuleSmartyPluginDirectories $moduleSmartyPluginDirectories)
    {
        $this->config->saveShopConfVar(
            'aarr',
            self::STORAGE_KEY,
            $moduleSmartyPluginDirectories->getWithRelativePath()
        );
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
