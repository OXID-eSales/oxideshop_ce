<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service;

use function is_string;

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
        $normalizedMetaData = $data;
        foreach ($data as $key => $value) {
            $normalizedValue = $this->lowerCaseFileClassesNames($key, $value);
            $normalizedMetaData[$key] = $normalizedValue;
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_SETTINGS])) {
            $normalizedMetaData[MetaDataProvider::METADATA_SETTINGS] = $this->convertModuleSettingConstraintsToArray(
                $normalizedMetaData[MetaDataProvider::METADATA_SETTINGS]
            );
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_TITLE])) {
            $normalizedMetaData = $this->normalizeMultiLanguageField(
                $normalizedMetaData,
                MetaDataProvider::METADATA_TITLE
            );
        }

        if (isset($normalizedMetaData[MetaDataProvider::METADATA_DESCRIPTION])) {
            $normalizedMetaData = $this->normalizeMultiLanguageField(
                $normalizedMetaData,
                MetaDataProvider::METADATA_DESCRIPTION
            );
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
    private function lowerCaseFileClassesNames($key, $value)
    {
        $normalizedValue = $value;
        if (is_array($value) && $key === MetaDataProvider::METADATA_FILES) {
            $normalizedValue = [];
            foreach ($value as $className => $path) {
                $normalizedValue[strtolower($className)] = $path;
            }
        }

        return $normalizedValue;
    }
}
