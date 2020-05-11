<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Admin\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Factory\AdminFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class AdminUserService
{
    /**
     * @var AdminDaoInterface
     */
    private $adminDao;

    /**
     * @var AdminFactoryInterface
     */
    private $adminFactory;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    /**
     * AdminUserService constructor.
     *
     * @param AdminDaoInterface $adminDao
     * @param AdminFactoryInterface $adminFactory
     *
     */
    public function __construct(
        AdminDaoInterface $adminDao,
        AdminFactoryInterface $adminFactory,
        BasicContextInterface $basicContext
    ) {
        $this->adminDao = $adminDao;
        $this->adminFactory = $adminFactory;
        $this->basicContect = $basicContext;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function createAdmin(
        string $email,
        string $password,
        string $rights = Admin::MALL_ADMIN,
        int $shopId = 0
    ): void {
        if ($shopId === 0) {
            $shopId = $this->basicContext->getDefaultShopId();
        }

        $this->adminDao->create($this->adminFactory->createAdmin(
            $email,
            $password,
            $rights,
            $shopId
        ));
    }
}
