<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;

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
