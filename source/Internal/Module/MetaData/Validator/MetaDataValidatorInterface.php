<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator;

/**
 * @internal
 */
interface MetaDataValidatorInterface
{
    public function validate(array $metaData);
}
