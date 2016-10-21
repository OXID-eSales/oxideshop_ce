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
namespace Unit\Setup;

require_once getShopBasePath() . '/Setup/functions.php';

use OxidEsales\EshopCommunity\Setup\Session as SetupSession;

/**
 * Language tests
 */
class LanguageTest extends \OxidTestCase
{

    /**
     * Test teardown
     *
     * @return null
     */
    protected function tearDown()
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
        $oSession->expects($this->at(0))->method("setSessionParam")->with($this->equalTo('setup_lang'));
        $oSession->expects($this->at(1))->method("getSessionParam")->with($this->equalTo('setup_lang'));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->at(0))->method("getRequestVar")->with($this->equalTo('setup_lang'), $this->equalTo('post'))->will($this->returnValue("de"));
        $oUtils->expects($this->at(1))->method("getRequestVar")->with($this->equalTo('setup_lang_submit'), $this->equalTo('post'))->will($this->returnValue("de"));

        $oSetup = $this->getMock("Setup", array("getStep"));
        $oSetup->expects($this->at(0))->method("getStep")->with($this->equalTo('STEP_WELCOME'));

        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getInstance", "setViewParam"));
        $oLang->expects($this->at(0))->method("getInstance")->with($this->equalTo('Session'))->will($this->returnValue($oSession));
        $oLang->expects($this->at(1))->method("getInstance")->with($this->equalTo('Utilities'))->will($this->returnValue($oUtils));
        $oLang->expects($this->at(2))->method("getInstance")->with($this->equalTo('Setup'))->will($this->returnValue($oSetup));
        $oLang->getLanguage();
    }

    /**
     * Testing Language::getSetupLang()
     *
     * @return null
     */
    public function testGetSetupLang()
    {
        $aLangs = array('en', 'de');
        $sBrowserLang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        $sBrowserLang = (in_array($sBrowserLang, $aLangs)) ? $sBrowserLang : $aLangs[0];

        $oSession = $this->getMock('SetupSession', array("setSessionParam", "getSessionParam"), array(), '', null);
        $oSession->expects($this->at(0))->method("getSessionParam")->with($this->equalTo('setup_lang'))->will($this->returnValue(null));
        $oSession->expects($this->at(1))->method("setSessionParam")->with($this->equalTo('setup_lang'), $this->equalTo($sBrowserLang));

        $oUtils = $this->getMock("Utilities", array("getRequestVar"));
        $oUtils->expects($this->at(0))->method("getRequestVar")->with($this->equalTo('setup_lang'), $this->equalTo('post'))->will($this->returnValue(null));

        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getInstance", "setViewParam"));
        $oLang->expects($this->at(0))->method("getInstance")->with($this->equalTo('Session'))->will($this->returnValue($oSession));
        $oLang->expects($this->at(1))->method("getInstance")->with($this->equalTo('Utilities'))->will($this->returnValue($oUtils));
        $oLang->getLanguage();
    }

    /**
     * Testing Language::getText()
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetModuleName()
    {
        $oLang = $this->getMock('OxidEsales\\EshopCommunity\\Setup\\Language', array("getText"));
        $oLang->expects($this->at(0))->method("getText")->with($this->equalTo('MOD_MODULE1'))->will($this->returnValue('module1'));
        $oLang->expects($this->at(1))->method("getText")->with($this->equalTo('MOD_MODULE2'))->will($this->returnValue('module2'));
        $oLang->expects($this->at(2))->method("getText")->with($this->equalTo('MOD_MODULE3'))->will($this->returnValue('module3'));
        $this->assertEquals('module1', $oLang->getModuleName("module1"));
        $this->assertEquals('module2', $oLang->getModuleName("module2"));
        $this->assertEquals('module3', $oLang->getModuleName("module3"));
    }
}
