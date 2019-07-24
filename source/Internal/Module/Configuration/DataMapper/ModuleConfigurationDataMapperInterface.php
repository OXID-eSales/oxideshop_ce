<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * @internal
 */
interface ModuleConfigurationDataMapperInterface
{
    public function toData(ModuleConfiguration $configuration): array;

    public function fromData(ModuleConfiguration $moduleConfiguration, array $data): ModuleConfiguration;
}
