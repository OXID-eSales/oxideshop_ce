<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Application\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactory;

class NewsletterRecipients
{
    public function getNewsletterRecipients(): array
    {
        $queryBuilder = $this->getQueryBuilder();

        $queryBuilder
            ->select([
                'u.OXSAL as Salutation',
                'u.OXFNAME as Firstname',
                'u.OXLNAME as Lastname',
                'u.OXUSERNAME as Email',
                'CASE
                    WHEN n.OXDBOPTIN = 0 THEN "not subscribed"
                    WHEN n.OXDBOPTIN = 1 THEN "subscribed"
                    WHEN n.OXDBOPTIN = 2 THEN "not confirmed"
                    ELSE "not subscribed"
                END AS "Opt-In state"',
                'c.OXTITLE as Country',
                'u.OXRIGHTS as "Assigned user groups"'
            ])
            ->from('oxuser', 'u')
            ->join('u', 'oxcountry', 'c', 'u.OXCOUNTRYID=c.OXID')
            ->leftJoin('u', 'oxnewssubscribed', 'n', 'u.oxid=n.oxuserid')
            ->orderBy('u.oxcreate', 'DESC');

        return $queryBuilder->execute()->fetchAllAssociative();
    }

    private function getQueryBuilder(): QueryBuilder
    {
        $connectionProvider = new ConnectionProvider();
        $queryBuilderFactory = new QueryBuilderFactory($connectionProvider);

        return$queryBuilderFactory->create();
    }
}
