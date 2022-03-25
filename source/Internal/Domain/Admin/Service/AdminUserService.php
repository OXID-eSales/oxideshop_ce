<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Factory\AdminFactoryInterface;

class AdminUserService implements AdminUserServiceInterface
{
    public function __construct(private AdminDaoInterface $adminDao, private AdminFactoryInterface $adminFactory)
    {
    }

    /**
     * @inheritDoc
     * @throws \InvalidArgumentException
     */
    public function createAdmin(
        string $email,
        string $password,
        string $rights,
        int $shopId
    ): void {
        $this->adminDao->create($this->adminFactory->createAdmin(
            $email,
            $password,
            $rights,
            $shopId
        ));
    }
}
