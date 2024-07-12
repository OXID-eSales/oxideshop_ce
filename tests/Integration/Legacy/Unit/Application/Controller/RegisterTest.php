<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxTestModules;

class RegisterTest extends \PHPUnit\Framework\TestCase
{

    /**
     * oxScLoginRegister::render() test case
     */
    public function testRenderForLoginFeature()
    {
        $this->getConfig()->setConfigParam("blPsLoginEnabled", true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RegisterController::class, ["isConfirmed"]);
        $oView->expects($this->once())->method('isConfirmed')->willReturn(true);
        $this->assertSame('page/account/register_confirm', $oView->render());
    }

    /**
     * oxScLoginRegister::confirmRegistration() test case
     */
    public function testConfirmRegistrationBadUserUpdateId()
    {
        oxTestModules::addFunction("oxuser", "loadUserByUpdateId", "{return false;}");
        oxTestModules::addFunction("oxUtilsView", "addErrorToDisplay", "{}");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RegisterController::class, ["getUpdateId"]);
        $oView->expects($this->once())->method('getUpdateId')->willReturn("testUpdateId");
        $this->assertSame('account', $oView->confirmRegistration());
    }

    /**
     * oxScLoginRegister::confirmRegistration() test case
     */
    public function testConfirmRegistration()
    {
        oxTestModules::addFunction("oxuser", "loadUserByUpdateId", "{return true;}");
        oxTestModules::addFunction("oxuser", "setUpdateKey", "{return true;}");
        oxTestModules::addFunction("oxuser", "save", "{return true;}");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RegisterController::class, ["getUpdateId"]);
        $oView->expects($this->once())->method('getUpdateId')->willReturn("testUpdateId");
        $this->assertSame('register?confirmstate=1', $oView->confirmRegistration());
    }

    /**
     * oxScLoginRegister::getUpdateId() test case
     */
    public function testGetUpdateId()
    {
        $this->setRequestParameter('uid', "testUid");

        $oView = oxNew('register');
        $this->assertSame("testUid", $oView->getUpdateId());
    }

    /**
     * oxScLoginRegister::isConfirmed() test case
     */
    public function testIsConfirmed()
    {
        $oView = oxNew('register');

        $this->setRequestParameter("confirmstate", 0);
        $this->assertFalse($oView->isConfirmed());

        $this->setRequestParameter("confirmstate", 1);
        $this->assertTrue($oView->isConfirmed());
    }

    public function testGetRegistrationError()
    {
        $oRegister = $this->getProxyClass('register');
        $this->setRequestParameter('newslettererror', 'testError');

        $this->assertSame('testError', $oRegister->getRegistrationError());
    }

    public function testGetRegistrationStatus()
    {
        $oRegister = $this->getProxyClass('register');
        $this->setRequestParameter('success', 'success');

        $this->assertSame('success', $oRegister->getRegistrationStatus());
    }

    /**
     * Testing if method returns correct value
     */
    public function testIsFieldRequired()
    {
        $oRegister = $this->getMock(\OxidEsales\Eshop\Application\Controller\RegisterController::class, ['getMustFillFields']);
        $oRegister->method('getMustFillFields')->willReturn(["testValue1" => 1, "testValue2" => 1]);

        $this->assertTrue($oRegister->isFieldRequired("testValue1"));
        $this->assertFalse($oRegister->isFieldRequired("testValue5"));
    }

    public function testRenderNoRStat()
    {
        $oRegister = oxNew('register');
        $this->assertSame('page/account/register', $oRegister->render());
    }

    public function testRenderRStat()
    {
        $oRegister = $this->getMock(\OxidEsales\Eshop\Application\Controller\RegisterController::class, ['getRegistrationStatus', 'getRegistrationError']);
        $oRegister->expects($this->exactly(2))->method('getRegistrationStatus')->willReturn('rst');
        $oRegister->expects($this->once())->method('getRegistrationError')->willReturn('rer');

        $this->assertSame('page/account/register_success', $oRegister->render());
        $this->assertSame('rst', $oRegister->getRegistrationStatus());
        $this->assertSame('rer', $oRegister->getRegistrationError());
    }

    public function testGetBreadCrumb()
    {
        $oRegister = oxNew('register');

        $this->assertCount(1, $oRegister->getBreadCrumb());
    }
}
