<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\{
    DataMapper\ModuleConfigurationExportDataMapperInterface, DataObject\ModuleConfiguration
};

class ClassExtensionsExportDataMapper implements ModuleConfigurationExportDataMapperInterface
{
    public function toData(ModuleConfiguration $configuration): array
    {
        return ['classExtensions' => $this->getClassExtensions($configuration)];
    }

    private function getClassExtensions(ModuleConfiguration $configuration): array
    {
        $extensions = [];

        if ($configuration->hasClassExtensions()) {
            $extensions['classExtension'] = [];
            foreach ($configuration->getClassExtensions() as $extension) {
                $extensions['classExtension'][] = [
                    'shopClass' => $extension->getShopClassName(),
                    'moduleClass' => $extension->getModuleExtensionClassName(),
                ];
            }
        }

        return $extensions;
    }
}
