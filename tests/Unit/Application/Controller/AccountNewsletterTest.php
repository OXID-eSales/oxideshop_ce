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
class AccountNewsletterTest extends \OxidTestCase
{

    /**
     * Testing Account_Newsletter::getSubscriptionStatus()
     *
     * @return null
     */
    public function testGetSubscriptionStatus()
    {
        $oView = $this->getProxyClass("Account_Newsletter");
        $oView->setNonPublicVar("_iSubscriptionStatus", "testStatus");
        $this->assertEquals("testStatus", $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     *
     * @return null
     */
    public function testSubscribeNoSessionUser()
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(false));

        $this->assertFalse($oView->subscribe());
        $this->assertEquals(0, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     *
     * @return null
     */
    public function testSubscribeNoStatusDefined()
    {
        $this->setRequestParameter("status", false);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxNewsSubscribed|PHPUnit\Framework\MockObject\MockObject $oSubscription */
        $oSubscription = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, array("setOptInStatus"));
        $oSubscription->expects($this->once())->method('setOptInStatus')->with($this->equalTo(0));

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("removeFromGroup", "getNewsSubscription"));
        $oUser->expects($this->once())->method('removeFromGroup')->with($this->equalTo('oxidnewsletter'));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));

        $this->assertNull($oView->subscribe());
        $this->assertEquals(-1, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::subscribe()
     *
     * @return null
     */
    public function testSubscribeCustomStatus()
    {
        $this->setRequestParameter("status", true);

        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSession->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        /** @var oxUser|PHPUnit\Framework\MockObject\MockObject $oUser */
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("setNewsSubscription"));
        $oUser->expects($this->atLeastOnce())->method('setNewsSubscription')->will($this->returnValue(true));

        /** @var Account_Newsletter|PHPUnit\Framework\MockObject\MockObject $oView */
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertNull($oView->subscribe());
        $this->assertEquals(1, $oView->getSubscriptionStatus());
    }

    /**
     * Testing Account_Newsletter::isNewsletter()
     *
     * @return null
     */
    public function testIsNewsletterNoSessionUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(false));
        $this->assertFalse($oView->isNewsletter());
    }

    /**
     * Testing Account_Newsletter::isNewsletter()
     *
     * @return null
     */
    public function testIsNewsletter()
    {
        $oSubscription = $this->getMock(\OxidEsales\Eshop\Application\Model\NewsSubscribed::class, array("getOptInStatus"));
        $oSubscription->expects($this->once())->method('getOptInStatus')->will($this->returnValue(1));

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array("inGroup", "getNewsSubscription"));
        $oUser->expects($this->once())->method('getNewsSubscription')->will($this->returnValue($oSubscription));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals(1, $oView->isNewsletter());
    }

    /**
     * Testing Account_Newsletter::render()
     *
     * @return null
     */
    public function testRenderNoUser()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\AccountNewsletterController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue($oUser));
        $this->assertEquals('page/account/newsletter.tpl', $oView->render());
    }

    /**
     * Testing Account_Newsletter::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oAccNewsletter = oxNew('Account_Newsletter');

        $this->assertEquals(2, count($oAccNewsletter->getBreadCrumb()));
    }
}
