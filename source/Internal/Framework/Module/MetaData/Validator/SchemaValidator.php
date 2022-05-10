<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataKeyException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException;

class SchemaValidator implements MetaDataValidatorInterface
{
    private MetaDataSchemaValidatorInterface $metaDataSchemaValidator;

    public function __construct(
        MetaDataSchemaValidatorInterface $metaDataSchemaValidator
    ) {
        $this->metaDataSchemaValidator = $metaDataSchemaValidator;
    }

    /**
     * @inheritDoc
     * @throws UnsupportedMetaDataValueTypeException
     * @throws UnsupportedMetaDataKeyException
     */
    public function validate(array $metaData): void
    {
        $this->metaDataSchemaValidator->validate(
            '',
            $metaData[MetaDataProvider::METADATA_METADATA_VERSION],
            $metaData[MetaDataProvider::METADATA_MODULE_DATA]
        );
    }
}
