<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Theme;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use oxRegistry;

class UtilsViewTest extends \OxidTestCase
{
    use ContainerTrait;

    public function setup(): void
    {
        parent::setUp();

        $theme = oxNew(Theme::class);
        $theme->load(ACTIVE_THEME);
        $theme->activate();
    }

    /**
     * Testing template processign code + skipped debug output code
     *
     * TODO: template engine needed
     */
    public function testGetTemplateOutput()
    {
        $this->getConfig()->setConfigParam('iDebug', 0);
        $sTpl = 'message/error';

        $oView = oxNew('oxview');
        $oView->addTplParam('statusMessage', 'xxx');

        $oUtilsView = oxNew('oxutilsview');

        $this->assertStringContainsString('alert', $oUtilsView->getTemplateOutput($sTpl, $oView));
        $this->assertStringContainsString('xxx', $oUtilsView->getTemplateOutput($sTpl, $oView));
    }

    public function testPassAllErrorsToView()
    {
        $aView = [];
        $aErrors[1][2] = serialize("foo");
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->passAllErrorsToView($aView, $aErrors);
        $this->assertEquals($aView['Errors'][1][2], "foo");
    }

    public function testAddErrorToDisplayCustomDestinationFromParam()
    {
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "myDest");

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['myDest'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $this->assertNull(oxRegistry::getSession()->getVariable('ErrorController'));
    }

    public function testAddErrorToDisplayCustomDestinationFromPost()
    {
        $this->setRequestParameter('CustomError', 'myDest');
        $this->setRequestParameter('actcontrol', 'oxwminibasket');

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "");
        $aErrors = Registry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['myDest'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $aErrorController = Registry::getSession()->getVariable('ErrorController');
        $this->assertEquals("oxwminibasket", $aErrorController['myDest']);
    }

    public function testAddErrorToDisplayDefaultDestination()
    {
        $this->setRequestParameter('actcontrol', 'start');
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "");
        $aErrors = Registry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['default'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $aErrorController = Registry::getSession()->getVariable('ErrorController');
        $this->assertEquals("start", $aErrorController['default']);
    }

    public function testAddErrorToDisplayUsingExeptionObject()
    {
        $oTest = oxNew('oxException');
        $oTest->setMessage("testMessage");

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay($oTest, false, false, "");

        $aErrors = Registry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['default'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
    }

    public function testAddErrorToDisplayIfNotSet()
    {
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(true));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay(null, false, false, "");

        $aErrors = Registry::getSession()->getVariable('Errors');
        $this->assertFalse(isset($aErrors['default'][0]));
        $this->assertNull(Registry::getSession()->getVariable('ErrorController'));
    }

    public function testAddErrorToDisplay_startsSessionIfNotStarted()
    {
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId', 'isHeaderSent', 'setForceNewSession', 'start']);
        $session->expects($this->once())->method('getId')->will($this->returnValue(false));
        $session->expects($this->once())->method('isHeaderSent')->will($this->returnValue(false));
        $session->expects($this->once())->method('setForceNewSession');
        $session->expects($this->once())->method('start');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oxUtilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $oxUtilsView->addErrorToDisplay(null, false, false, "");
    }
}
