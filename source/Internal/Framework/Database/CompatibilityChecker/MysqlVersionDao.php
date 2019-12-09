<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker;

use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class MysqlVersionDao implements DatabaseVersionDaoInterface
{
    /** @var QueryBuilderFactoryInterface */
    private $queryBuilderFactory;

    /** @param QueryBuilderFactoryInterface $queryBuilderFactory */
    public function __construct(QueryBuilderFactoryInterface $queryBuilderFactory)
    {
        $this->queryBuilderFactory = $queryBuilderFactory;
    }

    /** @return string */
    public function getVersion(): string
    {
        $result = $this->queryBuilderFactory->create()
            ->select('@@version as version')
            ->setMaxResults(1)
            ->execute()
            ->fetch();

        return $result['version'];
    }
}
