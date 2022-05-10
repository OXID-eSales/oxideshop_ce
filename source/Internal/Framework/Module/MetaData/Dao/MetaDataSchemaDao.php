<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataObject\MetaDataSchema;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataVersionException;

class MetaDataSchemaDao implements MetaDataSchemaDaoInterface
{
    public function __construct(
        private array $supportedMetadataSchemas,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function get(string $metaDataVersion): MetaDataSchema
    {
        $this->validateVersion($metaDataVersion);

        $metaDataSchema = new MetaDataSchema();
        $keys = [];
        $sections = [];
        foreach ($this->supportedMetadataSchemas[$metaDataVersion] as $key => $value) {
            $keys[] = is_int($key) ? $value : $key;
            if (\is_string($key)) {
                $sections[$key] = $value;
            }
        }
        $metaDataSchema->setKeys($keys);
        $metaDataSchema->setSections($sections);

        return $metaDataSchema;
    }

    /**
     * @param string $metaDataVersion
     * @return void
     * @throws UnsupportedMetaDataVersionException
     */
    private function validateVersion(string $metaDataVersion): void
    {
        if (!\array_key_exists($metaDataVersion, $this->supportedMetadataSchemas)) {
            throw new UnsupportedMetaDataVersionException(
                sprintf(
                    'Metadata version %s is not supported',
                    $metaDataVersion
                )
            );
        }
    }
}
