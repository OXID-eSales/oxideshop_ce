<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Admin\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Factory\AdminFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserService;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AdminUserServiceTest extends TestCase
{
    #[Test]
    public function createAdmin(): void
    {
        $email = uniqid();
        $password = uniqid();
        $rights = uniqid();
        $shopId = rand(1, 10);

        $adminStub = $this->createStub(Admin::class);

        $adminFactorySpy = $this->createMock(AdminFactoryInterface::class);
        $adminFactorySpy
            ->method('createAdmin')
            ->with($email, $password, $rights, $shopId)
            ->willReturn($adminStub);

        $adminDaoMock = $this->createMock(AdminDaoInterface::class);
        $adminDaoMock
            ->method('create')
            ->with($adminStub);

        $sut = $this->getAdminDao(adminDao: $adminDaoMock, adminFactory: $adminFactorySpy);

        $sut->createAdmin($email, $password, $rights, $shopId);
    }

    private function getAdminDao(
        ?AdminDaoInterface $adminDao = null,
        ?AdminFactoryInterface $adminFactory = null
    ): AdminUserServiceInterface
    {
        return new AdminUserService(
            adminDao: $adminDao ?? $this->createStub(AdminDaoInterface::class),
            adminFactory: $adminFactory ?? $this->createStub(AdminFactoryInterface::class)
        );
    }
}