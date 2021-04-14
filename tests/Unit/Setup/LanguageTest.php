<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Setup;

require_once getShopBasePath() . '/Setup/functions.php';

use OxidEsales\EshopCommunity\Setup\Session as SetupSession;

/**
 * Language tests
 */
class LanguageTest extends \OxidTestCase
{

    /**
     * Test teardown
     */
    protected function tearDown(): void
    {
        if (isset($_GET['istep'])) {
            unset($_GET['istep']);
        }
        if (isset($_POST['istep'])) {
            unset($_POST['istep']);
        }

        parent::tearDown();
    }

    public function testGetSetupLangLanguageIdentIsPassedByRequest()
    {
        $oSession = $this->getMock('SetupSession', array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->method("setSessionParam")->with($this->equalTo('setup_lang'));
        $oSession->method("getSessionParam")->with($this->equalTo('setup_lang'));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->method("getRequestVar")->will($this->returnValue("de"));

        $oSetup = $this->getMock("Setup", array("getStep"));
        $oSetup->method("getStep")->with($this->equalTo('STEP_WELCOME'));

        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getInstance", "setViewParam"));
        $oLang
            ->method('getInstance')
            ->withConsecutive(['Session'], ['Utilities'], ['Setup'])
            ->willReturnOnConsecutiveCalls(
                $oSession,
                $oUtils,
                $oSetup
            );

        $oLang->getLanguage();
    }

    /**
     * Testing Language::getSetupLang()
     */
    public function testGetSetupLang()
    {
        $aLangs = array('en', 'de');
        $sBrowserLang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        $sBrowserLang = (in_array($sBrowserLang, $aLangs)) ? $sBrowserLang : $aLangs[0];

        $oSession = $this->getMock('SetupSession', array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->method("getSessionParam")->with($this->equalTo('setup_lang'))->will($this->returnValue(null));
        $oSession->method("setSessionParam")->with($this->equalTo('setup_lang'), $this->equalTo($sBrowserLang));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->method("getRequestVar")->with($this->equalTo('setup_lang'), $this->equalTo('post'))->will($this->returnValue(null));

        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getInstance", "setViewParam"));
        $oLang
            ->method('getInstance')
            ->withConsecutive(['Session'], ['Utilities'])
            ->willReturnOnConsecutiveCalls(
                $oSession,
                $oUtils,
            );

        $oLang->getLanguage();
    }

    /**
     * Testing Language::getText()
     */
    public function testGetText()
    {
        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getLanguage"));
        $oLang->expects($this->any())->method("getLanguage")->will($this->returnValue('en'));
        $this->assertEquals("System Requirements", $oLang->getText("TAB_0_TITLE"));
        $this->assertNull($oLang->getText("TEST_IDENT"));
    }

    /**
     * Testing Language::getModuleName()
     */
    public function testGetModuleName()
    {
        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getText"));
        $oLang
            ->method('getText')
            ->withConsecutive(['MOD_MODULE1'], ['MOD_MODULE2'], ['MOD_MODULE3'])
            ->willReturnOnConsecutiveCalls(
                'module1',
                'module2',
                'module3'
            );

        $this->assertEquals('module1', $oLang->getModuleName("module1"));
        $this->assertEquals('module2', $oLang->getModuleName("module2"));
        $this->assertEquals('module3', $oLang->getModuleName("module3"));
    }
}
