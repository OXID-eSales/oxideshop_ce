<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

interface MetaDataSchemataProviderInterface
{
    public function getMetaDataSchemata(): array;

    public function getMetaDataSchemaForVersion(string $metaDataVersion): array;

    public function getFlippedMetaDataSchemaForVersion(string $metaDataVersion): array;
}
