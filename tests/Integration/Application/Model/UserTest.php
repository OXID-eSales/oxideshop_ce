<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Application\Model\Address;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\UserException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserTest extends IntegrationTestCase
{
    private const USER_ID = 'test_user_id';
    private const USER_NAME = 'test_user@example.com';
    private const ADDRESS_ID = 'test_address_id';
    private const COUNTRY_ID = 'a7c40f631fc920687.20179984';

    public function tearDown(): void
    {
        unset($_SESSION['deladrid']);
        parent::tearDown();
    }

    public function testSetPassword(): void
    {
        $password = 'some-pass';
        $user = oxNew(User::class);

        $user->setPassword($password);

        $this->assertTrue(
            $this->get(PasswordServiceBridgeInterface::class)
                ->verifyPassword(
                    $password,
                    $user->getFieldData('oxpassword')
                )
        );
        $this->assertEmpty($user->getFieldData('oxpasssalt'));
    }

    public function testSetPasswordWithEmptyPass(): void
    {
        $user = oxNew(User::class);

        $user->setPassword('');

        $this->assertEmpty($user->getFieldData('oxpassword'));
        $this->assertEmpty($user->getFieldData('oxpasssalt'));
    }

    public function testChangeUserDataUpdatesOwnDeliveryAddress(): void
    {
        $this->createUser();
        $this->createAddress(self::USER_ID);
        $_POST['oxaddressid'] = self::ADDRESS_ID;

        $this->changeUserData([
            'oxaddress__oxid' => 'client_supplied_id',
            'oxaddress__oxuserid' => 'client_supplied_owner',
            'oxaddress__oxcity' => 'Updated city',
        ]);

        $address = $this->loadAddress(self::ADDRESS_ID);
        $this->assertSame(self::USER_ID, $address->getFieldData('oxuserid'));
        $this->assertSame('Updated city', $address->getFieldData('oxcity'));
        $this->assertSame('Original company', $address->getFieldData('oxcompany'));
        $this->assertFalse(oxNew(Address::class)->load('client_supplied_id'));
        $this->assertSame(self::ADDRESS_ID, Registry::getSession()->getVariable('deladrid'));
    }

    public function testChangeUserDataRejectsForeignDeliveryAddress(): void
    {
        $this->createUser();
        $this->createAddress('test_other_user_id');
        Registry::getSession()->setVariable('deladrid', 'test_previous_selection');
        $_POST['oxaddressid'] = self::ADDRESS_ID;

        try {
            $this->changeUserData(['oxaddress__oxcity' => 'Updated city']);
        } catch (UserException) {
            $address = $this->loadAddress(self::ADDRESS_ID);
            $this->assertSame('test_other_user_id', $address->getFieldData('oxuserid'));
            $this->assertSame('Original city', $address->getFieldData('oxcity'));
            $this->assertSame('Original city', $this->loadUser()->getFieldData('oxcity'));
            $this->assertSame('test_previous_selection', Registry::getSession()->getVariable('deladrid'));
            return;
        }

        $this->fail('Foreign delivery address must be rejected');
    }

    private function changeUserData(array $deliveryAddress): void
    {
        $user = $this->loadUser();
        $password = $user->getFieldData('oxpassword');

        $user->changeUserData(
            self::USER_NAME,
            $password,
            $password,
            [
                'oxuser__oxusername' => self::USER_NAME,
                'oxuser__oxfname' => 'First name',
                'oxuser__oxlname' => 'Last name',
                'oxuser__oxstreet' => 'Street',
                'oxuser__oxstreetnr' => '1',
                'oxuser__oxzip' => '12345',
                'oxuser__oxcity' => 'Updated billing city',
                'oxuser__oxcountryid' => self::COUNTRY_ID,
            ],
            array_merge([
                'oxaddress__oxfname' => 'First name',
                'oxaddress__oxlname' => 'Last name',
                'oxaddress__oxstreet' => 'Street',
                'oxaddress__oxstreetnr' => '1',
                'oxaddress__oxzip' => '12345',
                'oxaddress__oxcity' => 'City',
                'oxaddress__oxcountryid' => self::COUNTRY_ID,
            ], $deliveryAddress)
        );
    }

    private function createUser(): void
    {
        $user = oxNew(User::class);
        $user->setId(self::USER_ID);
        $user->oxuser__oxusername = new Field(self::USER_NAME);
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxcity = new Field('Original city');
        $user->setPassword('some-pass');
        $user->save();
    }

    private function createAddress(string $userId): void
    {
        $address = oxNew(Address::class);
        $address->setId(self::ADDRESS_ID);
        $address->oxaddress__oxuserid = new Field($userId);
        $address->oxaddress__oxcity = new Field('Original city');
        $address->oxaddress__oxcompany = new Field('Original company');
        $address->save();
    }

    private function loadUser(): User
    {
        $user = oxNew(User::class);
        $user->load(self::USER_ID);

        return $user;
    }

    private function loadAddress(string $addressId): Address
    {
        $address = oxNew(Address::class);
        $address->load($addressId);

        return $address;
    }
}
