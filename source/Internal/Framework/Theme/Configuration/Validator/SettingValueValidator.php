<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Validator;

readonly class SettingValueValidator implements SettingValueValidatorInterface
{
    private const string DANGEROUS_ELEMENT_PATTERN =
        '/<\s*\/?\s*(?:script|iframe|object|embed|svg|style|link|meta|base|applet|frame|frameset|form)\b/iu';
    private const string EVENT_HANDLER_PATTERN = '/<[^>]*\son[a-z]+\s*=/iu';
    private const string DANGEROUS_SCHEME_PATTERN = '/(?:javascript|vbscript)\s*:/iu';

    public function isValid(string $value): bool
    {
        foreach ($this->forbiddenPatterns() as $pattern) {
            if (preg_match($pattern, $value) === 1) {
                return false;
            }
        }

        return true;
    }

    private function forbiddenPatterns(): array
    {
        return [
            self::DANGEROUS_ELEMENT_PATTERN,
            self::EVENT_HANDLER_PATTERN,
            self::DANGEROUS_SCHEME_PATTERN,
        ];
    }
}
