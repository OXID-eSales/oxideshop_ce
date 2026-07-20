<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Validator;

readonly class SettingValueValidator implements SettingValueValidatorInterface
{
    public function isValid(string $value): bool
    {
        return preg_match('/<\s*script\b/iu', $value) !== 1;
    }
}
