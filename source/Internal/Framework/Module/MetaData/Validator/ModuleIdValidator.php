<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\ModuleIdNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProvider;

class ModuleIdValidator implements MetaDataValidatorInterface
{
    /**
     * @param array $metaData
     * @throws ModuleIdNotValidException
     */
    public function validate(array $metaData): void
    {
        $metaDataId = $metaData[MetaDataProvider::METADATA_ID] ?? '';
        if ($metaDataId === '') {
            throw new ModuleIdNotValidException('Module ID is not provided in metadata file.');
        }
    }
}
