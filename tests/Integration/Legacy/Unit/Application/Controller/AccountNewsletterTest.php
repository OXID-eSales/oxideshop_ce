<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxRegistry;

/**
 * Tests for Account class
 */
class AccountNewsletterTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing Account_Newsletter::getSubscriptionStatus()
     */
    public function testGetSubscriptionStatus()
    {
        $oView = $this->getProxyClass("Account_Newsletter");
        $oView->setNonPublicVar("_iSubscriptionStatus", "testStatus");
        $this->assertSame("testStatus", $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     */
    public function testSubscribeNoSessionUser()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn(false);

        $this->assertFalse($oView->subscribe());
        $this->assertSame(0, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     */
    public function testSubscribeNoStatusDefined()
    {
        $this->setRequestParameter("status", false);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxNewsSubscribed|PHPUnit\Framework\MockObject\MockObject $oSubscription */
        $oSubscription = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, ["setOptInStatus"]);
        $oSubscription->expects($this->once())->method('setOptInStatus')->with(0);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["removeFromGroup", "getNewsSubscription"]);
        $oUser->expects($this->once())->method('removeFromGroup')->with('oxidnewsletter');
        $oUser->expects($this->once())->method('getNewsSubscription')->willReturn($oSubscription);

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn($oUser);

        $this->assertNull($oView->subscribe());
        $this->assertSame(-1, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     */
    public function testSubscribeCustomStatus()
    {
        $this->setRequestParameter("status", true);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $oSession->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["setNewsSubscription"]);
        $oUser->expects($this->atLeastOnce())->method('setNewsSubscription')->willReturn(true);

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn($oUser);
        $this->assertNull($oView->subscribe());
        $this->assertSame(1, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::isNewsletter()
     */
    public function testIsNewsletterNoSessionUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn(false);
        $this->assertFalse($oView->isNewsletter());
    }

    /**
     * Testing Account_Newsletter::isNewsletter()
     */
    public function testIsNewsletter()
    {
        $oSubscription = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, ["getOptInStatus"]);
        $oSubscription->expects($this->once())->method('getOptInStatus')->willReturn(1);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["inGroup", "getNewsSubscription"]);
        $oUser->expects($this->once())->method('getNewsSubscription')->willReturn($oSubscription);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->expects($this->once())->method('getUser')->willReturn($oUser);
        $this->assertSame(1, $oView->isNewsletter());
    }

    /**
     * Testing Account_Newsletter::render()
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, ["getUser"]);
        $oView->method('getUser')->willReturn($oUser);
        $this->assertSame('page/account/newsletter', $oView->render());
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oAccNewsletter = oxNew('Account_Newsletter');

        $this->assertCount(2, $oAccNewsletter->getBreadCrumb());
    }
}
