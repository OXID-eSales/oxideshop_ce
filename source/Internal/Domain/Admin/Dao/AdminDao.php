<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\UserNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class AdminDao implements AdminDaoInterface
{
    public function __construct(private QueryBuilderFactoryInterface $queryBuilderFactory)
    {
    }

    /**
     * @param Admin $admin
     */
    public function create(Admin $admin): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->insert('oxuser')
            ->values([
                'OXID'        => ':OXID',
                'OXUSERNAME'  => ':OXUSERNAME',
                'OXPASSWORD'  => ':OXPASSWORD',
                'OXRIGHTS'    => ':OXRIGHTS',
                'OXSHOPID'    => ':OXSHOPID',
            ])
            ->setParameters([
                'OXID' => $admin->getId(),
                'OXUSERNAME' => $admin->getEmail(),
                'OXPASSWORD' => $admin->getPasswordHash(),
                'OXRIGHTS' => $admin->getRights(),
                'OXSHOPID' => $admin->getShopId()
            ]);
        $queryBuilder->execute();
    }
}
