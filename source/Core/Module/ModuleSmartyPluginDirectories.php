<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderBridgeInterface;
use Webmozart\PathUtil\Path;

/**
 * Class ModuleSmartyPluginDirectories
 *
 * @internal Do not make a module extension for this class.
 *
 * @ignore   This class will not be included in documentation.
 */
class ModuleSmartyPluginDirectories
{
    /**
     * @var array
     */
    private $moduleSmartyPluginDirectories = [];

    /**
     *
     * @deprecated since v6.4.0 (2019-05-24); Module smarty plugins directory are stored in project configuration file now.
     *             Use appropriate Dao to set them.
     *
     * @param array $moduleSmartyPluginDirectories
     */
    public function set($moduleSmartyPluginDirectories)
    {
        $this->moduleSmartyPluginDirectories = $moduleSmartyPluginDirectories;
    }

    /**
     * @return array
     */
    public function getWithFullPath()
    {
        $smartyPluginDirectoriesWithFullPath = [];

        foreach ($this->getActiveModulePaths() as $moduleId => $modulePath) {
            if (isset($this->moduleSmartyPluginDirectories[$moduleId])) {
                foreach ($this->moduleSmartyPluginDirectories[$moduleId] as $smartyPluginDirectory) {
                    $smartyPluginDirectoriesWithFullPath[] = Path::join($modulePath, $smartyPluginDirectory);
                }
            }
        }

        return $smartyPluginDirectoriesWithFullPath;
    }

    /**
     * @return string[]
     */
    private function getActiveModulePaths(): array
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(ActiveModulesDataProviderBridgeInterface::class)
            ->getModulePaths();
    }
}
