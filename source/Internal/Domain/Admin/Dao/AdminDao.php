<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\EmailAlreadyTakenException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;

class AdminDao implements AdminDaoInterface
{
    public function __construct(private QueryBuilderFactoryInterface $queryBuilderFactory)
    {
    }

    /**
     * @param Admin $admin
     * @throws EmailAlreadyTakenException
     */
    public function create(Admin $admin): void
    {
        $this->checkEmailNotTaken($admin->getEmail(), $admin->getShopId());

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
                'OXSHOPID' => $admin->getShopId(),
            ]);
        $queryBuilder->executeStatement();
    }

    /**
     * @throws EmailAlreadyTakenException
     */
    private function checkEmailNotTaken(string $email, int $shopId): void
    {
        $queryBuilder = $this->queryBuilderFactory->create();
        $queryBuilder
            ->select('1')
            ->from('oxuser')
            ->where('OXUSERNAME = :OXUSERNAME')
            ->andWhere('OXSHOPID = :OXSHOPID')
            ->setParameters([
                'OXUSERNAME' => $email,
                'OXSHOPID' => $shopId,
            ])
            ->setMaxResults(1);

        if ($queryBuilder->execute()->fetchOne()) {
            throw new EmailAlreadyTakenException("Can not create an admin, the email '$email' is already in use.");
        }
    }
}
