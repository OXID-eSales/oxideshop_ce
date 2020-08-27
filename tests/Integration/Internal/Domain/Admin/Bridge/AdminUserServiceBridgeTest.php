<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Admin\Bridge;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Bridge\AdminUserServiceBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\DataObject\Admin;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Service\AdminUserServiceInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class AdminUserServiceBridgeTest extends TestCase
{
    use ContainerTrait;

    private $email = 'testuser@oxideshop.dev';

    protected function tearDown(): void
    {
        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $testUser->delete($testUser->getId());

        parent::tearDown();
    }

    public function testCreateAdmin(): void
    {
        $adminUserService = $this->get(AdminUserServiceInterface::class);

        $adminUserServiceBridge = new AdminUserServiceBridge($adminUserService);

        $adminUserServiceBridge->createAdmin(
            $this->email,
            'test123',
            Admin::MALL_ADMIN,
            1
        );

        $testUser = new User();
        $testUser->load($testUser->getIdByUserName($this->email));
        $this->assertTrue($testUser->isMallAdmin());
    }
}
