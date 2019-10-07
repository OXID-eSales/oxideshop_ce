<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassExtension;

class ClassExtensionsDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'classExtensions';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasClassExtensions()) {
            $data[self::MAPPING_KEY] = $this->getClassExtensions($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setClassExtensions($moduleConfiguration, $data[self::MAPPING_KEY]);
        }
        return $moduleConfiguration;
    }

    private function getClassExtensions(ModuleConfiguration $configuration): array
    {
        $extensions = [];

        foreach ($configuration->getClassExtensions() as $extension) {
            $extensions[$extension->getShopClassName()] = $extension->getModuleExtensionClassName();
        }

        return $extensions;
    }

    private function setClassExtensions(ModuleConfiguration $moduleConfiguration, array $extensions) : void
    {
        foreach ($extensions as $shopClass => $moduleClass) {
            $moduleConfiguration->addClassExtension(new ClassExtension(
                $shopClass,
                $moduleClass
            ));
        }
    }
}
