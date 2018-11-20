<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData;

/**
 * Class MetaDataNormalizer
 *
 * @internal
 *
 * @package OxidEsales\EshopCommunity\Internal\Module\MetaData
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
            MetaDataDataProvider::METADATA_DESCRIPTION,
            MetaDataDataProvider::METADATA_BLOCKS,
            MetaDataDataProvider::METADATA_SETTINGS
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
