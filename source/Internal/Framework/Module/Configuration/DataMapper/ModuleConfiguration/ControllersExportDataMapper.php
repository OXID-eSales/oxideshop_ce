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

class ControllersExportDataMapper implements ModuleConfigurationExportDataMapperInterface
{
    public function toData(ModuleConfiguration $configuration): array
    {
        return ['controllers' => $this->getControllers($configuration)];
    }
    private function getControllers(ModuleConfiguration $configuration): array
    {
        $controllers = [];

        if ($configuration->hasControllers()) {
            $controllers['controller'] = [];
            foreach ($configuration->getControllers() as $controller) {
                $controllers['controller'][] = [
                    'id' => $controller->getId(),
                    'controllerClassNameSpace' => $controller->getControllerClassNameSpace(),
                ];
            }
        }

        return $controllers;
    }
}
