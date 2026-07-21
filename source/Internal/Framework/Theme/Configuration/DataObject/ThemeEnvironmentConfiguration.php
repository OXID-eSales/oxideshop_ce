<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject;

readonly class ThemeEnvironmentConfiguration
{
    /** @param array<string, mixed> $settingValues */
    public function __construct(
        private array $settingValues = [],
    ) {
    }

    /** @return array<string, mixed> */
    public function getSettingValues(): array
    {
        return $this->settingValues;
    }
}
