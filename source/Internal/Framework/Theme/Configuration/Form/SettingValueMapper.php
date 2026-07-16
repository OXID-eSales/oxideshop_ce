<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Form;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;

readonly class SettingValueMapper implements SettingValueMapperInterface
{
    private const TYPE_BOOLEAN = 'bool';
    private const TYPE_NUMBER = 'num';
    private const TYPE_COLLECTION = 'arr';
    private const TYPE_ASSOCIATIVE_COLLECTION = 'aarr';

    public function toFormValue(Setting $setting): bool|string
    {
        $value = $setting->getValue();

        return match ($setting->getType()) {
            self::TYPE_BOOLEAN => (bool) $value,
            self::TYPE_COLLECTION => $this->joinLines((array) $value),
            self::TYPE_ASSOCIATIVE_COLLECTION => $this->joinKeyValueLines((array) $value),
            default => $this->stringify($value),
        };
    }

    public function fromFormValues(ThemeConfiguration $configuration, array $formValues): array
    {
        $settingValues = [];

        foreach ($configuration->getThemeSettings() as $setting) {
            if (isset($formValues[$setting->getName()])) {
                $settingValues[$setting->getName()] = $this->fromFormValue($setting, $formValues[$setting->getName()]);
            }
        }

        return $settingValues;
    }

    private function fromFormValue(Setting $setting, string $formValue): mixed
    {
        return match ($setting->getType()) {
            self::TYPE_BOOLEAN => filter_var($formValue, FILTER_VALIDATE_BOOLEAN),
            self::TYPE_NUMBER => $this->toNumber($formValue),
            self::TYPE_COLLECTION => $this->splitLines($formValue),
            self::TYPE_ASSOCIATIVE_COLLECTION => $this->splitKeyValueLines($formValue),
            default => $formValue,
        };
    }

    private function joinLines(array $values): string
    {
        return implode("\n", array_map(fn(mixed $value): string => $this->stringify($value), $values));
    }

    /** @return string[] */
    private function splitLines(string $formValue): array
    {
        $lines = array_map(static fn(string $line): string => trim($line), explode("\n", $formValue));

        return array_values(array_filter($lines, static fn(string $line): bool => $line !== ''));
    }

    private function joinKeyValueLines(array $values): string
    {
        $lines = [];

        foreach ($values as $key => $value) {
            if (is_scalar($value)) {
                $lines[] = $key . ' => ' . $this->stringify($value);
            }
        }

        return implode("\n", $lines);
    }

    /** @return array<string, string> */
    private function splitKeyValueLines(string $formValue): array
    {
        $keyValues = [];

        foreach ($this->splitLines($formValue) as $line) {
            if (preg_match('/(.+)=>(.+)/', $line, $matches) === 1) {
                $keyValues[trim($matches[1])] = trim($matches[2]);
            }
        }

        return $keyValues;
    }

    private function toNumber(string $formValue): float|int|string
    {
        return filter_var($formValue, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE)
            ?? filter_var($formValue, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE)
            ?? $formValue;
    }

    private function stringify(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
