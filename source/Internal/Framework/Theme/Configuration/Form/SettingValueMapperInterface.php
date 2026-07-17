<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;

interface SettingValueMapperInterface
{
    public function toFormValue(Setting $setting): bool|string;

    /**
     * @param array<string, string> $formValues
     * @return array<string, mixed>
     */
    public function fromFormValues(ThemeConfiguration $configuration, array $formValues): array;
}
