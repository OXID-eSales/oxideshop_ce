<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\User;

use oxField;
use OxidEsales\EshopCommunity\Internal\Domain\Authentication\Bridge\PasswordServiceBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use oxRegistry;
use oxUser;

class LoginTest extends UserTestCase
{
    use ContainerTrait;

    /**
     * Tries to login with password which is generated with old algorithm
     * and checks if password and salt were regenerated.
     */
    public function testRehashingPasswordWorksOnLoginWithOldPassword()
    {
        $oUser = $this->_createUser($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt);

        $result = $this->_login($this->_sDefaultUserName, $this->_sDefaultUserPassword);

        $this->assertSame('payment', $result);
        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');

        $oUser->load($oUser->getId());
        $this->assertNotSame($this->_sOldEncodedPassword, $oUser->oxuser__oxpassword->value, 'Old and new passwords must not match.');
        $this->assertNotSame($this->_sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
    }

    /**
     * Tries to login with password which was generated using new algorithm
     * and checks if password and salt were not regenerated.
     */
    public function testLoginWithNewPassword()
    {
        $salt = '';
        $passwordHash = $this->get(PasswordServiceBridgeInterface::class)->hash($this->_sDefaultUserPassword, 'PASSWORD_BCRYPT');

        $oUser = $this->_createUser($this->_sDefaultUserName, $passwordHash, $salt);
        $this->_login();

        $oUser->load($oUser->getId());

        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');
        $this->assertSame($passwordHash, $oUser->oxuser__oxpassword->value, 'Password in database must match with new password.');
        $this->assertEmpty($oUser->oxuser__oxpasssalt->value, 'Salt in database must be empty.');
    }

    public function providerNotSuccessfulLogin()
    {
        return array(
            // Not successful login with old password
            array($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt),
            // Not successful login with new password
            array($this->_sDefaultUserName, $this->_sNewEncodedPassword, $this->_sNewSalt),
        );
    }

    /**
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
        $oUser = $this->_createUser($sUserName, $sEncodedPassword, $sSalt);
        $sPasswordWrong = 'wrong_password';
        $this->_login($sUserName, $sPasswordWrong);

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
    private function _createUser($sUserName, $sEncodedPassword, $sSalt)
    {
        $oUser = oxNew('oxUser');
        $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sEncodedPassword, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSalt, oxField::T_RAW);
        $oUser->save();

        return $oUser;
    }
}
