<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;

interface ThemeConfigurationValidatorInterface
{
    public function validate(ThemeConfiguration $themeConfiguration, int $shopId): void;
}
