<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;
use \oxRegistry;
use \oxTestModules;

/**
 * Tests for contact class
 */
class ContactTest extends \OxidTestCase
{

    /**
     * Test view render.
     *
     * @return null
     */
    public function testRender()
    {
        $oContact = oxNew('Contact');
        $this->assertEquals('page/info/contact.tpl', $oContact->render());
    }

    /**
     * Test if send mail is not executed if user mail is not valid
     * and warning message is displayed
     *
     * M#1001
     *
     * @return null
     */
    public function testSave_withIncorectEmail()
    {
        oxRegistry::getSession()->deleteVariable('Errors');

        $aParams['oxuser__oxusername'] = 'invalidEmail';
        $this->setRequestParameter('editval', $aParams);
        $oContact = oxNew('Contact');

        $this->assertFalse($oContact->send());

        //checking if warning was added to errors list
        $sErr = oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOVALIDEMAIL');
        $aEx = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aEx['default'][0]);

        $this->assertEquals($sErr, $oErr->getOxMessage());
    }

    /**
     * Test if send mail is not executed if user data is not entered
     * and warning message is displayed
     *
     * @return null
     */
    public function testSave_withoutUserData()
    {
        oxRegistry::getSession()->deleteVariable('Errors');
        $oContact = oxNew('Contact');

        $this->assertFalse($oContact->send());

        //checking if warning was added to errors list
        $sErr = oxRegistry::getLang()->translateString('ERROR_MESSAGE_INPUT_NOTALLFIELDS');
        $aEx = oxRegistry::getSession()->getVariable('Errors');
        $oErr = unserialize($aEx['default'][0]);

        $this->assertEquals($sErr, $oErr->getOxMessage());
    }

    /**
     * Test send mail
     *
     * @return null
     */
    public function testSave()
    {
        oxTestModules::addFunction('oxemail', 'sendContactMail', '{return true;}');

        $aParams['oxuser__oxusername'] = 'aaaa@aaa.com';
        $aParams['oxuser__oxfname'] = 'first name';
        $aParams['oxuser__oxlname'] = 'last name';
        $this->setRequestParameter('editval', $aParams);
        $this->setRequestParameter('c_subject', "testSubject");
        $oObj = $this->getProxyClass("Contact");
        $oObj->send();

        $this->assertEquals(1, $oObj->getNonPublicVar('_blContactSendStatus'));
    }

    /**
     * Test getting user data.
     *
     * @return null
     */
    public function testGetUserData()
    {
        $this->setRequestParameter('editval', 'testval');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testval', $oObj->getUserData());
    }

    /**
     * Test getting subject.
     *
     * @return null
     */
    public function testGetContactSubject()
    {
        $this->setRequestParameter('c_subject', 'testsubject');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testsubject', $oObj->getContactSubject());
    }

    /**
     * Test getting message.
     *
     * @return null
     */
    public function testGetContactMessage()
    {
        $this->setRequestParameter('c_message', 'testmessage');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testmessage', $oObj->getContactMessage());
    }

    /**
     * Test contact send status.
     *
     * @return null
     */
    public function testGetContactSendStatus()
    {
        $oObj = $this->getProxyClass('Contact');
        $oObj->setNonPublicVar('_blContactSendStatus', true);
        $this->assertTrue($oObj->getContactSendStatus());
    }

    /**
     * Testing Contact::getBreadCrumb()
     *
     * @return null
     */
    public function testGetBreadCrumb()
    {
        $oContact = oxNew('Contact');

        $this->assertEquals(1, count($oContact->getBreadCrumb()));
    }

    /**
     * Test case for bug #0002065: Contact-Mail shows MR or MRS instead of localized salutation
     *
     * @return null
     */
    public function testSendForBugtrackEntry0002065()
    {
        $aParams = array("oxuser__oxusername" => "user@oxid-esales.com",
                         "oxuser__oxfname"    => "admin",
                         "oxuser__oxlname"    => "admin",
                         "oxuser__oxsal"      => "MR");

        $this->setRequestParameter("editval", $aParams);
        $this->setRequestParameter("c_message", "message");
        $this->setRequestParameter("c_subject", "subject");

        $oLang = oxRegistry::getLang();
        $sMessage = $oLang->translateString('MESSAGE_FROM') . " " . $oLang->translateString('MR') . " admin admin (user@oxid-esales.com)<br /><br />message";

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendContactMail"));
        $oEmail
            ->expects($this->once())
            ->method('sendContactMail')
            ->with(
                $this->equalTo('user@oxid-esales.com'),
                $this->equalTo('subject'),
                $this->equalTo($sMessage)
            )->will(
                $this->returnValue(true)
            );

        oxTestModules::addModuleObject('oxemail', $oEmail);

        /** @var Contact|PHPUnit\Framework\MockObject\MockObject $oContact */
        $oContact = oxNew('Contact');
        $oContact->send();
    }

    /**
     * Testing method send()
     *
     * @return null
     */
    public function testSendEmailNotSend()
    {
        /** @var oxUtilsView|PHPUnit\Framework\MockObject\MockObject $oUtils */
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('addErrorToDisplay'));
        $oUtils->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo("ERROR_MESSAGE_CHECK_EMAIL"));
        oxTestModules::addModuleObject('oxUtilsView', $oUtils);

        $aParams = array("oxuser__oxusername" => "user@oxid-esales.com",
                         "oxuser__oxfname"    => "admin",
                         "oxuser__oxlname"    => "admin",
                         "oxuser__oxsal"      => "MR");

        $this->setRequestParameter("editval", $aParams);
        $this->setRequestParameter("c_message", "message");
        $this->setRequestParameter("c_subject", "subject");

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, array("sendContactMail"));
        $oEmail->expects($this->once())->method('sendContactMail')->will($this->returnValue(false));

        oxTestModules::addModuleObject('oxemail', $oEmail);

        /** @var Contact|PHPUnit\Framework\MockObject\MockObject $oContact */
        $oContact = oxNew('Contact');
        $oContact->send();
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxcompany = new oxField('shop');

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array('getActiveShop'));
        $oConfig->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ContactController::class, array('getConfig'));
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals('shop', $oView->getTitle());
    }
}
