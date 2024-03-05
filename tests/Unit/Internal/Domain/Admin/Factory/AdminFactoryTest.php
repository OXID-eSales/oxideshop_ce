<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Domain\Admin\Factory;

use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidEmailException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidRightsException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidShopException;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Factory\AdminFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorService;
use OxidEsales\EshopCommunity\Internal\Utility\Hash\Service\PasswordHashServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

final class AdminFactoryTest extends TestCase
{
    use ProphecyTrait;

    private $adminFactory;

    private string $password = 'test123';

    private function createAdminFactory(): AdminFactory
    {
        $shopAdapter = $this->prophesize(ShopAdapterInterface::class);
        $shopAdapter->willImplement(ShopAdapterInterface::class);
        $shopAdapter->validateShopId(1)->willReturn(true);
        $shopAdapter->generateUniqueId()->willReturn(uniqid());

        $passwordHashService = $this->prophesize(PasswordHashServiceInterface::class);
        $passwordHashService->hash($this->password)->willReturn(md5($this->password));

        return new AdminFactory(
            $shopAdapter->reveal(),
            new EmailValidatorService(),
            $passwordHashService->reveal()
        );
    }

    public function testCreateAdmin(): void
    {
        $adminFactory = $this->createAdminFactory();

        $admin = $adminFactory->createAdmin(
            'testuser@oxideshop.dev',
            $this->password,
            Admin::MALL_ADMIN,
            1
        );

        $this->assertInstanceOf(
            Admin::class,
            $admin
        );

        $this->assertNotEquals($this->password, $admin->getPasswordHash());
    }

    public function testFailedCreate(): void
    {
        $adminFactory = $this->createAdminFactory();

        $this->expectException(InvalidEmailException::class);

        $adminFactory->createAdmin(
            'testuser',
            $this->password,
            Admin::MALL_ADMIN,
            1
        );

        $this->expectException(InvalidRightsException::class);

        $adminFactory->createAdmin(
            'testuser@oxideshop.dev',
            $this->password,
            'admin',
            1
        );

        $this->expectException(InvalidShopException::class);

        $adminFactory->createAdmin(
            'testuser@oxideshop.dev',
            $this->password,
            Admin::MALL_ADMIN,
            12
        );
    }
}
