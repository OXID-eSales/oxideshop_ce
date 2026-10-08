<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

interface SettingValueMapperInterface
{
    public function toFormValue(Setting $setting): bool|string;

    /**
     * @param array<string, string> $formValues
     * @return array<string, mixed>
     */
    public function fromFormValues(ThemeConfiguration $configuration, array $formValues): array;
}
