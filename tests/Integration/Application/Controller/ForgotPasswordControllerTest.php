<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller;

use OxidEsales\Eshop\Application\Controller\ForgotPasswordController;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ForgotPasswordControllerTest extends IntegrationTestCase
{
    private string $userId = 'test_forgot_password_user_id';
    private string $userName = 'forgot-password@example.com';
    private string $newUserName = 'forgot-password-new@example.com';
    private string $newPassword = 'NewPassw0rd!x';

    public function tearDown(): void
    {
        unset($_SESSION['usr'], $_SESSION['Errors']);
        parent::tearDown();
    }

    public function testUpdatePasswordWithLinkIssuedBeforeEmailChangeIsRejected(): void
    {
        $this->createUser();
        $resetId = $this->loadUser()->getUpdateId();
        $this->assertFalse(oxNew(User::class)->isExpiredUpdateId($resetId));
        $oldPasswordHash = $this->loadUser()->getFieldData('oxpassword');
        $user = $this->loadUser();
        $user->oxuser__oxusername = new Field($this->newUserName);
        $user->save();
        $_POST['uid'] = $resetId;
        $_POST['password_new'] = $this->newPassword;
        $_POST['password_new_confirm'] = $this->newPassword;
        $controller = oxNew(ForgotPasswordController::class);
        $isExpiredLink = $controller->isExpiredLink();

        $result = $controller->updatePassword();

        $this->assertTrue($isExpiredLink);
        $this->assertNull($result);
        $this->assertStringContainsString(
            'ERROR_MESSAGE_PASSWORD_LINK_EXPIRED',
            serialize(Registry::getSession()->getVariable('Errors'))
        );
        $this->assertSame($oldPasswordHash, $this->loadUser()->getFieldData('oxpassword'));
        $this->assertNotSame($this->userId, Registry::getSession()->getVariable('usr'));
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
