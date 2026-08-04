<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Validator;

readonly class SettingValueValidator implements SettingValueValidatorInterface
{
    private const URL_SCHEME_PATTERN = '/^[a-z][a-z\d+.-]*:/i';
    private const OBFUSCATED_SCRIPT_SCHEME_PATTERN = '/^(?:java\s*script|vb\s*script)\s*:/i';
    private const HTTP_URL_PATTERN = '/^https?:\/\//i';
    private const URI_CHARSET_PATTERN = '/^[A-Za-z0-9\-._~:\/?#\[\]@!$&\'()*+,;=%]*$/';
    private const MARKUP_PATTERN = '/<[a-z!\/]/i';

    public function isValid(string $value): bool
    {
        return $this->hasUrlScheme($value)
            ? $this->isValidHttpUrl($value)
            : !$this->containsMarkup($value);
    }

    private function hasUrlScheme(string $value): bool
    {
        $trimmedValue = trim($value);

        return preg_match(self::URL_SCHEME_PATTERN, $trimmedValue) === 1
            || preg_match(self::OBFUSCATED_SCRIPT_SCHEME_PATTERN, $trimmedValue) === 1;
    }

    private function isValidHttpUrl(string $value): bool
    {
        return $this->hasHttpScheme($value)
            && $this->containsOnlyUrlCharacters($value)
            && $this->hasHost($value);
    }

    private function hasHttpScheme(string $value): bool
    {
        return preg_match(self::HTTP_URL_PATTERN, $value) === 1;
    }

    private function containsOnlyUrlCharacters(string $value): bool
    {
        return preg_match(self::URI_CHARSET_PATTERN, $value) === 1;
    }

    private function hasHost(string $value): bool
    {
        $host = parse_url($value, PHP_URL_HOST);

        return is_string($host) && $host !== '';
    }

    private function containsMarkup(string $value): bool
    {
        return preg_match(self::MARKUP_PATTERN, $value) === 1;
    }
}
