<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\User;

use oxField;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\User\UserTestCase;
use oxRegistry;
use oxUser;

class LoginTest extends UserTestCase
{
    use ContainerTrait;

    /**
     * @runInSeparateProcess
     *
     * Tries to login with password which is generated with old algorithm
     * and checks if password and salt were regenerated.
     */
    public function testRehashingPasswordWorksOnLoginWithOldPassword()
    {
        $oUser = $this->createUser($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt);

        $result = $this->login($this->_sDefaultUserName, $this->_sDefaultUserPassword);

        $this->assertSame('payment', $result);
        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');

        $oUser->load($oUser->getId());
        $this->assertNotSame($this->_sOldEncodedPassword, $oUser->oxuser__oxpassword->value, 'Old and new passwords must not match.');
        $this->assertNotSame($this->_sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
    }

    /**
     * @runInSeparateProcess
     *
     * Tries to login with password which was generated using new algorithm
     * and checks if password and salt were not regenerated.
     */
    public function testLoginWithNewPassword()
    {
        $salt = '';
        $passwordHash = $this->get(PasswordServiceBridgeInterface::class)->hash($this->_sDefaultUserPassword, 'PASSWORD_BCRYPT');

        $oUser = $this->createUser($this->_sDefaultUserName, $passwordHash, $salt);
        $this->login();

        $oUser->load($oUser->getId());

        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');
        $this->assertSame($passwordHash, $oUser->oxuser__oxpassword->value, 'Password in database must match with new password.');
        $this->assertEmpty($oUser->oxuser__oxpasssalt->value, 'Salt in database must be empty.');
    }

    public static function providerNotSuccessfulLogin()
    {
        return array(
            // Not successful login with old password
            array('_testUserName@oxid-esales.com', '4bb11fbb0c6bf332517a7ec397e49f1c', '3262383936333839303439393466346533653733366533346137326666393632'),
            // Not successful login with new password
            array('_testUserName@oxid-esales.com', 'b016e37ac8ec71449b475e84a941e3c39a27fb8f0710d4b47d6116ad6a6afcaa0c17006a4c01ffc67f3db95772fe001584cb4ce7e5bacd74198c24d1851841d5', '56784f8ffc657fff84915b93e12a626e'),
        );
    }

    /**
     * @runInSeparateProcess
     *
     * Tries to login with wrong password and checks if password and salt were not changed.
     *
     * @param string $sUserName
     * @param string $sEncodedPassword
     * @param string $sSalt
     *
     * @dataProvider providerNotSuccessfulLogin
     */
    public function testNotSuccessfulLogin($sUserName, $sEncodedPassword, $sSalt)
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
