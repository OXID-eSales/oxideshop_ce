<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\ClassWithoutNamespace;

class ClassesWithoutNamespaceDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'classesWithoutNamespace';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasClassWithoutNamespaces()) {
            $data[self::MAPPING_KEY] = $this->getClassWithoutNamespace($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setClassWithoutNamespace($moduleConfiguration, $data[self::MAPPING_KEY]);
        }
        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getClassWithoutNamespace(ModuleConfiguration $configuration): array
    {
        $classes = [];

        if ($configuration->hasClassWithoutNamespaces()) {
            foreach ($configuration->getClassesWithoutNamespace() as $class) {
                $classes[$class->getShopClass()] = $class->getModuleClass();
            }
        }

        return $classes;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $classes
     */
    private function setClassWithoutNamespace(ModuleConfiguration $moduleConfiguration, array $classes): void
    {
        foreach ($classes as $shopClass => $moduleClass) {
            $moduleConfiguration->addClassWithoutNamespace(new ClassWithoutNamespace(
                $shopClass,
                $moduleClass
            ));
        }
    }
}
