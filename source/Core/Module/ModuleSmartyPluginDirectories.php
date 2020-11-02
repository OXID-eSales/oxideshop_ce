<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\Eshop\Core\Module\Module as EshopModule;

/**
 * Class ModuleSmartyPluginDirectories.
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class ModuleSmartyPluginDirectories
{
    /**
     * @var EshopModule
     *
     * Needed to get the absolute path to a module directory
     */
    private $module = null;

    /**
     * @var array
     */
    private $moduleSmartyPluginDirectories = [];

    /**
     * SmartyPluginDirectoryBridge constructor.
     */
    public function __construct(EshopModule $module)
    {
        $this->module = $module;
    }

    /**
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to add them.
     *
     * @param array  $moduleSmartyPluginDirectories
     * @param string $moduleId
     */
    public function add($moduleSmartyPluginDirectories, $moduleId): void
    {
        $this->moduleSmartyPluginDirectories[$moduleId] = $moduleSmartyPluginDirectories;
    }

    /**
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to set them.
     *
     * @param array $moduleSmartyPluginDirectories
     */
    public function set($moduleSmartyPluginDirectories): void
    {
        $this->moduleSmartyPluginDirectories = $moduleSmartyPluginDirectories;
    }

    /**
     * Delete the smarty plugin directories for the module, given by its ID.
     *
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to remove them.
     *
     * @param string $moduleId the ID of the module, for which we want to delete the controllers from the storage
     */
    public function remove($moduleId): void
    {
        unset($this->moduleSmartyPluginDirectories[$moduleId]);
    }

    /**
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to get them.
     *
     * @return array The smarty plugin directories of all modules with absolute path as numeric array
     */
    public function getWithRelativePath()
    {
        return $this->moduleSmartyPluginDirectories;
    }

    /**
     * @return array
     */
    public function getWithFullPath()
    {
        $smartyPluginDirectoriesWithFullPath = [];
        $smartyPluginDirectories = $this->getWithRelativePath();

        foreach ($smartyPluginDirectories as $moduleId => $smartyDirectoriesOfOneModule) {
            foreach ($smartyDirectoriesOfOneModule as $smartyPluginDirectory) {
                $smartyPluginDirectoriesWithFullPath[] = $this->module->getModuleFullPath($moduleId) .
                                                         \DIRECTORY_SEPARATOR .
                                                         $smartyPluginDirectory;
            }
        }

        return $smartyPluginDirectoriesWithFullPath;
    }
}
