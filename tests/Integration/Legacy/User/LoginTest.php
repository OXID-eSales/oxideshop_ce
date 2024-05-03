<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\User;

use oxField;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use oxRegistry;
use oxUser;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

final class LoginTest extends UserTestCase
{
    use ContainerTrait;

    #[RunInSeparateProcess]
    public function testRehashingPasswordWorksOnLoginWithOldPassword(): void
    {
        $oUser = $this->createUser($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt);

        $result = $this->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);

        $this->assertSame('payment', $result);
        $this->assertSame(
            $oUser->getId(),
            oxRegistry::getSession()->getVariable('usr'),
            'User ID is missing in session.'
        );

        $oUser->load($oUser->getId());
        $this->assertNotSame(
            $this->_sOldEncodedPassword,
            $oUser->oxuser__oxpassword->value,
            'Old and new passwords must not match.'
        );
        $this->assertNotSame($this->_sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
    }

    #[RunInSeparateProcess]
    public function testLoginWithNewPassword(): void
    {
        $salt = '';
        $passwordHash = $this->get(PasswordServiceBridgeInterface::class)->hash(
            $this->_sDefaultUserPassword,
            'PASSWORD_BCRYPT'
        );

        $oUser = $this->createUser($this->_sDefaultUserName, $passwordHash, $salt);
        $this->login();

        $oUser->load($oUser->getId());

        $this->assertSame(
            $oUser->getId(),
            oxRegistry::getSession()->getVariable('usr'),
            'User ID is missing in session.'
        );
        $this->assertSame(
            $passwordHash,
            $oUser->oxuser__oxpassword->value,
            'Password in database must match with new password.'
        );
        $this->assertEmpty($oUser->oxuser__oxpasssalt->value, 'Salt in database must be empty.');
    }

    public static function providerNotSuccessfulLogin(): array
    {
        return [
            // Not successful login with old password
            [
                '_testUserName@oxid-esales.com',
                '4bb11fbb0c6bf332517a7ec397e49f1c',
                '3262383936333839303439393466346533653733366533346137326666393632',
            ],
            // Not successful login with new password
            [
                '_testUserName@oxid-esales.com',
                'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5',
                '56784f8ffc657fff84915b93e12a626e',
            ],
        ];
    }

    #[DataProvider('providerNotSuccessfulLogin')]
    #[RunInSeparateProcess]
    public function testNotSuccessfulLogin(string $sUserName, string $sEncodedPassword, string $sSalt): void
    {
        $oUser = $this->createUser($sUserName, $sEncodedPassword, $sSalt);
        $sPasswordWrong = 'wrong_password';
        $this->login($sUserName, $sPasswordWrong);

        $oUser->load($oUser->getId());

        $this->assertNull(oxRegistry::getSession()->getVariable('usr'), 'User ID should be null in session.');
        $this->assertSame($sEncodedPassword, $oUser->oxuser__oxpassword->value, 'Password must be same.');
        $this->assertSame($sSalt, $oUser->oxuser__oxpasssalt->value, 'Salt must be same.');
    }

    /**
     * @param string $sUserName
     * @param string $sEncodedPassword
     * @param string $sSalt
     *
     * @return oxUser
     */
    private function createUser($sUserName, $sEncodedPassword, $sSalt)
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sEncodedPassword, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSalt, oxField::T_RAW);
        $oUser->save();

        return $oUser;
    }
}
