<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once 'userTestCase.php';

class Integration_User_loginTest extends UserTestCase
{

    /**
     * Tries to login with password which is generated with old algorithm
     * and checks if password and salt were regenerated.
     */
    public function testLoginWithOldPassword()
    {
        $oUser = $this->_createUser($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt);

        $this->_login($this->_sDefaultUserName, $this->_sDefaultUserPassword);

        $oUser->load($oUser->getId());

        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');
        $this->assertNotSame($this->_sOldEncodedPassword, $oUser->oxuser__oxpassword->value, 'Old and new passwords must not match.');
        $this->assertNotSame($this->_sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
    }

    /**
     * Tries to login with old password from different subshop, makes sure there are no crashes
     */
    public function testAdminLoginWithOldPasswordMultishop()
    {

        $this->setAdminMode(true);

        //faking cookie check
        $oUtils = $this->getMock("oxUtilsServer", array("getOxCookie"));
        $oUtils->expects($this->any())->method("getOxCookie")->will($this->returnValue(array("test" => "test")));
        oxRegistry::set("oxUtilsServer", $oUtils);

        //creating test admin user
        $oUser = $this->_createUser($this->_sDefaultUserName, $this->_sOldEncodedPassword, $this->_sOldSalt);
        //updating user over oxBase methods as oxUser restricts rights update
        $oUpdUser = new oxBase();
        $oUpdUser->init("oxuser");
        $oUpdUser->load($oUser->getId());
        $oUpdUser->oxuser__oxrights = new oxField(1);
        $oUpdUser->oxuser__oxshopid = new oxField(1);
        $oUpdUser->save();

        //set active shop 2
        oxRegistry::getConfig()->setShopId(2);

        //perform the login
        $this->_login($this->_sDefaultUserName, $this->_sDefaultUserPassword);

        $oUser->load($oUser->getId());
        $this->assertEquals(1, $oUser->oxuser__oxshopid->value, "User shop ID changed");
        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('auth'), 'User ID is missing in session.');
        $this->assertNotSame($this->_sOldEncodedPassword, $oUser->oxuser__oxpassword->value, 'Old and new passwords must not match.');
        $this->assertNotSame($this->_sOldSalt, $oUser->oxuser__oxpasssalt->value, 'Old and new salt must not match.');
    }

    /**
     * Tries to login with password which was generated using new algorithm
     * and checks if password and salt were not regenerated.
     */
    public function testLoginWithNewPassword()
    {
        $oUser = $this->_createUser($this->_sDefaultUserName, $this->_sNewEncodedPassword, $this->_sNewSalt);
        $this->_login();

        $oUser->load($oUser->getId());

        $this->assertSame($oUser->getId(), oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session.');
        $this->assertSame($this->_sNewEncodedPassword, $oUser->oxuser__oxpassword->value, 'Password in database must match with new password.');
        $this->assertSame($this->_sNewSalt, $oUser->oxuser__oxpasssalt->value, 'Salt in database must match with new salt.');
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
        $oUser = new oxUser();
        $oUser->oxuser__oxusername = new oxField($sUserName, oxField::T_RAW);
        $oUser->oxuser__oxpassword = new oxField($sEncodedPassword, oxField::T_RAW);
        $oUser->oxuser__oxpasssalt = new oxField($sSalt, oxField::T_RAW);
        $oUser->save();

        return $oUser;
    }
}
