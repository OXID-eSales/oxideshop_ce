<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

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
