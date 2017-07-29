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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxUser;
use \oxTestModules;

class InviteTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxinvitations', 'oxuserid');

        parent::tearDown();
    }

    /**
     * Testing method setInviteData()
     *
     * @return null
     */
    public function testSetInviteData()
    {
        $oView = $this->getProxyClass("invite");
        $oView->setInviteData("testData");

        $this->assertEquals("testData", $oView->getNonPublicVar("_aInviteData"));
    }

    /**
     * Testing Invite::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oInvite = oxNew('Invite');

        $this->assertEquals(1, count($oInvite->getBreadCrumb()));
    }

    /**
     * Testing method getInviteData()
     *
     * @return null
     */
    public function testGetInviteData()
    {
        $oView = $this->getProxyClass("invite");
        $oView->setNonPublicVar("_aInviteData", "testData");

        $this->assertEquals("testData", $oView->getInviteData());
    }

    /**
     * Testing method getInviteSendStatus()
     *
     * @return null
     */
    public function testGetInviteSendStatus()
    {
        $oView = $this->getProxyClass("invite");
        $oView->setNonPublicVar("_iMailStatus", 1);

        $this->assertTrue($oView->getInviteSendStatus());
    }

    /**
     * Testing method send() - no user input
     *
     * @return null
     */
    public function testSend_noUserInput()
    {
        $this->setRequestParameter('editval', null);
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('sendInviteMail'));
        $oEmail->expects($this->never())->method('sendInviteMail');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oView = $this->getProxyClass("invite");
        $oView->send();

        $this->assertNull($oView->getNonPublicVar("_iMailStatus"));
    }

    /**
     * Testing method send()
     *
     * @return null
     */
    public function testSend()
    {
        $this->setRequestParameter('editval', array('rec_email' => array('testRecEmail@oxid-esales.com'), 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('sendInviteMail'));
        $oEmail->expects($this->once())->method('sendInviteMail')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\InviteController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(new oxUser()));
        $oView->send();
        $this->assertTrue($oView->getInviteSendStatus());
    }

    /**
     * Testing method send()
     *
     * @return null
     */
    public function testSend_invitationNotActive()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam("blInvitationsEnabled", false);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($oConfig->getShopHomeURL()));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oView = $this->getProxyClass("invite");
        $oView->send();
    }

    /**
     * Testing method send() - on success updated statistics
     *
     * @return null
     */
    public function testSend_updatesStatistics()
    {
        $this->setRequestParameter('editval', array('rec_email' => array('testRecEmail@oxid-esales.com'), 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array('sendInviteMail'));
        $oEmail->expects($this->once())->method('sendInviteMail')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('updateInvitationStatistics'));
        $oUser->expects($this->once())->method('updateInvitationStatistics')->will($this->returnValue(true));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\InviteController::class, array('getUser'));
        $oView->expects($this->exactly(2))->method('getUser')->will($this->returnValue($oUser));
        $oView->send();
    }

    /**
     * Testing method render()
     *
     * @return null
     */
    public function testRender()
    {
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\InviteController::class, array("getUser"));
        $oView->expects($this->any())->method('getUser')->will($this->returnValue(new oxUser()));

        $this->assertEquals('page/privatesales/invite.tpl', $oView->render());
    }

    /**
     * Testing method render() - mail was sent status
     *
     * @return null
     */
    public function testRender_mailWasSent()
    {
        $this->getConfig()->setConfigParam("blInvitationsEnabled", true);

        $oView = $this->getProxyClass('invite');
        $oView->setNonPublicVar("_iMailStatus", 1);
        $oView->render();

        $this->assertTrue($oView->getInviteSendStatus());
    }

    /**
     * Testing method render()
     *
     * @return null
     */
    public function testRender_invitationNotActive()
    {
        $oConfig = $this->getConfig();
        $oConfig->setConfigParam("blInvitationsEnabled", false);

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($oConfig->getShopHomeURL()));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oView = $this->getProxyClass("invite");

        $this->assertEquals(null, $oView->render());
    }

}
