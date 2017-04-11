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

class Integration_User_registrationTest extends UserTestCase
{

    /** @var bool */
    protected $_blSkipCustomTearDown = true;

    public function testRegisterNewUser()
    {
        $sUserName = $this->_sDefaultUserName;
        $sUserPassword = $this->_sDefaultUserPassword;

        $oCmpUser = $this->_createCmpUserObject();

        $this->_setUserRegistrationParametersToRequest($sUserName, $sUserPassword);
        $this->assertSame('register?success=1', $oCmpUser->registerUser());

        return $oCmpUser->getUser()->getId();
    }

    /**
     * @param string $sUserId
     *
     * @depends testRegisterNewUser
     */
    public function testLoginWithNewUser($sUserId)
    {
        $oCmpUser = $this->_createCmpUserObject();
        $oCmpUser->logout();
        $this->assertNull(oxRegistry::getSession()->getVariable('usr'), 'User ID should not be in session after logout.');

        $this->_login();

        $this->assertSame($sUserId, oxRegistry::getSession()->getVariable('usr'), 'User ID is missing in session after log in.');
    }

    /**
     * @param string $sUserName
     * @param string $sUserPassword
     */
    private function _setUserRegistrationParametersToRequest($sUserName, $sUserPassword)
    {
        $sGermanyId = 'a7c40f631fc920687.20179984';

        $this->setRequestParam('userLoginName', $sUserName);
        $this->setRequestParam('lgn_usr', $sUserName);

        $this->setRequestParam('lgn_pwd', $sUserPassword);
        $this->setRequestParam('lgn_pwd2', $sUserPassword);
        $this->setRequestParam('passwordLength', $sUserPassword);
        $this->setRequestParam('userPasswordConfirm', $sUserPassword);

        $this->setRequestParam(
            'invadr',
            array(
                 'oxuser__oxsal'       => 'Mr',
                 'oxuser__oxfname'     => 'SomeTestName',
                 'oxuser__oxlname'     => 'SomeTestSurname',
                 'oxuser__oxstreet'    => 'SomeTestStreet',
                 'oxuser__oxstreetnr'  => '23',
                 'oxuser__oxzip'       => '44444',
                 'oxuser__oxcity'      => 'SomeTestCoty',
                 'oxuser__oxcountryid' => $sGermanyId
            )
        );
    }

    /**
     * @return oxcmp_user
     */
    private function _createCmpUserObject()
    {
        $oRegister = new Register();
        $oCmpUser = new oxcmp_user();
        $oCmpUser->setParent($oRegister);

        return $oCmpUser;
    }
}