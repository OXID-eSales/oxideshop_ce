<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataMapper;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

interface ThemeConfigurationDataMapperInterface
{
    public function toData(ThemeConfiguration $configuration): array;

    public function fromData(array $data): ThemeConfiguration;
}
