<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\ModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;

class ControllersDataMapper implements ModuleConfigurationDataMapperInterface
{
    public const MAPPING_KEY = 'controllers';

    public function toData(ModuleConfiguration $configuration): array
    {
        $data = [];

        if ($configuration->hasControllers()) {
            $data[self::MAPPING_KEY] = $this->getControllers($configuration);
        }

        return $data;
    }

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration
    {
        if (isset($data[self::MAPPING_KEY])) {
            $this->setControllers($moduleConfiguration, $data[self::MAPPING_KEY]);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $controllers
     */
    private function setControllers(ModuleConfiguration $moduleConfiguration, array $controllers) : void
    {
        foreach ($controllers as $id => $controllerClassNamespace) {
            $moduleConfiguration->addController(new Controller(
                $id,
                $controllerClassNamespace
            ));
        }
    }

    /**
     * @param ModuleConfiguration $configuration
     *
     * @return array
     */
    private function getControllers(ModuleConfiguration $configuration): array
    {
        $controllers = [];

        if ($configuration->hasControllers()) {
            foreach ($configuration->getControllers() as $controller) {
                $controllers[$controller->getId()] = $controller->getControllerClassNameSpace();
            }
        }

        return $controllers;
    }
}
