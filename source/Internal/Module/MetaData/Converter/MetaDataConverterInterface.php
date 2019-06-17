<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Converter;

/**
 * @internal
 */
interface MetaDataConverterInterface
{
    public function convert(array $metaData): array;
}
