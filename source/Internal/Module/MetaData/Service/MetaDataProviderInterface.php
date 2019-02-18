<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData\Service;

use OxidEsales\EshopCommunity\Internal\Module\MetaData\Exception\InvalidMetaDataException;

/**
 * @internal
 */
interface MetaDataProviderInterface
{
    /**
     * @param string $filePath
     *
     * @return array
     * @throws InvalidMetaDataException
     */
    public function getData(string $filePath): array;
}
