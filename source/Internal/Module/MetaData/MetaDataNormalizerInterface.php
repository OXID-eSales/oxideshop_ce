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
interface MetaDataNormalizerInterface
{
    /**
     * Normalize the array aModule in metadata.php
     *
     * @param array $data
     *
     * @return array
     */
    public function normalizeData(array $data): array;
}
