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
class AccountPasswordTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePasswordNoUser()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);

        $this->assertNull($oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePasswordEmptyNewPass()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ERROR_MESSAGE_INPUT_EMPTYPASS"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["checkPassword"]);
        $oUser->method('checkPassword')->willReturn(new Exception("ERROR_MESSAGE_INPUT_EMPTYPASS"));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertSame("ERROR_MESSAGE_INPUT_EMPTYPASS", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePasswordTooShortNewPass()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ERROR_MESSAGE_PASSWORD_TOO_SHORT"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["checkPassword"]);
        $oUser->method('checkPassword')->willReturn(new Exception("ERROR_MESSAGE_PASSWORD_TOO_SHORT"));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertSame("ERROR_MESSAGE_PASSWORD_TOO_SHORT", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePasswordPasswordsDoNotMatch()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["checkPassword"]);
        $oUser->method('checkPassword')->willReturn(new Exception("ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH"));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertSame("ACCOUNT_PASSWORD_ERRPASSWDONOTMATCH", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePasswordMissingOldPass()
    {
        $this->setRequestParameter('password_old', null);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ return "ACCOUNT_PASSWORD_ERRINCORRECTCURRENTPASSW"; }');

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["checkPassword", "isSamePassword"]);
        $oUser->method('checkPassword');
        $oUser->method('isSamePassword')->willReturn(false);

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertSame("ACCOUNT_PASSWORD_ERRINCORRECTCURRENTPASSW", $oView->changePassword());
        $this->assertFalse($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::changePassword()
     */
    public function testChangePassword()
    {
        $this->setRequestParameter('password_old', "oldpassword");
        $this->setRequestParameter('password_new', "newpassword");
        $this->setRequestParameter('password_new_confirm', "newpassword");

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);


        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["checkPassword", "isSamePassword", "setPassword", "save"]);
        $oUser->method('checkPassword');
        $oUser->method('isSamePassword')->with("oldpassword")->willReturn(true);
        $oUser->method('setPassword')->with("newpassword");
        $oUser->method('save')->willReturn(true);

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertNull($oView->changePassword());
        $this->assertTrue($oView->isPasswordChanged());
    }

    /**
     * Testing Account_Newsletter::isPasswordChanged()
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
     */
    public function testRenderNoUser()
    {
        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn(false);

        $this->assertSame('page/account/login', $oView->render());
    }

    /**
     * Testing Account_Newsletter::render()
     */
    public function testRender()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxpassword = new oxField("testPassword");

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);

        $this->assertSame('page/account/password', $oView->render());
    }

    /**
     * Testing Account_Password::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccPaswd = oxNew('Account_Password');

        $this->assertCount(2, $oAccPaswd->getBreadCrumb());
    }

    /**
     * Test Account_Password::changePassword() - try to login with password with spec chars.
     * #0003680
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['isSamePassword']);

        /** @var oxInputValidator|PHPUnit\Framework\MockObject\MockObject $oInputValidator */
        $oInputValidator = $this->getMock('oxInputValidator');
        $oInputValidator->expects($this->once())
            ->method('checkPassword')
            ->with($oUser, $sPass, $sPass, true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oInputValidator);

        $oUser->expects($this->once())
            ->method('isSamePassword')
            ->with($sOldPass)
            ->willThrowException(new oxException('ChangePass user test'));

        /** @var Account_Password|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountPasswordController::class, ['getUser']);
        $oView->expects($this->once())->method('getUser')->willReturn($oUser);

        $oView->changePassword();

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\InputValidator::class, $oRealInputValidator);
    }
}
