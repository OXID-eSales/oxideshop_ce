<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Module;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge\ShopConfigurationDaoBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

/**
 * Class responsible for cleaning not used extensions for module which is going to be activated.
 *
 * @deprecated since v6.4.0 (2019-03-22); the whole chain is updated during module activation and deactivation in the database we do not need this functionality any more
 *
 * @internal do not make a module extension for this class
 *
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ModuleExtensionsCleaner
{
    /**
     * Removes garbage ( module not used extensions ) from all installed extensions list.
     * For example: some classes were renamed, so these should be removed.
     *
     * @param array $installedExtensions
     *
     * @return array
     *
     * @throws ModuleConfigurationNotFoundException
     */
    public function cleanExtensions($installedExtensions, \OxidEsales\Eshop\Core\Module\Module $module)
    {
        $moduleExtensions = $module->getExtensions();

        $installedModuleExtensions = $this->filterExtensionsByModuleId($installedExtensions, $module->getId());

        if (\count($installedModuleExtensions)) {
            $garbage = $this->getModuleExtensionsGarbage($moduleExtensions, $installedModuleExtensions);

            if (\count($garbage)) {
                $installedExtensions = $this->removeGarbage($installedExtensions, $garbage);
            }
        }

        return $installedExtensions;
    }

    /**
     * Returns extensions list by module id.
     *
     * @return array
     *
     * @throws ModuleConfigurationNotFoundException
     */
    private function filterExtensionsByModuleId(array $installedExtensions, string $moduleId)
    {
        $container = ContainerFactory::getInstance()->getContainer();

        $moduleConfiguration = $container
            ->get(ShopConfigurationDaoBridgeInterface::class)
            ->get()
            ->getModuleConfiguration($moduleId);

        $filteredExtensions = [];

        foreach ($installedExtensions as $class => $extend) {
            foreach ($extend as $extendPath) {
                if (0 === strpos($extendPath, $moduleConfiguration->getPath())) {
                    $filteredExtensions[$class][] = $extendPath;
                }
            }
        }

        return $filteredExtensions;
    }

    /**
     * Returns extension which is no longer in metadata - garbage.
     *
     * @param array $moduleMetaDataExtensions  extensions defined in metadata
     * @param array $moduleInstalledExtensions extensions which are installed
     *
     * @return array
     */
    protected function getModuleExtensionsGarbage($moduleMetaDataExtensions, $moduleInstalledExtensions)
    {
        $garbage = $moduleInstalledExtensions;

        foreach ($garbage as $installedClassName => $installedClassPaths) {
            if (isset($moduleMetaDataExtensions[$installedClassName])) {
                // In case more than one extension is specified per module.
                $metaDataExtensionPaths = $moduleMetaDataExtensions[$installedClassName];
                if (!\is_array($metaDataExtensionPaths)) {
                    $metaDataExtensionPaths = [$metaDataExtensionPaths];
                }

                foreach ($installedClassPaths as $index => $installedClassPath) {
                    if (\in_array($installedClassPath, $metaDataExtensionPaths, true)) {
                        unset($garbage[$installedClassName][$index]);
                    }
                }

                if (0 === \count($garbage[$installedClassName])) {
                    unset($garbage[$installedClassName]);
                }
            }
        }

        return $garbage;
    }

    /**
     * Removes garbage - not exiting module extensions, returns clean array of installed extensions.
     *
     * @param array $installedExtensions all installed extensions ( from all modules )
     * @param array $garbage             extension which are not used and should be removed
     *
     * @return array
     */
    protected function removeGarbage($installedExtensions, $garbage)
    {
        foreach ($garbage as $className => $classPaths) {
            foreach ($classPaths as $sClassPath) {
                if (isset($installedExtensions[$className])) {
                    unset($installedExtensions[$className][array_search($sClassPath, $installedExtensions[$className], true)]);
                    if (0 === \count($installedExtensions[$className])) {
                        unset($installedExtensions[$className]);
                    }
                }
            }
        }

        return $installedExtensions;
    }
}
