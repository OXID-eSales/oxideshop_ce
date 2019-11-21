<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;

interface MetaDataToModuleConfigurationDataMapperInterface
{
    /**
     * @param array $metaData
     *
     * @return ModuleConfiguration
     */
    public function fromData(array $metaData): ModuleConfiguration;
}
