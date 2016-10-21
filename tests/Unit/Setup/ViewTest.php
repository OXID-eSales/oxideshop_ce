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

use OxidEsales\EshopCommunity\Setup\View;
use OxidEsales\EshopCommunity\Setup\Session as SetupSession;

require_once getShopBasePath() . '/Setup/functions.php';


/**
 * view tests
 */
class ViewTest extends \OxidTestCase
{
    /**
     * Testing view::getTitle()
     *
     * @return null
     */
    public function testGetTitle()
    {
        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getText"));
        $oSetupView->expects($this->once())->method("getText")->with($this->equalTo(null), $this->equalTo(false))->will($this->returnValue("getText"));
        $this->assertEquals("getText", $oSetupView->getTitle());
    }

    /**
     * Testing view::setTitle()
     *
     * @return null
     */
    public function testSetTitle()
    {
        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getText"));
        $oSetupView->expects($this->once())->method("getText")->with($this->equalTo("testTitle"));
        $oSetupView->setTitle("testTitle");
        $oSetupView->getTitle();
    }

    /**
     * Testing view::getMessages()
     *
     * @return null
     */
    public function testGetMessages()
    {
        $oSetupView = new View();
        $this->assertEquals(array(), $oSetupView->getMessages());
    }

    /**
     * Testing view::setMessage()
     *
     * @return null
     */
    public function testSetMessage()
    {
        $oSetupView = new View();
        $oSetupView->setMessage("msg1");
        $this->assertEquals(array("msg1"), $oSetupView->getMessages());
        $oSetupView->setMessage("msg2");
        $this->assertEquals(array("msg1", "msg2"), $oSetupView->getMessages());
        $oSetupView->setMessage("msg3", true);
        $this->assertEquals(array("msg3"), $oSetupView->getMessages());
    }

    /**
     * Testing view::getText()
     *
     * @return null
     */
    public function testGetText()
    {
        $oInst = $this->getMock("Language", array("getText"));
        $oInst->expects($this->once())->method("getText")->with($this->equalTo("testId"));

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Language"))->will($this->returnValue($oInst));
        $oSetupView->getText("testId", false);
    }

    /**
     * Testing view::setViewParam() and view::getViewParam()
     *
     * @return null
     */
    public function testSetViewParamGetViewParam()
    {
        $oSetupView = new View();
        $oSetupView->setViewParam("testParamName", "testParamValue");
        $this->assertEquals("testParamValue", $oSetupView->getViewParam("testParamName"));
    }

    /**
     * Testing view::getSetupStep()
     *
     * @return null
     */
    public function testGetSetupStep()
    {
        $oInst = $this->getMock("Setup", array("getStep"));
        $oInst->expects($this->once())->method("getStep")->with($this->equalTo("testStepId"));

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oInst));
        $oSetupView->getSetupStep("testStepId", false);
    }

    /**
     * Testing view::getNextSetupStep()
     *
     * @return null
     */
    public function testGetNextSetupStep()
    {
        $oInst = $this->getMock("Setup", array("getNextStep"));
        $oInst->expects($this->once())->method("getNextStep");

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oInst));
        $oSetupView->getNextSetupStep();
    }

    /**
     * Testing view::getNextSetupStep()
     *
     * @return null
     */
    public function testGetCurrentSetupStep()
    {
        $oInst = $this->getMock("Setup", array("getCurrentStep"));
        $oInst->expects($this->once())->method("getCurrentStep");

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oInst));
        $oSetupView->getCurrentSetupStep();
    }

    /**
     * Testing view::getSetupSteps()
     *
     * @return null
     */
    public function testGetSetupSteps()
    {
        $oInst = $this->getMock("Setup", array("getSteps"));
        $oInst->expects($this->once())->method("getSteps");

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Setup"))->will($this->returnValue($oInst));
        $oSetupView->getSetupSteps();
    }

    /**
     * Testing view::getImageDir()
     *
     * @return null
     */
    public function testGetImageDir()
    {
        $oSetupView = new View();
        $this->assertEquals(getInstallPath() . 'out/admin/img', $oSetupView->getImageDir());
    }

    /**
     * Testing view::isDeletedSetup()
     *
     * @return null
     */
    public function testIsDeletedSetup()
    {
        $sPath = getShopBasePath();

        $oInst1 = $this->getMock('SetupSession', array("getSessionParam"), array(), '', null);
        $oInst1->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(array("dbiDemoData" => 0)));
        $oInst1->expects($this->at(1))->method("getSessionParam")->will($this->returnValue(array("blDelSetupDir" => true)));

        $oInst2 = $this->getMock("Utilities", array("removeDir"));
        $oInst2->expects($this->at(0))->method("removeDir")->with($this->equalTo($sPath . "out/pictures/generated"), $this->equalTo(true))->will($this->returnValue(true));
        $oInst2->expects($this->at(1))->method("removeDir")->with($this->equalTo($sPath . "out/pictures/master"), $this->equalTo(true), $this->equalTo(1), $this->equalTo(array("nopic.jpg")))->will($this->returnValue(true));
        $oInst2->expects($this->at(2))->method("removeDir")->with($this->equalTo($sPath . "Setup"), $this->equalTo(true))->will($this->returnValue(true));

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->at(0))->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oInst1));
        $oSetupView->expects($this->at(1))->method("getInstance")->with($this->equalTo("Utilities"))->will($this->returnValue($oInst2));
        $this->assertTrue($oSetupView->isDeletedSetup());
    }

    /**
     * Testing view::getReqInfoUrl()
     *
     * @return null
     */
    public function testGetReqInfoUrl()
    {
        $sUrl = "http://oxidforge.org/en/installation.html";

        $oSetupView = new View();
        $this->assertEquals($sUrl . "#PHP_version_at_least_5.3.25", $oSetupView->getReqInfoUrl("php_version", false));
        $this->assertEquals($sUrl, $oSetupView->getReqInfoUrl("none", false));
        $this->assertEquals($sUrl . "#Zend_Optimizer", $oSetupView->getReqInfoUrl("zend_optimizer", false));
    }

    /**
     * Testing view::getSid()
     *
     * @return null
     */
    public function testGetSid()
    {
        $oInst = $this->getMock('SetupSession', array("getSid"), array(), '', false);
        $oInst->expects($this->once())->method("getSid")->will($this->returnValue("testSid"));

        $oSetupView = $this->getMock("\\OxidEsales\\EshopCommunity\\Setup\\View", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("Session"))->will($this->returnValue($oInst));
        $this->assertEquals("testSid", $oSetupView->getSid(false));
    }
}
