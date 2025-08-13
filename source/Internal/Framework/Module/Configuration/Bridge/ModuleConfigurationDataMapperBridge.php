<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Bridge;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper\{
    ModuleConfigurationExportDataMapperInterface
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

readonly class ModuleConfigurationDataMapperBridge implements ModuleConfigurationDataMapperBridgeInterface
{
    public function __construct(private ModuleConfigurationExportDataMapperInterface $moduleConfigurationDataMapper)
    {
    }

    public function toData(ModuleConfiguration $configuration): array
    {
        return $this->moduleConfigurationDataMapper->toData($configuration);
    }
}
