<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\View\Form;

interface SettingValueValidatorInterface
{
    public function isValid(string $value): bool;
}
