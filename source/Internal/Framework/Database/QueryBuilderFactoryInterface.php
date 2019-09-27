<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @internal
 */
interface QueryBuilderFactoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function create();
}
