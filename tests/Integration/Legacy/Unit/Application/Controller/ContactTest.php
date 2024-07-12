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
     */
    public function testRender()
    {
        $oContact = oxNew('Contact');
        $this->assertEquals('page/info/contact', $oContact->render());
    }

    /**
     * Test if send mail is not executed if user mail is not valid
     * and warning message is displayed
     *
     * M#1001
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
     */
    public function testGetUserData()
    {
        $this->setRequestParameter('editval', 'testval');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testval', $oObj->getUserData());
    }

    /**
     * Test getting subject.
     */
    public function testGetContactSubject()
    {
        $this->setRequestParameter('c_subject', 'testsubject');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testsubject', $oObj->getContactSubject());
    }

    /**
     * Test getting message.
     */
    public function testGetContactMessage()
    {
        $this->setRequestParameter('c_message', 'testmessage');
        $oObj = $this->getProxyClass("Contact");
        $this->assertEquals('testmessage', $oObj->getContactMessage());
    }

    /**
     * Test contact send status.
     */
    public function testGetContactSendStatus()
    {
        $oObj = $this->getProxyClass('Contact');
        $oObj->setNonPublicVar('_blContactSendStatus', true);
        $this->assertTrue($oObj->getContactSendStatus());
    }

    /**
     * Testing Contact::getBreadCrumb()
     */
    public function testGetBreadCrumb()
    {
        $oContact = oxNew('Contact');

        $this->assertEquals(1, count($oContact->getBreadCrumb()));
    }

    /**
     * Test case for bug #0002065: Contact-Mail shows MR or MRS instead of localized salutation
     */
    public function testSendForBugtrackEntry0002065()
    {
        $aParams = ["oxuser__oxusername" => "user@oxid-esales.com", "oxuser__oxfname"    => "admin", "oxuser__oxlname"    => "admin", "oxuser__oxsal"      => "MR"];

        $this->setRequestParameter("editval", $aParams);
        $this->setRequestParameter("c_message", "message");
        $this->setRequestParameter("c_subject", "subject");

        $oLang = oxRegistry::getLang();
        $sMessage = $oLang->translateString('MESSAGE_FROM') . " " . $oLang->translateString('MR') . " admin admin (user@oxid-esales.com)<br /><br />message";

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendContactMail"]);
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
     */
    public function testSendEmailNotSend()
    {
        /** @var oxUtilsView|PHPUnit\Framework\MockObject\MockObject $oUtils */
        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtils->expects($this->once())->method('addErrorToDisplay')->with($this->equalTo("ERROR_MESSAGE_CHECK_EMAIL"));
        oxTestModules::addModuleObject('oxUtilsView', $oUtils);

        $aParams = ["oxuser__oxusername" => "user@oxid-esales.com", "oxuser__oxfname"    => "admin", "oxuser__oxlname"    => "admin", "oxuser__oxsal"      => "MR"];

        $this->setRequestParameter("editval", $aParams);
        $this->setRequestParameter("c_message", "message");
        $this->setRequestParameter("c_subject", "subject");

        /** @var oxEmail|PHPUnit\Framework\MockObject\MockObject $oEmail */
        $oEmail = $this->getMock(\OxidEsales\Eshop\Core\Email::class, ["sendContactMail"]);
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

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveShop']);
        $oConfig->expects($this->any())->method('getActiveShop')->will($this->returnValue($oShop));

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\ContactController::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertEquals('shop', $oView->getTitle());
    }
}
