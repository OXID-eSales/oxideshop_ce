<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \Exception;
use \oxField;
use \oxException;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for Account class
 */
class AccountPasswordTest extends \OxidTestCase
{

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePasswordNoUser()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $this->assertNull($oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePasswordEmptyNewPass()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ERROR_MESSAGE_INPUT_EMPTYPASS"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("checkPassword"));
        $oUser->expects($this->any())->method('checkPassword')->will($this->returnValue(new Exception("ERROR_MESSAGE_INPUT_EMPTYPASS")));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals("ERROR_MESSAGE_INPUT_EMPTYPASS", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePasswordTooShortNewPass()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ERROR_MESSAGE_PASSWORD_TOO_SHORT"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("checkPassword"));
        $oUser->expects($this->any())->method('checkPassword')->will($this->returnValue(new Exception("ERROR_MESSAGE_PASSWORD_TOO_SHORT")));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals("ERROR_MESSAGE_PASSWORD_TOO_SHORT", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePasswordPasswordsDoNotMatch()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("checkPassword"));
        $oUser->expects($this->any())->method('checkPassword')->will($this->returnValue(new Exception("ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH")));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals("ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePasswordMissingOldPass()
    {
        $this->setRequestParameter('password_old', null);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ACCOUNT_PASSWORD_ERRINCORRECTCURRENTPASSW"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("checkPassword", "isSamePassword"));
        $oUser->expects($this->any())->method('checkPassword');
        $oUser->expects($this->any())->method('isSamePassword')->will($this->returnValue(false));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals("ACCOUNT_PASSWORD_ERRINCORRECTCURRENTPASSW", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     *
     * @return null
     */
    public function testChangePassword()
    {
        $this->setRequestParameter('password_old', "oldpassword");
        $this->setRequestParameter('password_new', "newpassword");
        $this->setRequestParameter('password_new_confirm', "newpassword");

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);


        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("checkPassword", "isSamePassword", "setPassword", "save"));
        $oUser->expects($this->any())->method('checkPassword');
        $oUser->expects($this->any())->method('isSamePassword')->with($this->equalTo("oldpassword"))->will($this->returnValue(true));
        $oUser->expects($this->any())->method('setPassword')->with($this->equalTo("newpassword"));
        $oUser->expects($this->any())->method('save')->will($this->returnValue(true));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertNull($oView->changePassword());
        $this->assertTrue($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::isPasswordChanged()
     *
     * @return null
     */
    public function testIsPasswordChanged()
    {
        $oView = $this->getProxyClass("Account_Password");
        $this->assertFalse($oView->isPasswordChanged());

        $oView->setNonPublicVar("_blPasswordChanged", true);
        $this->assertTrue($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(false));

        $this->assertEquals('page/account/login.tpl', $oView->render());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));

        $this->assertEquals('page/account/password.tpl', $oView->render());
    }

    /**
     * Testing Account_Password::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccPaswd = oxNew('Account_Password');

        $this->assertEquals(2, count($oAccPaswd->getBreadCrumb()));
    }

    /**
     * Test Account_Password::changePassword() - try to login with password with spec chars.
     * #0003680
     *
     * @return null
     */
    public function testLogin_setPasswordWithSpecChars()
    {
        $oRealInputValidator = \OxidEsales\Eshop\Core\Registry::getInputValidator();

        $this->expectException('oxException');
        $this->expectExceptionMessage('ChangePass user test');

        $sOldPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%_old';
        $sPass = '&quot;&#34;"o?p[]XfdKvA=#3K8tQ%';
        $this->setRequestParameter('password_old', $sOldPass);
        $this->setRequestParameter('password_new', $sPass);
        $this->setRequestParameter('password_new_confirm', $sPass);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('isSamePassword'));

        /** @var oxInputValidator|PHPUnit\Framework\MockObject\MockObject $oInputValidator */
        $oInputValidator = $this->getMock('oxInputValidator');
        $oInputValidator->expects($this->once())
            ->method('checkPassword')
            ->with($this->equalTo($oUser), $this->equalTo($sPass), $this->equalTo($sPass), $this->equalTo(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oInputValidator);

        $oUser->expects($this->once())
            ->method('isSamePassword')
            ->with($this->equalTo($sOldPass))
            ->will($this->throwException(new oxException('ChangePass user test')));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, array('getUser'));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $oView->changePassword();

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oRealInputValidator);
    }
}
