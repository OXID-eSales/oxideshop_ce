<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;

/**
 * Class MetaDataMapper
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\MetaData\DataMapper
 */
interface MetaDataToModuleConfigurationDataMapperInterface
{
    /**
     * @param array $metaData
     *
     * @return ModuleConfiguration
     */
    public function fromData(array $metaData): ModuleConfiguration;
}
