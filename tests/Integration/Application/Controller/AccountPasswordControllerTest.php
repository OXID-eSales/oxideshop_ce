<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\AccountPasswordController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class AccountPasswordControllerTest extends IntegrationTestCase
{
    private string $userId = 'test_account_password_user_id';
    private string $userName = 'account-password@example.com';
    private string $password = 'some-pass';
    private string $newPassword = 'NewPassw0rd!x';

    public function tearDown(): void
    {
        unset($_SESSION['usr'], $_SESSION['login-token'], $_SESSION['sess_stoken'], $_SESSION['Errors']);
        parent::tearDown();
    }

    public function testChangePasswordInvalidatesPasswordResetLink(): void
    {
        $this->createUser();
        $resetId = $this->loadUser()->getUpdateId();
        $this->assertFalse(oxNew(User::class)->isExpiredUpdateId($resetId));
        $oldPasswordHash = $this->loadUser()->getFieldData('oxpassword');
        $this->loginUser();
        $_POST['password_old'] = $this->password;
        $_POST['password_new'] = $this->newPassword;
        $_POST['password_new_confirm'] = $this->newPassword;
        $controller = oxNew(AccountPasswordController::class);

        $controller->changePassword();

        $this->assertTrue($controller->isPasswordChanged());
        $this->assertNotSame($oldPasswordHash, $this->loadUser()->getFieldData('oxpassword'));
        $this->assertTrue(oxNew(User::class)->isExpiredUpdateId($resetId));
    }

    private function loginUser(): void
    {
        oxNew(User::class)->login($this->userName, $this->password);
        $_POST['stoken'] = Registry::getSession()->getSessionChallengeToken();
    }

    private function createUser(): void
    {
        $user = oxNew(User::class);
        $user->setId($this->userId);
        $user->oxuser__oxusername = new Field($this->userName);
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->setPassword($this->password);
        $user->save();
    }

    private function loadUser(): User
    {
        $user = oxNew(User::class);
        $user->load($this->userId);

        return $user;
    }
}
