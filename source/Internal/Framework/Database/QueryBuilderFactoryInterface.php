<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
interface QueryBuilderFactoryInterface
{
    public function create(): QueryBuilder;
}
