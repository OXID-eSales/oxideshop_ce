<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Service;

use function is_string;

/**
 * @internal
 */
class MetaDataNormalizer implements MetaDataNormalizerInterface
{
    /**
     * Normalize the array aModule in metadata.php
     *
     * @param array $data
     *
     * @return array
     */
    public function normalizeData(array $data): array
    {
        $normalizedMetaData = [];
        foreach ($data as $key => $value) {
            $normalizedKey = strtolower($key);
            $normalizedValue = $this->normalizeValues($normalizedKey, $value);
            $normalizedMetaData[$normalizedKey] = $normalizedValue;
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_SETTINGS])) {
            $normalizedMetaData[MetaDataProvider::METADATA_SETTINGS] = $this->convertModuleSettingConstraintsToArray($normalizedMetaData[MetaDataProvider::METADATA_SETTINGS]);
            $normalizedMetaData[MetaDataProvider::METADATA_SETTINGS] = $this->convertBooleanSettingsToRealBoolean($normalizedMetaData[MetaDataProvider::METADATA_SETTINGS]);
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_TITLE])) {
            $normalizedMetaData = $this->normalizeMultiLanguageField($normalizedMetaData, MetaDataProvider::METADATA_TITLE);
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_DESCRIPTION])) {
            $normalizedMetaData = $this->normalizeMultiLanguageField($normalizedMetaData, MetaDataProvider::METADATA_DESCRIPTION);
        }

        return $normalizedMetaData;
    }

    /**
     * @param array $metadataModuleSettings
     * @return array
     */
    private function convertModuleSettingConstraintsToArray(array $metadataModuleSettings): array
    {
        foreach ($metadataModuleSettings as $key => $setting) {
            if (isset($setting['constraints'])) {
                $metadataModuleSettings[$key]['constraints'] = explode('|', $setting['constraints']);
            }
        }

        return $metadataModuleSettings;
    }

    /**
     * @param array $metadataModuleSettings
     * @return array
     */
    private function convertBooleanSettingsToRealBoolean(array $metadataModuleSettings): array
    {
        foreach ($metadataModuleSettings as $key => $setting) {
            if (isset($setting['type'], $setting['value']) && $setting['type'] === 'bool') {
                $metadataModuleSettings[$key]['value'] = filter_var($setting['value'], FILTER_VALIDATE_BOOLEAN);
            }
        }

        return $metadataModuleSettings;
    }

    /**
     * @param array  $normalizedMetaData
     * @param string $fieldName
     * @return array
     */
    private function normalizeMultiLanguageField(array $normalizedMetaData, string $fieldName): array
    {
        $title = $normalizedMetaData[$fieldName];

        if (is_string($title)) {
            $defaultLanguage = $normalizedMetaData[MetaDataProvider::METADATA_LANG] ?? 'en';
            $normalizedTitle = [
                $defaultLanguage => $title,
            ];
            $normalizedMetaData[$fieldName] = $normalizedTitle;
        }

        return $normalizedMetaData;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    private function normalizeValues($key, $value)
    {
        $subItemsToNormalize = [
            MetaDataProvider::METADATA_DESCRIPTION,
            MetaDataProvider::METADATA_BLOCKS,
            MetaDataProvider::METADATA_SETTINGS,
            MetaDataProvider::METADATA_FILES
        ];
        if (\is_array($value) && in_array($key, $subItemsToNormalize, true)) {
            $normalizedValue = $this->lowerCaseArrayKeysRecursive($value);
        } else {
            $normalizedValue = $value;
        }

        return $normalizedValue;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    private function lowerCaseArrayKeysRecursive(array $array): array
    {
        return array_map(
            function ($item) {
                if (\is_array($item)) {
                    $item = $this->lowerCaseArrayKeysRecursive($item);
                }

                return $item;
            },
            array_change_key_case($array)
        );
    }
}
