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

require_once getShopBasePath() . '/setup/oxsetup.php';

/**
 * oxSetupView tests
 */
class Unit_Setup_oxSetupViewTest extends OxidTestCase
{

    /**
     * Testing oxSetupView::getTitle()
     *
     * @return null
     */
    public function testGetTitle()
    {
        $oSetupView = $this->getMock("oxsetupView", array("getText"));
        $oSetupView->expects($this->once())->method("getText")->with($this->equalTo(null), $this->equalTo(false))->will($this->returnValue("getText"));
        $this->assertEquals("getText", $oSetupView->getTitle());
    }

    /**
     * Testing oxSetupView::setTitle()
     *
     * @return null
     */
    public function testSetTitle()
    {
        $oSetupView = $this->getMock("oxsetupView", array("getText"));
        $oSetupView->expects($this->once())->method("getText")->with($this->equalTo("testTitle"));
        $oSetupView->setTitle("testTitle");
        $oSetupView->getTitle();
    }

    /**
     * Testing oxSetupView::getMessages()
     *
     * @return null
     */
    public function testGetMessages()
    {
        $oSetupView = new oxsetupView();
        $this->assertEquals(array(), $oSetupView->getMessages());
    }

    /**
     * Testing oxSetupView::setMessage()
     *
     * @return null
     */
    public function testSetMessage()
    {
        $oSetupView = new oxsetupView();
        $oSetupView->setMessage("msg1");
        $this->assertEquals(array("msg1"), $oSetupView->getMessages());
        $oSetupView->setMessage("msg2");
        $this->assertEquals(array("msg1", "msg2"), $oSetupView->getMessages());
        $oSetupView->setMessage("msg3", true);
        $this->assertEquals(array("msg3"), $oSetupView->getMessages());
    }

    /**
     * Testing oxSetupView::getText()
     *
     * @return null
     */
    public function testGetText()
    {
        $oInst = $this->getMock("oxSetupLang", array("getText"));
        $oInst->expects($this->once())->method("getText")->with($this->equalTo("testId"));

        $oSetupView = $this->getMock("oxSetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetupLang"))->will($this->returnValue($oInst));
        $oSetupView->getText("testId", false);
    }

    /**
     * Testing oxSetupView::setViewParam() and oxSetupView::getViewParam()
     *
     * @return null
     */
    public function testSetViewParamGetViewParam()
    {
        $oSetupView = new oxsetupView();
        $oSetupView->setViewParam("testParamName", "testParamValue");
        $this->assertEquals("testParamValue", $oSetupView->getViewParam("testParamName"));
    }

    /**
     * Testing oxSetupView::getSetupStep()
     *
     * @return null
     */
    public function testGetSetupStep()
    {
        $oInst = $this->getMock("oxSetup", array("getStep"));
        $oInst->expects($this->once())->method("getStep")->with($this->equalTo("testStepId"));

        $oSetupView = $this->getMock("oxsetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oInst));
        $oSetupView->getSetupStep("testStepId", false);
    }

    /**
     * Testing oxSetupView::getNextSetupStep()
     *
     * @return null
     */
    public function testGetNextSetupStep()
    {
        $oInst = $this->getMock("oxSetup", array("getNextStep"));
        $oInst->expects($this->once())->method("getNextStep");

        $oSetupView = $this->getMock("oxsetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oInst));
        $oSetupView->getNextSetupStep();
    }

    /**
     * Testing oxSetupView::getNextSetupStep()
     *
     * @return null
     */
    public function testGetCurrentSetupStep()
    {
        $oInst = $this->getMock("oxSetup", array("getCurrentStep"));
        $oInst->expects($this->once())->method("getCurrentStep");

        $oSetupView = $this->getMock("oxsetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oInst));
        $oSetupView->getCurrentSetupStep();
    }

    /**
     * Testing oxSetupView::getSetupSteps()
     *
     * @return null
     */
    public function testGetSetupSteps()
    {
        $oInst = $this->getMock("oxSetup", array("getSteps"));
        $oInst->expects($this->once())->method("getSteps");

        $oSetupView = $this->getMock("oxsetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oInst));
        $oSetupView->getSetupSteps();
    }

    /**
     * Testing oxSetupView::getImageDir()
     *
     * @return null
     */
    public function testGetImageDir()
    {
        $oSetupView = new oxsetupView();
        $this->assertEquals(getInstallPath() . 'out/admin/img', $oSetupView->getImageDir());
    }

    /**
     * Testing oxSetupView::isDeletedSetup()
     *
     * @return null
     */
    public function testIsDeletedSetup()
    {
        $sPath = getInstallPath();

        $oInst1 = $this->getMock("oxSetupSession", array("getSessionParam"), array(), '', null);
        $oInst1->expects($this->at(0))->method("getSessionParam")->will($this->returnValue(array("dbiDemoData" => 0)));
        $oInst1->expects($this->at(1))->method("getSessionParam")->will($this->returnValue(array("blDelSetupDir" => true)));

        $oInst2 = $this->getMock("oxSetupUtils", array("removeDir"));
        $oInst2->expects($this->at(0))->method("removeDir")->with($this->equalTo($sPath . "out/pictures_/generated"), $this->equalTo(true))->will($this->returnValue(true));
        $oInst2->expects($this->at(1))->method("removeDir")->with($this->equalTo($sPath . "out/pictures_/master"), $this->equalTo(true), $this->equalTo(1), $this->equalTo(array("nopic.jpg")))->will($this->returnValue(true));
        $oInst2->expects($this->at(2))->method("removeDir")->with($this->equalTo($sPath . "setup"), $this->equalTo(true))->will($this->returnValue(true));

        $oInst3 = $this->getMock("oxSetup", array("getVersionPrefix"));
        $oInst3->expects($this->once())->method("getVersionPrefix")->will($this->returnValue("_"));

        $oSetupView = $this->getMock("oxSetupView", array("getInstance"));
        $oSetupView->expects($this->at(0))->method("getInstance")->with($this->equalTo("OxSetupSession"))->will($this->returnValue($oInst1));
        $oSetupView->expects($this->at(1))->method("getInstance")->with($this->equalTo("oxSetupUtils"))->will($this->returnValue($oInst2));
        $oSetupView->expects($this->at(2))->method("getInstance")->with($this->equalTo("oxSetup"))->will($this->returnValue($oInst3));
        $this->assertTrue($oSetupView->isDeletedSetup());
    }

    /**
     * Testing oxSetupView::getReqInfoUrl()
     *
     * @return null
     */
    public function testGetReqInfoUrl()
    {
        $sUrl = "http://oxidforge.org/en/installation.html";

        $oSetupView = new oxsetupView();
        $this->assertEquals($sUrl . "#PHP_version_at_least_5.3.25", $oSetupView->getReqInfoUrl("php_version", false));
        $this->assertEquals($sUrl, $oSetupView->getReqInfoUrl("none", false));
        $this->assertEquals($sUrl . "#Zend_Optimizer", $oSetupView->getReqInfoUrl("zend_optimizer", false));
    }

    /**
     * Testing oxSetupView::getSid()
     *
     * @return null
     */
    public function testGetSid()
    {
        $oInst = $this->getMock("oxSetupSession", array("getSid"), array(), '', false);
        $oInst->expects($this->once())->method("getSid")->will($this->returnValue("testSid"));

        $oSetupView = $this->getMock("oxsetupView", array("getInstance"));
        $oSetupView->expects($this->once())->method("getInstance")->with($this->equalTo("oxSetupSession"))->will($this->returnValue($oInst));
        $this->assertEquals("testSid", $oSetupView->getSid(false));
    }
}
