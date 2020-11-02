<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;

interface ProjectConfigurationDataMapperInterface
{
    public function toData(ProjectConfiguration $configuration): array;

    public function fromData(array $data): ProjectConfiguration;
}
