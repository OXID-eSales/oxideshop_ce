<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model;

use OxidEsales\Eshop\Core\Exception\UserException;

/**
 * Class UserTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Application\Model
 *
 * @covers  \OxidEsales\EshopCommunity\Application\Model\User
 */
class UserTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test, that during the deletion of a user all user data is deleted due to transaction commit.
     */
    public function testUserDeletionTransactionCommit()
    {
        $userId = 'testUserIdDeletionTransactionC';
        $addressId = 'testAddrIdDeletionTransactionC';
        $this->addUserData($userId, $addressId);
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if (false === $user->load($userId)) {
            $this->fail('User cannot be loaded: ' . $userId);
        }

        $user->delete();

        $this->assertUserDataDeletedAfterTransactionCommit(
            $userId,
            $addressId
        );
    }

    /**
     * Test, that during the deletion of a user no user data is deleted due to transaction rollback.
     */
    public function testUserDeletionTransactionRollback()
    {
        $userId = 'testUserDeletionTransactionR';
        $addressId = 'testAddrIdDeletionTransactionR';
        $this->addUserData($userId, $addressId);
        $userMock = $this->getMockBuilder(\OxidEsales\Eshop\Application\Model\User::class)
            ->setMethods(['deleteAdditionally'])
            ->getMock();
        $userMock->expects($this->any())
            ->method('deleteAdditionally')
            // Use an exception, which is most probably not thrown in real code
            ->will($this->throwException(new \OverflowException()));
        if (false === $userMock->load($userId)) {
            $this->fail('User cannot be loaded: ' . $userId);
        }

        $exceptionThrown = false;
        try {
            $userMock->delete();
        } catch (\OverflowException $exception) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown, 'An exception has been thrown');
        $this->assertUserDataNotDeletedAfterTransactionRollback(
            $userId,
            $addressId
        );
    }

    /**
     * Assert that user data is deleted after transaction commit
     *
     * @param string $userId Id of the user to be deleted
     * @param string $addressId Id of the address to be delete
     */
    private function assertUserDataDeletedAfterTransactionCommit($userId, $addressId)
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $userAddress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);

        $this->assertFalse($user->load($userId), 'User has been deleted');
        $this->assertFalse($userAddress->load($addressId), 'User address has been deleted');
    }

    /**
     * Assert that user data is NOT deleted after transaction rollback
     *
     * @param string $userId Id of the user to not be deleted
     * @param string $addressId Id of the address not to be delete
     */
    private function assertUserDataNotDeletedAfterTransactionRollback($userId, $addressId)
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $userAddress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);

        $this->assertTrue($user->load($userId), 'User has not been deleted');
        $this->assertTrue($userAddress->load($addressId), 'User address has not been deleted');
    }

    /**
     * @param string $userId Id of the user to be added
     */
    /**
     * @param string $userId Id of the user to be added
     * @param string $addressId Id of the address to be added
     */
    private function addUserData($userId, $addressId)
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->setId($userId);
        if (!$user->save()) {
            $this->fail('User cannot be saved: ' . $userId);
        }

        $userAddress = oxNew(\OxidEsales\Eshop\Application\Model\Address::class);
        $userAddress->setId($addressId);
        $userAddress->oxaddress__oxuserid = new \OxidEsales\Eshop\Core\Field($userId, \OxidEsales\Eshop\Core\Field::T_RAW);
        if (!$userAddress->save()) {
            $this->fail('User address cannot be saved: ' . $addressId);
        }
    }
}
