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

class Unit_Views_inviteTest extends OxidTestCase
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
        $oInvite = new Invite();

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
     * Testing method getCaptcha()
     *
     * @return null
     */
    public function testGetCaptcha()
    {
        $oView = $this->getProxyClass('invite');
        $this->assertEquals(oxNew('oxCaptcha'), $oView->getCaptcha());
    }

    /**
     * Testing method send() - no user input
     *
     * @return null
     */
    public function testSend_noUserInput()
    {
        modConfig::setRequestParameter('editval', null);
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock('oxEmail', array('sendInviteMail'));
        $oEmail->expects($this->never())->method('sendInviteMail');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oView = $this->getProxyClass("invite");
        $oView->send();

        $this->assertNull($oView->getNonPublicVar("_iMailStatus"));
    }

    /**
     * Testing method send() - no captcha text
     *
     * @return null
     */
    public function testSend_withoutCaptcha()
    {
        modConfig::setRequestParameter('editval', array('rec_email' => 'testRecEmail@oxid-esales.com', 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock('oxEmail', array('sendInviteMail'));
        $oEmail->expects($this->never())->method('sendInviteMail');
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oCaptcha = $this->getMock('oxCaptcha', array('pass'));
        $oCaptcha->expects($this->once())->method('pass')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxCaptcha', $oCaptcha);

        $oView = $this->getMock("invite", array("getUser"));
        $oView->expects($this->once())->method('getUser')->will($this->returnValue(true));
        $oView->send();
        $this->assertFalse($oView->getInviteSendStatus());
    }

    /**
     * Testing method send()
     *
     * @return null
     */
    public function testSend()
    {
        modConfig::setRequestParameter('editval', array('rec_email' => array('testRecEmail@oxid-esales.com'), 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock('oxEmail', array('sendInviteMail'));
        $oEmail->expects($this->once())->method('sendInviteMail')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oCaptcha = $this->getMock('oxCaptcha', array('pass'));
        $oCaptcha->expects($this->once())->method('pass')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxCaptcha', $oCaptcha);

        $oView = $this->getMock("invite", array("getUser"));
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
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam("blInvitationsEnabled", false);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
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
        modConfig::setRequestParameter('editval', array('rec_email' => array('testRecEmail@oxid-esales.com'), 'send_name' => 'testSendName', 'send_email' => 'testSendEmail@oxid-esales.com', 'send_message' => 'testSendMessage', 'send_subject' => 'testSendSubject'));
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

        $oEmail = $this->getMock('oxEmail', array('sendInviteMail'));
        $oEmail->expects($this->once())->method('sendInviteMail')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxEmail', $oEmail);

        $oCaptcha = $this->getMock('oxCaptcha', array('pass'));
        $oCaptcha->expects($this->once())->method('pass')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxCaptcha', $oCaptcha);

        $oUser = $this->getMock('oxUser', array('updateInvitationStatistics'));
        $oUser->expects($this->once())->method('updateInvitationStatistics')->will($this->returnValue(true));

        $oView = $this->getMock('invite', array('getUser'));
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
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

        $oView = $this->getMock("invite", array("getUser"));
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
        modConfig::getInstance()->setConfigParam("blInvitationsEnabled", true);

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
        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam("blInvitationsEnabled", false);

        $oUtils = $this->getMock('oxUtils', array('redirect'));
        $oUtils->expects($this->once())->method('redirect')->with($this->equalTo($oConfig->getShopHomeURL()));
        oxTestModules::addModuleObject('oxUtils', $oUtils);

        $oView = $this->getProxyClass("invite");

        $this->assertEquals(null, $oView->render());
    }

}
