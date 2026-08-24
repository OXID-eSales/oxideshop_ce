<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserComponentTest extends IntegrationTestCase
{
    private string $userName = 'some-users-email@example.com';
    private string $password = 'password123';


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

    public function testChangeUserWithoutOrderRemark(): void
    {
        $_POST = $this->getUserFormData();
        $this->getUserComponent()->createUser();

        $this->getUserComponent()->changeUser();

        $this->assertFalse(Registry::getSession()->hasVariable('ordrem'));
    }

    public function testChangeUserWithOrderRemark(): void
    {
        $_POST = $this->getUserFormData();
        $this->getUserComponent()->createUser();

        $orderRemark = 'Some order remark';
        $_POST['order_remark'] = $orderRemark;

        $this->getUserComponent()->changeUser();

        $this->assertEquals($orderRemark, Registry::getSession()->getVariable('ordrem'));
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

    public function testChangeUserDoesNotUpdateAddressOwnedByAnotherUser(): void
    {
        $this->userName = 'address-owner@example.com';
        $this->createUser($this->userName);
        $ownerId = $this->fetchUserData()['OXID'];

        $foreignAddressId = 'foreignaddressid01';
        $this->createAddressForUser($foreignAddressId, $ownerId);

        $this->userName = 'other-user@example.com';
        $this->createUser($this->userName);
        $otherUserId = $this->fetchUserData()['OXID'];

        $requestData = $this->getUserFormData();
        $requestData['blshowshipaddress'] = 1;
        $requestData['oxaddressid'] = $foreignAddressId;
        $_POST = $requestData;

        $this->getUserComponent()->changeUser();

        $this->assertEquals($ownerId, $this->fetchAddressUserId($foreignAddressId));
        $this->assertNotEquals($otherUserId, $this->fetchAddressUserId($foreignAddressId));
    }

    public function testChangeUserEmailValidation(): void
    {
        $existingUser = $this->createUser($this->userName);
        $guestUser = $this->createUser('guest@example.com', true);
        $secondUser = $this->createUser('second-user@example.com');

        $this->assertEmailChangeRejected($secondUser['email'], $existingUser['email']);
        $this->assertEmailChangeRejected($secondUser['email'], $guestUser['email']);
        $this->assertEmailChangeAccepted($secondUser['email'], 'new-unique-email@example.com');
    }

    private function createUser(string $email, bool $isGuest = false): array
    {
        $userData = $this->getUserFormData();

        if ($isGuest) {
            unset($userData['lgn_pwd'], $userData['lgn_pwd2']);
        }

        $userData['oxuser__oxusername'] = $email;
        $userData['lgn_usr'] = $email;
        $_POST = $userData;

        $this->getUserComponent()->createUser();

        return [
            'email' => $email
        ];
    }

    private function assertEmailChangeRejected(string $currentEmail, string $newEmail): void
    {
        $this->userName = $currentEmail;

        $requestData = $this->prepareChangeUserRequest($newEmail);
        $_POST = $requestData;

        $result = $this->getUserComponent()->changeuser_testvalues();

        $this->assertNull($result);
        $userData = $this->fetchUserData();
        $this->assertEquals($currentEmail, $userData['OXUSERNAME']);
    }

    private function assertEmailChangeAccepted(string $currentEmail, string $newEmail): void
    {
        $this->userName = $currentEmail;

        $requestData = $this->prepareChangeUserRequest($newEmail);
        $_POST = $requestData;

        $result = $this->getUserComponent()->changeuser_testvalues();

        $this->assertEquals('account_user', $result);
        $this->userName = $newEmail;
        $userData = $this->fetchUserData();
        $this->assertEquals($newEmail, $userData['OXUSERNAME']);
    }

    private function prepareChangeUserRequest(string $newEmail): array
    {
        $requestData = $this->getUserFormData();
        $requestData['invadr']['oxuser__oxusername'] = $newEmail;
        return $requestData;
    }

    private function createAddressForUser(string $addressId, string $userId): void
    {
        $address = oxNew(Address::class);
        $address->setId($addressId);
        $address->assign([
            'oxaddress__oxfname' => uniqid('owner-first-name-', true),
            'oxaddress__oxlname' => uniqid('owner-last-name-', true),
            'oxaddress__oxstreet' => uniqid('owner-street-', true),
            'oxaddress__oxstreetnr' => 1,
            'oxaddress__oxzip' => 12345,
            'oxaddress__oxcity' => 'Freiburg',
            'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984',
        ]);
        $address->oxaddress__oxuserid = new Field($userId, Field::T_RAW);
        $address->save();
    }

    private function fetchAddressUserId(string $addressId): string
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();

        return (string) $queryBuilder
            ->select('oxuserid')
            ->from('oxaddress')
            ->where('oxid = :oxid')
            ->setParameter('oxid', $addressId)
            ->execute()
            ->fetchOne();
    }

    private function mockSession(): void
    {
        $sessionMock = $this->createPartialMock(Session::class, ['checkSessionChallenge']);
        $sessionMock
            ->expects($this->atLeastOnce())
            ->method('checkSessionChallenge')
            ->willReturn(true);
        Registry::set(Session::class, $sessionMock);
    }

    private function getUserFormData(): array
    {
        return [
            'oxuser__oxfname' => uniqid('first-name-', true),
            'oxuser__oxlname' => uniqid('last-name-', true),
            'oxuser__oxusername' => $this->userName,
            'lgn_usr' => $this->userName,
            'lgn_pwd' => $this->password,
            'lgn_pwd2' => $this->password,
            'user_password' => $this->password,
            'invadr' => [
                'oxuser__oxfname' => uniqid('first-name-', true),
                'oxuser__oxlname' => uniqid('last-name-', true),
                'oxuser__oxstreet' => uniqid('street-', true),
                'oxuser__oxstreetnr' => 123,
                'oxuser__oxzip' => 123,
                'oxuser__oxcity' => 'Freiburg',
                'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
            ],
            'deladr' => [
                'oxaddress__oxfname' => uniqid('del-first-name-', true),
                'oxaddress__oxlname' => uniqid('del-last-name-', true),
                'oxaddress__oxstreet' => uniqid('del-street-', true),
                'oxaddress__oxstreetnr' => 123,
                'oxaddress__oxzip' => 123,
                'oxaddress__oxcity' => 'Freiburg',
                'oxaddress__oxcountryid' => 'a7c40f631fc920687.20179984',
            ]
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
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();

        return $queryBuilder
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
