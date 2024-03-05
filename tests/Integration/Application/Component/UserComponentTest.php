<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserComponentTest extends IntegrationTestCase
{
    private string $userName = 'some-users-email@example.com';

    public function setUp(): void
    {
        parent::setUp();

        $this->mockSession();
        Registry::getConfig()->reinitialize();
    }

    public function testCreateUserWillActivateUserAutomatically(): void
    {
        $_POST = $this->getUserFormData();

        $this->getUserComponent()->createUser();

        $this->assertNotEmpty($this->fetchUserData()['OXACTIVE']);
    }

    public function testCreateUserWithPrivateSalesWillNotActivateUserAutomatically(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', true);
        $_POST = $this->getUserFormData();

        $this->getUserComponent()->createUser();

        $this->assertEmpty($this->fetchUserData()['OXACTIVE']);
    }

    public function testCreateUserWithMissingBillingAddressData(): void
    {
        $requestData = $this->getUserFormData();
        unset($requestData['invadr']);
        $_POST = $requestData;

        $return = $this->getUserComponent()->createUser();

        $this->assertFalse($return);
    }

    public function testCreateUserWithPrivateSalesAndExtraFormDataWillNotUpdateUserStatus(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', true);
        $requestData = $this->getUserFormData();
        $requestData['invadr']['oxuser__oxactive'] = 1;
        $_POST = $requestData;

        $this->getUserComponent()->createUser();

        $this->assertEmpty($this->fetchUserData()['OXACTIVE']);
    }

    public function testCreateUserWithExtraFormDataWillNotUpdateNonAddressUserFields(): void
    {
        $wrongShopId = 123;
        $wrongUserRights = 'admin';
        $wrongCustomerNumber = 12345;
        $wrongPassword = uniqid('some-pass-', true);
        $wrongPasswordSalt = uniqid('some-pass-salt-', true);
        $wrongTimestamp = '2001-01-01';
        $wrongUpdateExpiration = 123;
        $requestData = $this->getUserFormData();
        $requestData['invadr']['oxuser__oxshopid'] = $wrongShopId;
        $requestData['invadr']['oxuser__oxrights'] = $wrongUserRights;
        $requestData['invadr']['oxuser__oxcustnr'] = $wrongCustomerNumber;
        $requestData['invadr']['oxuser__oxpassword'] = $wrongPassword;
        $requestData['invadr']['oxuser__oxpasssalt'] = $wrongPasswordSalt;
        $requestData['invadr']['oxuser__oxcreate'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxregister'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxupdatekey'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxupdateexp'] = $wrongUpdateExpiration;
        $_POST = $requestData;

        $this->getUserComponent()->createUser();

        $userData = $this->fetchUserData();
        $this->assertNotEquals($wrongShopId, $userData['OXSHOPID']);
        $this->assertNotEquals($wrongUserRights, $userData['OXRIGHTS']);
        $this->assertNotEquals($wrongUserRights, $userData['OXCUSTNR']);
        $this->assertNotEquals($wrongPassword, $userData['OXPASSWORD']);
        $this->assertNotEquals($wrongPasswordSalt, $userData['OXPASSSALT']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXCREATE']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXREGISTER']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXUPDATEKEY']);
        $this->assertNotEquals($wrongUpdateExpiration, $userData['OXUPDATEEXP']);
    }

    public function testChangeUserWithMissingBillingAddressData(): void
    {
        $_POST = $this->getUserFormData();
        $this->getUserComponent()->createUser();

        $requestData = $this->getUserFormData();
        unset($requestData['invadr']);
        $_POST = $requestData;

        $return = $this->getUserComponent()->changeUser();

        $this->assertFalse($return);
    }

    public function testChangeUserWithExtraFormDataWillNotUpdateNonAddressUserFields(): void
    {
        $_POST = $this->getUserFormData();
        $this->getUserComponent()->createUser();

        $wrongShopId = 123;
        $wrongUserRights = 'admin';
        $wrongCustomerNumber = 12345;
        $wrongPassword = uniqid('some-pass-', true);
        $wrongPasswordSalt = uniqid('some-pass-salt-', true);
        $wrongTimestamp = '2001-01-01';
        $wrongUpdateExpiration = 123;
        $requestData = $this->getUserFormData();
        $requestData['invadr']['oxuser__oxshopid'] = $wrongShopId;
        $requestData['invadr']['oxuser__oxrights'] = $wrongUserRights;
        $requestData['invadr']['oxuser__oxcustnr'] = $wrongCustomerNumber;
        $requestData['invadr']['oxuser__oxpassword'] = $wrongPassword;
        $requestData['invadr']['oxuser__oxpasssalt'] = $wrongPasswordSalt;
        $requestData['invadr']['oxuser__oxcreate'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxregister'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxupdatekey'] = $wrongTimestamp;
        $requestData['invadr']['oxuser__oxupdateexp'] = $wrongUpdateExpiration;
        $_POST = $requestData;

        $this->getUserComponent()->changeUser();

        $userData = $this->fetchUserData();
        $this->assertNotEquals($wrongShopId, $userData['OXSHOPID']);
        $this->assertNotEquals($wrongUserRights, $userData['OXRIGHTS']);
        $this->assertNotEquals($wrongUserRights, $userData['OXCUSTNR']);
        $this->assertNotEquals($wrongPassword, $userData['OXPASSWORD']);
        $this->assertNotEquals($wrongPasswordSalt, $userData['OXPASSSALT']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXCREATE']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXREGISTER']);
        $this->assertNotEquals($wrongTimestamp, $userData['OXUPDATEKEY']);
        $this->assertNotEquals($wrongUpdateExpiration, $userData['OXUPDATEEXP']);
    }

    private function mockSession(): void
    {
        $sessionMock = $this->createPartialMock(Session::class, ['checkSessionChallenge']);
        $sessionMock
            ->method('checkSessionChallenge')
            ->willReturn(true);
        Registry::set(Session::class, $sessionMock);
    }

    private function getUserFormData(): array
    {
        $password = uniqid('some-string-', true);

        return [
            'oxuser__oxfname' => uniqid('first-name-', true),
            'oxuser__oxlname' => uniqid('last-name-', true),
            'oxuser__oxusername' => $this->userName,
            'lgn_usr' => $this->userName,
            'lgn_pwd' => $password,
            'lgn_pwd2' => $password,
            'invadr' => [
                'oxuser__oxfname' => uniqid('first-name-', true),
                'oxuser__oxlname' => uniqid('last-name-', true),
                'oxuser__oxstreet' => uniqid('street-', true),
                'oxuser__oxstreetnr' => 123,
                'oxuser__oxzip' => 123,
                'oxuser__oxcity' => 'Freiburg',
                'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
            ],
        ];
    }

    private function getUserComponent(): UserComponent
    {
        $userComponent = oxNew(UserComponent::class);
        $userComponent->setParent(oxNew(FrontendController::class));
        return $userComponent;
    }

    private function fetchUserData(): array
    {
        return $this
            ->getDbConnection()
            ->createQueryBuilder()
            ->select('*')
            ->from('oxuser')
            ->where('oxusername = :oxusername')
            ->setParameters([
                ':oxusername' => $this->userName,
            ])
            ->execute()
            ->fetchAssociative();
    }
}
