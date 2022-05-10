<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataObject\MetaDataSchema;

interface MetaDataSchemaDaoInterface
{
    /**
     * @param string $metaDataVersion
     * @return MetaDataSchema
     */
    public function get(string $metaDataVersion): MetaDataSchema;
}
