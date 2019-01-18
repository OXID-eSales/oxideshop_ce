<?php
declare(strict_types=1);

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
    /**
     * @param ModuleConfiguration $configuration
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array;

    /**
     * @param array $data
     *
     * @return ModuleConfiguration
     */
    public function fromData(array $data): ModuleConfiguration;
}
