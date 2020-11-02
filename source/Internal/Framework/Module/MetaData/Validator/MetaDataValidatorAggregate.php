<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Validator;

class MetaDataValidatorAggregate implements MetaDataValidatorInterface
{
    /**
     * @var MetaDataValidatorInterface[]
     */
    private $metaDataValidators;

    /**
     * @param MetaDataValidatorInterface ...$metaDataValidators
     */
    public function __construct(MetaDataValidatorInterface ...$metaDataValidators)
    {
        $this->metaDataValidators = $metaDataValidators;
    }

    public function validate(array $metaData): void
    {
        foreach ($this->metaDataValidators as $metaDataValidator) {
            $metaDataValidator->validate($metaData);
        }
    }
}
