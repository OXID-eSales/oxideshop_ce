<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\UserMain;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserMainTest extends IntegrationTestCase
{
    private string $userId = 'test_user_main_user_id';
    private string $userName = 'user-main@example.com';
    private string $newPassword = 'NewPassw0rd!x';

    public function setUp(): void
    {
        parent::setUp();

        Registry::getConfig()->setAdminMode(true);
    }

    public function tearDown(): void
    {
        Registry::getConfig()->setAdminMode(false);

        parent::tearDown();
    }

    public function testSaveWithNewPasswordInvalidatesPasswordResetLink(): void
    {
        $this->createUser();
        $resetId = $this->loadUser()->getUpdateId();
        $this->assertFalse(oxNew(User::class)->isExpiredUpdateId($resetId));
        $oldPasswordHash = $this->loadUser()->getFieldData('oxpassword');
        $this->prepareRequest();
        $_POST['newPassword'] = $this->newPassword;

        oxNew(UserMain::class)->save();

        $this->assertNotSame($oldPasswordHash, $this->loadUser()->getFieldData('oxpassword'));
        $this->assertTrue(oxNew(User::class)->isExpiredUpdateId($resetId));
    }

    public function testSaveWithoutNewPasswordKeepsPasswordResetLink(): void
    {
        $this->createUser();
        $resetId = $this->loadUser()->getUpdateId();
        $this->prepareRequest();

        oxNew(UserMain::class)->save();

        $this->assertFalse(oxNew(User::class)->isExpiredUpdateId($resetId));
    }

    private function prepareRequest(): void
    {
        $_POST['oxid'] = $this->userId;
        $_POST['editval'] = [
            'oxuser__oxusername' => $this->userName,
            'oxuser__oxactive' => 1,
        ];
    }

    private function createUser(): void
    {
        $user = oxNew(User::class);
        $user->setId($this->userId);
        $user->oxuser__oxusername = new Field($this->userName);
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->setPassword('some-pass');
        $user->save();
    }

    private function loadUser(): User
    {
        $user = oxNew(User::class);
        $user->load($this->userId);

        return $user;
    }
}
