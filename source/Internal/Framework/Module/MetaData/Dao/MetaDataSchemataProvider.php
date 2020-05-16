<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;

class MetaDataSchemataProvider implements MetaDataSchemataProviderInterface
{
    /**
     * @var array
     */
    private $metaDataSchemata;

    /**
     * MetaDataDefinition constructor.
     *
     * @param array $metaDataSchemata
     */
    public function __construct(array $metaDataSchemata)
    {
        $this->metaDataSchemata = $metaDataSchemata;
    }

    /**
     * @return array
     */
    public function getMetaDataSchemata(): array
    {
        return $this->metaDataSchemata;
    }

    /**
     * @param string $metaDataVersion
     *
     * @throws UnsupportedMetaDataVersionException
     *
     * @return array
     */
    public function getMetaDataSchemaForVersion(string $metaDataVersion): array
    {
        if (false === array_key_exists($metaDataVersion, $this->metaDataSchemata)) {
            throw new UnsupportedMetaDataVersionException("Metadata version $metaDataVersion is not supported");
        }

        return $this->metaDataSchemata[$metaDataVersion];
    }

    /**
     * @param string $metaDataVersion
     *
     * @throws UnsupportedMetaDataVersionException
     *
     * @return array
     */
    public function getFlippedMetaDataSchemaForVersion(string $metaDataVersion): array
    {
        if (false === array_key_exists($metaDataVersion, $this->metaDataSchemata)) {
            throw new UnsupportedMetaDataVersionException("Metadata version $metaDataVersion is not supported");
        }

        return $this->arrayFlipRecursive($this->metaDataSchemata[$metaDataVersion]);
    }

    /**
     * Recursively exchange keys and values for a given array
     *
     * @param array $metaDataVersion
     *
     * @return array
     */
    private function arrayFlipRecursive(array $metaDataVersion): array
    {
        $transposedArray = [];

        foreach ($metaDataVersion as $key => $item) {
            if (is_numeric($key) && \is_string($item)) {
                $transposedArray[$item] = $key;
            } elseif (\is_string($key) && \is_array($item)) {
                $transposedArray[$key] = $this->arrayFlipRecursive($item);
            }
        }

        return $transposedArray;
    }
}
