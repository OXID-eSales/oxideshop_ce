<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Admin\Service;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class AdminUserServiceTest extends TestCase
{
    use ContainerTrait;

    private string $email = 'testuser@oxideshop.dev';

    public function tearDown(): void
    {
        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));

        $testUser->delete($testUser->getId());

        parent::tearDown();
    }

    public function testCreateMallAdmin(): void
    {
        $adminUserService = $this->get(AdminUserServiceInterface::class);

        $adminUserService->createAdmin(
            $this->email,
            'test123',
            Admin::MALL_ADMIN,
            1
        );

        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $this->assertTrue($testUser->isMallAdmin());
    }

    public function testCreateAdmin(): void
    {
        $adminUserService = $this->get(AdminUserServiceInterface::class);

        $adminUserService->createAdmin(
            $this->email,
            'test123',
            '1',
            1
        );

        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $this->assertFalse($testUser->isMallAdmin());
        $this->assertEquals(1, $testUser->oxuser__oxrights->value);
    }
}
