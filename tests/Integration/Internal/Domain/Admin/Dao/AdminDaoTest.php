<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Admin\Dao;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDao;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Dao\AdminDaoInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\EmailAlreadyTakenException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\Test;

final class AdminDaoTest extends IntegrationTestCase
{
    #[Test]
    public function testCreateSucceedsWhenEmailIsUnique(): void
    {
        $admin = new Admin(
            id: uniqid(),
            email: $this->generateEmail(),
            passwordHash: md5(uniqid()),
            rights: uniqid(),
            shopId: rand(1, 10),
        );

        $this->getAdminDao()->create($admin);

        $this->assertTrue(true);
    }

    #[Test]
    public function testCreateThrowsExceptionIfEmailAlreadyExists(): void
    {
        $email = $this->generateEmail();
        $shopId = rand(1, 10);

        $admin = new Admin(
            id: uniqid(),
            email: $email,
            passwordHash: md5(uniqid()),
            rights: uniqid(),
            shopId: $shopId,
        );

        $this->getAdminDao()->create($admin);

        $this->expectException(EmailAlreadyTakenException::class);

        $this->getAdminDao()->create($admin);
    }

    private function generateEmail(): string
    {
        return sprintf('%s@%s.com', uniqid(), uniqid());
    }

    private function getAdminDao(): AdminDaoInterface
    {
        return new AdminDao(
            queryBuilderFactory: $this->get(QueryBuilderFactoryInterface::class)
        );
    }
}