<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\SmartyPluginDirectory;

class SmartyPluginDirectoriesDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'smartyPluginDirectories';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasSmartyPluginDirectories()) {
            $data[self::MAPPING_KEY] = $this->getSmartyPluginDirectory($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setSmartyPluginDirectory($moduleConfiguration, $data[self::MAPPING_KEY]);
        }
        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $directories
     */
    private function setSmartyPluginDirectory(ModuleConfiguration $moduleConfiguration, array $directories): void
    {
        foreach ($directories as $directory) {
            $moduleConfiguration->addSmartyPluginDirectory(new SmartyPluginDirectory(
                $directory
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getSmartyPluginDirectory(ModuleConfiguration $configuration): array
    {
        $directories = [];

        if ($configuration->hasSmartyPluginDirectories()) {
            foreach ($configuration->getSmartyPluginDirectories() as $directory) {
                $directories[] = $directory->getDirectory();
            }
        }

        return $directories;
    }
}
