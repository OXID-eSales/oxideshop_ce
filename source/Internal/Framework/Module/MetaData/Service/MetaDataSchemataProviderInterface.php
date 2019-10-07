<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Service;

interface MetaDataSchemataProviderInterface
{
    /**
     * @return array
     */
    public function getMetaDataSchemata(): array;

    /**
     * @param string $metaDataVersion
     *
     * @return array
     */
    public function getMetaDataSchemaForVersion(string $metaDataVersion): array;

    /**
     * @param string $metaDataVersion
     *
     * @return array
     */
    public function getFlippedMetaDataSchemaForVersion(string $metaDataVersion): array;
}
