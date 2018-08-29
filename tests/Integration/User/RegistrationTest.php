<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\User;

use oxcmp_user;
use oxRegistry;

require_once 'UserTestCase.php';

class RegistrationTest extends UserTestCase
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

        $this->setRequestParameter('userLoginName', $sUserName);
        $this->setRequestParameter('lgn_usr', $sUserName);

        $this->setRequestParameter('lgn_pwd', $sUserPassword);
        $this->setRequestParameter('lgn_pwd2', $sUserPassword);
        $this->setRequestParameter('passwordLength', $sUserPassword);
        $this->setRequestParameter('userPasswordConfirm', $sUserPassword);

        $this->setRequestParameter(
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
        $oRegister = oxNew('Register');
        $oCmpUser = oxNew('oxcmp_user');
        $oCmpUser->setParent($oRegister);

        return $oCmpUser;
    }
}
