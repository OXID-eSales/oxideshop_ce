<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;

/**
 * @internal
 */
interface ProjectConfigurationDataMapperInterface
{
    /**
     * @param ProjectConfiguration $configuration
     * @return array
     */
    public function toData(ProjectConfiguration $configuration): array;

    /**
     * @param array $data
     * @return ProjectConfiguration
     */
    public function fromData(array $data): ProjectConfiguration;
}
