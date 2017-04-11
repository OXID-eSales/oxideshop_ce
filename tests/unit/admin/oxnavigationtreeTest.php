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

class Unit_Admin_oxNavigationTreeTest extends OxidTestCase
{

    protected $_sWrongDynfile = 'wrongfile.xml';
    protected $_sValidDynfile = 'goodfile.xml';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();

        // files for test
        switch ($this->getName()) {
            case 'testCheckDynFileWrongFileContent' :
                // creating wrong file
                if ($rHandle = @fopen(oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sWrongDynfile}", 'w')) {
                    fwrite($rHandle, 'some wrong content');
                    fclose($rHandle);
                }
                break;
            case 'testCheckDynFileFileIsValidXml' :
                // creating valid file
                if ($rHandle = @fopen(oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sValidDynfile}", 'w')) {
                    fwrite($rHandle, '<?xml version="1.0" encoding="ISO-8859-15"?><OX>');
                    fclose($rHandle);
                }
                break;
        }
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        // deleting test files
        switch ($this->getName()) {
            case 'testCheckDynFileWrongFileContent' :
                @unlink(oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sWrongDynfile}");
                break;
            case 'testCheckDynFileFileIsValidXml' :
                // creating valid file
                @unlink(oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sValidDynfile}");
                break;
        }
        return parent::tearDown();
    }

    /**
     * OxNavigationTree::_addDynLinks() test case
     *
     * @return null
     */
    public function testAddDynLinks()
    {
        oxTestModules::addFunction("oxUtilsFile", "checkFile", "{ return true; }");
        modConfig::getInstance()->setConfigParam('sAdminDir', "admin");

        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <OXMENU type="dyn">
                   <MAINMENU>
                     <SUBMENU cl="login" clparam="loginParam"></SUBMENU>
                   </MAINMENU>
                   <MAINMENU>
                     <SUBMENU cl="oxadminview" clparam="oxadminviewParam"></SUBMENU>
                   </MAINMENU>
                   <MAINMENU>
                     <SUBMENU cl="oxadmindetails" clparam="oxadmindetailsParam"></SUBMENU>
                   </MAINMENU>
                   <MAINMENU>
                     <SUBMENU cl="oxadminlist" clparam="oxadminlistParam"></SUBMENU>
                   </MAINMENU>
                 </OXMENU>';

        $sRezXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <OXMENU type="dyn">
                   <MAINMENU id="dyn_menu">
                     <SUBMENU cl="login" clparam="loginParam" list="dynscreen_list" listparam="menu=login" link="index.php?cl=dynscreen&amp;menu=login&amp;loginParam">
                       <TAB external="true" location="pages/login_about.php" id="dyn_about" />
                       <TAB external="true" location="pages/login_technics.php" id="dyn_interface" />
                       <TAB id="dyn_interface" cl="login" />
                     </SUBMENU>
                   </MAINMENU>
                   <MAINMENU id="dyn_menu">
                     <SUBMENU cl="oxadminview" clparam="oxadminviewParam" list="dynscreen_list" listparam="menu=oxadminview" link="index.php?cl=dynscreen&amp;menu=oxadminview&amp;oxadminviewParam">
                       <TAB external="true" location="pages/oxadminview_about.php" id="dyn_about" />
                       <TAB external="true" location="pages/oxadminview_technics.php" id="dyn_interface" />
                       <TAB id="dyn_interface" cl="oxadminview" />
                     </SUBMENU>
                   </MAINMENU>
                   <MAINMENU id="dyn_menu">
                     <SUBMENU cl="oxadmindetails" clparam="oxadmindetailsParam" list="dynscreen_list" listparam="menu=oxadmindetails" link="index.php?cl=dynscreen&amp;menu=oxadmindetails&amp;oxadmindetailsParam">
                       <TAB external="true" location="pages/oxadmindetails_about.php" id="dyn_about" />
                       <TAB external="true" location="pages/oxadmindetails_technics.php" id="dyn_interface" />
                       <TAB id="dyn_interface" cl="oxadmindetails" />
                     </SUBMENU>
                   </MAINMENU>
                   <MAINMENU id="dyn_menu">
                     <SUBMENU cl="oxadminlist" clparam="oxadminlistParam" list="dynscreen_list" listparam="menu=oxadminlist" link="index.php?cl=dynscreen&amp;menu=oxadminlist&amp;oxadminlistParam">
                       <TAB external="true" location="pages/oxadminlist_about.php" id="dyn_about" />
                       <TAB external="true" location="pages/oxadminlist_technics.php" id="dyn_interface" />
                       <TAB id="dyn_interface" cl="oxadminlist" />
                     </SUBMENU>
                   </MAINMENU>
                 </OXMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oRezDom = new DOMDocument();
        $oRezDom->formatOutput = true;
        $oRezDom->loadXML($sRezXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_getAdminUrl"));
        $oNavTree->expects($this->never())->method('_getAdminUrl');
        $oNavTree->UNITaddDynLinks($oDom);
        $this->assertEquals(str_replace(array("\t", " ", "\n", "\r"), "", $oRezDom->saveXML()), str_replace(array("\t", " ", "\n", "\r"), "", $oDom->saveXML()));

    }

    /**
     * OxNavigationTree::getDomXml() test case
     *
     * @return null
     */
    public function testGetDomXml()
    {
        $aTestMethods = array("_getInitialDom", "_checkGroups", "_checkRights", "_checkDemoShopDenials", "_cleanEmptyParents", "removeInvisibleMenuNodes");


        $oNavTree = $this->getMock("oxnavigationtree", $aTestMethods);
        $oNavTree->expects($this->once())->method('_getInitialDom')->will($this->returnValue(new stdClass));
        $oNavTree->expects($this->once())->method('_checkGroups');
        $oNavTree->expects($this->once())->method('_checkRights');
        $oNavTree->expects($this->once())->method('_checkDemoShopDenials');
        $oNavTree->expects($this->once())->method('removeInvisibleMenuNodes');
        $oNavTree->expects($this->exactly(2))->method('_cleanEmptyParents');


        $oNavTree->getDomXml();
    }

    /**
     * OxNavigationTree::_getDynMenuUrl() test case
     *
     * @return null
     */
    public function testGetDynMenuUrl()
    {
        $iLang = 0;

        $oAdminView = new oxadminview();
        $sDynscreenUrl = $oAdminView->getServiceUrl($iLang) . "menue/dynscreen.xml";
        $sDynscreenLocalUrl = getShopBasePath() . "application/views/admin/dynscreen_local.xml";

        $oNavTree = new oxnavigationtree();
        $this->assertEquals($sDynscreenUrl, $oNavTree->UNITgetDynMenuUrl($iLang, true));
        $this->assertEquals($sDynscreenLocalUrl, $oNavTree->UNITgetDynMenuUrl($iLang, false));
    }

    /**
     * OxNavigationTree::_hasGroup() test case
     *
     * @return null
     */
    public function testHasGroup()
    {
        $oUser = $this->getMock("oxuser", array("inGroup"));
        $oUser->expects($this->once())->method('inGroup')->with($this->equalTo("testGroupId"))->will($this->returnValue(true));

        $oNavTree = $this->getMock("oxnavigationtree", array("getUser"));
        $oNavTree->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oNavTree->UNIThasGroup("testGroupId"));
    }

    /**
     * OxNavigationTree::_hasRights() test case
     *
     * @return null
     */
    public function testHasRights()
    {
        $oUser = new oxuser();
        $oUser->oxuser__oxrights = new oxField("testRights");

        $oNavTree = $this->getMock("oxnavigationtree", array("getUser"));
        $oNavTree->expects($this->once())->method('getUser')->will($this->returnValue($oUser));
        $this->assertTrue($oNavTree->UNIThasRights("testRights"));
    }

    /**
     * OxNavigationTree::getEditUrl() test case
     *
     * @return null
     */
    public function testGetEditUrl()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" clparam="testTabParam2"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $this->assertEquals("cl=testTabClass2&testTabParam2", $oNavTree->getEditUrl("testClass", 1));
    }

    /**
     * check if the external url read out correct
     *
     * @return null
     */
    public function testGetEditUrlExternal()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" external="1" location="testExternalUrl"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $this->assertEquals("testExternalUrl", $oNavTree->getEditUrl("testClass", 1));
    }

    /**
     * OxNavigationTree::getListUrl() test case
     *
     * @return null
     */
    public function testGetListUrl()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <SUBMENU cl="testClass" list="testClass" listparam="testClassParam">
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $this->assertEquals("cl=testClass&testClassParam", $oNavTree->getListUrl("testClass"));
    }

    /**
     * OxNavigationTree::getListNodes() test case
     *
     * @return null
     */
    public function testGetListNodes()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <MAINMENU>
                   <SUBMENU cl="testClass1"><TAB></TAB></SUBMENU>
                   <SUBMENU cl="testClass2"><TAB></TAB></SUBMENU>
                 </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNodeList = $oNavTree->getListNodes(array("testClass1", "testClass2"));

        $this->assertEquals(2, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertEquals("testClass1", $oNode->getAttribute("cl"));

        $oNode = $oNodeList->item(1);
        $this->assertNotNull($oNode);
        $this->assertEquals("testClass2", $oNode->getAttribute("cl"));
    }

    /**
     * OxNavigationTree::markNodeActive() test case
     *
     * @return null
     */
    public function testMarkNodeActive()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <MAINMENU>
                   <SUBMENU cl="testClass1"><TAB></TAB></SUBMENU>
                   <SUBMENU cl="testClass2" list="testClass1"><TAB></TAB></SUBMENU>
                 </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        // checking if attribute is not set
        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1']");

        $this->assertEquals(1, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertEquals("", $oNode->getAttribute("active"));

        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1' or @list='testClass1']");

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNavTree->markNodeActive("testClass1");

        // checking if attribute is set correct
        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1' or @list='testClass1']");

        $this->assertEquals(2, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertEquals("1", $oNode->getAttribute("active"));

        $oNode = $oNodeList->item(1);
        $this->assertNotNull($oNode);
        $this->assertEquals("1", $oNode->getAttribute("active"));
    }

    /**
     * OxNavigationTree::getBtn() test case
     *
     * @return null
     */
    public function testGetBtn()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                   <SUBMENU>
                     <TAB cl="testClass" />
                     <BTN id="testBtn1" />
                     <BTN id="testBtn2" />
                   </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oBtnList = $oNavTree->getBtn("testClass");

        $this->assertNotNull($oBtnList);
        $this->assertTrue($oBtnList instanceof stdClass);
        $this->assertNotNull($oBtnList->testBtn1);
        $this->assertEquals(1, $oBtnList->testBtn1);
        $this->assertNotNull($oBtnList->testBtn2);
        $this->assertEquals(1, $oBtnList->testBtn2);
    }

    /**
     * OxNavigationTree::getActiveTab() test case
     *
     * @return null
     */
    public function testGetActiveTab()
    {
        $oTab = $this->getMock("stdClass", array("getAttribute"));
        $oTab->expects($this->once())->method('getAttribute')->will($this->returnValue("testClassName"));

        $oTabs = $this->getMock("stdClass", array("item"));
        $oTabs->expects($this->once())->method('item')->will($this->returnValue($oTab));
        $oTabs->length = 2;

        $oNavTree = $this->getMock("oxnavigationtree", array("getTabs"));
        $oNavTree->expects($this->once())->method('getTabs')->will($this->returnValue($oTabs));

        $this->assertEquals("testClassName", $oNavTree->getActiveTab("testClass", 1));
    }

    /**
     * OxNavigationTree::getTabs() test case
     * test if the returned value of tabs equals the expected amount.
     *
     * @return null
     */
    public function testGetTabs()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" external="1" location="testExternalUrl"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("getDomXml"));
        $oNavTree->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oTabs = $oNavTree->getTabs("testClass", 1, true);

        $this->assertNotNull($oTabs);
        $this->assertEquals(2, $oTabs->length);
        $oTab = $oTabs->item(0);
        $this->assertNotNull($oTab);
        $this->assertEquals("testTabClass1", $oTab->getAttribute("cl"));

        $oTab = $oTabs->item(1);
        $this->assertNotNull($oTab);
        $this->assertEquals("testTabClass2", $oTab->getAttribute("cl"));
        $this->assertEquals(1, $oTab->getAttribute("active"));

    }

    /**
     * OxNavigationTree::_copyAttributes() test case
     *
     * @return null
     */
    public function testCopyAttributes()
    {
        $oAttr1 = new stdClass();
        $oAttr1->nodeName = 'nodeName1';
        $oAttr1->nodeValue = 'nodeValue1';

        $oAttr2 = new stdClass();
        $oAttr2->nodeName = 'nodeName2';
        $oAttr2->nodeValue = 'nodeValue2';

        $oDomElemFrom = new stdClass();
        $oDomElemFrom->attributes = array($oAttr1, $oAttr2);

        $oDomElemTo = $this->getMock("stdClass", array("setAttribute"));
        $oDomElemTo->expects($this->at(0))->method('setAttribute')->with($this->equalTo('nodeName1'), $this->equalTo('nodeValue1'));
        $oDomElemTo->expects($this->at(1))->method('setAttribute')->with($this->equalTo('nodeName2'), $this->equalTo('nodeValue2'));

        $oNavTree = new oxnavigationtree();
        $oNavTree->UNITcopyAttributes($oDomElemTo, $oDomElemFrom);
    }

    /**
     * OxNavigationTree::_checkGroups() test case
     *
     * @return null
     */
    public function testCheckGroups()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                   <MAINMENU>
                     <SUBMENU cl="testClass1" group="testGroup1">
                       <TAB cl="testTabClass1" />
                     </SUBMENU>
                     <SUBMENU cl="testClass2" nogroup="testGroup2">
                       <TAB cl="testTabClass2" />
                     </SUBMENU>
                   </MAINMENU>';

        $sResXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?><MAINMENU>   </MAINMENU>";

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_hasGroup"));
        $oNavTree->expects($this->at(0))->method('_hasGroup')->will($this->returnValue(false));
        $oNavTree->expects($this->at(1))->method('_hasGroup')->will($this->returnValue(true));
        $oNavTree->UNITcheckGroups($oDom);
        $this->assertEquals(str_replace(array("\t", " ", "\n", "\r"), "", $sResXml), str_replace(array("\t", " ", "\n", "\r"), "", $oDom->saveXML()));
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case when menu is marked as invisible,
     * also if it marked as visible and default behaviour, if attribute visible is not present.
     *
     * @return null
     */
    public function testRemoveInvisibleMenuNodes()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                   <MAINMENU>
                     <SUBMENU cl="MenuEntry-Visible" visible="1">
                       <TAB cl="MenuTab-AVisible" />
                     </SUBMENU>
                     <SUBMENU cl="MenuEntry-NotVisible" visible="0">
                       <TAB cl="MenuTab-NotVisible" />
                     </SUBMENU>
                     <SUBMENU cl="MenuEntry-DefaultVisibility">
                       <TAB cl="MenuTab-DefaultVisibility" />
                     </SUBMENU>
                   </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_getInitialDom"));
        $oNavTree->expects($this->any())->method('_getInitialDom')->will($this->returnValue($oDom));
        $oRDom = $oNavTree->getDomXml();

        $aExpectedMenuClasses = array("MenuEntry-Visible", "MenuEntry-DefaultVisibility");
        foreach ($oRDom->documentElement->childNodes as $menuItem) {
            if ($menuItem->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($menuItem->getAttribute('cl'), $aExpectedMenuClasses);
            }
        }
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case wen main menu is marked as invisible.
     *
     * @return null
     */
    public function testRemoveInvisibleMainMenuNodes()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                    <OXMENU id="NAVIGATION_ESHOPADMIN">
                       <MAINMENU id="MainMenu-Visible">
                           <SUBMENU cl="MenuEntry-Visible" visible="1">
                               <TAB cl="MenuTab-Visible" />
                           </SUBMENU>
                           <SUBMENU cl="MenuEntry-DefaultVisibility">
                               <TAB cl="MenuTab-DefaultVisibility" />
                           </SUBMENU>
                       </MAINMENU>
                       <MAINMENU id="MainMenu-NotVisible" visible="0">
                           <SUBMENU cl="MenuEntry-NotVisible" visible="0">
                             <TAB cl="MenuTab-NotVisible" />
                           </SUBMENU>
                       </MAINMENU>
                    </OXMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_getInitialDom"));
        $oNavTree->expects($this->any())->method('_getInitialDom')->will($this->returnValue($oDom));
        $oRDom = $oNavTree->getDomXml();

        $aExpectedMenuItems = array("MainMenu-Visible");
        foreach ($oRDom->documentElement->childNodes as $menuNode) {
            if ($menuNode->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($menuNode->getAttribute('id'), $aExpectedMenuItems);
            }
        }
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case when tab is marked as not visible.
     *
     * @return null
     */
    public function testRemoveInvisibleTabs()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                   <MAINMENU>
                     <SUBMENU cl="MenuEntry-Visible">
                       <TAB cl="MenuTab-Visible" visible="1" />
                     </SUBMENU>
                     <SUBMENU cl="MenuEntry-VisibleTwo">
                       <TAB cl="MenuTab-NotVisible" visible="0" />
                     </SUBMENU>
                     <SUBMENU cl="MenuEntry-DefaultVisibility">
                       <TAB cl="MenuTab-DefaultVisibility" />
                     </SUBMENU>
                   </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_getInitialDom"));
        $oNavTree->expects($this->any())->method('_getInitialDom')->will($this->returnValue($oDom));
        $oRDom = $oNavTree->getDomXml();

        $aExpectedMenuClasses = array("MenuTab-Visible", null, "MenuTab-DefaultVisibility");
        foreach ($oRDom->documentElement->childNodes as $menuItem) {
            if ($menuItem->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($oNavTree->getActiveTab($menuItem->getAttribute('cl'), 0), $aExpectedMenuClasses);
            }
        }
    }

    /**
     * OxNavigationTree::_checkRights() test case
     *
     * @return null
     */
    public function testCheckRights()
    {
        $sXml = '<?xml version="1.0" encoding="ISO-8859-15"?>
                   <MAINMENU>
                     <SUBMENU cl="testClass1" rights="testGroup1">
                       <TAB cl="testTabClass1" />
                     </SUBMENU>
                     <SUBMENU cl="testClass2" norights="testGroup2">
                       <TAB cl="testTabClass2" />
                     </SUBMENU>
                   </MAINMENU>';

        $sResXml = "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?><MAINMENU>   </MAINMENU>";

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock("oxnavigationtree", array("_hasRights"));
        $oNavTree->expects($this->at(0))->method('_hasRights')->will($this->returnValue(false));
        $oNavTree->expects($this->at(1))->method('_hasRights')->will($this->returnValue(true));
        $oNavTree->UNITcheckRights($oDom);
        $this->assertEquals(str_replace(array("\t", " ", "\n", "\r"), "", $sResXml), str_replace(array("\t", " ", "\n", "\r"), "", $oDom->saveXML()));
    }

    /**
     * OxNavigationTree::_checkDynFile() test case
     * testing new functionality
     * dyn file must not be created if content is empty
     *
     * @return null
     */
    public function testCheckDynFileFileDoesNotExist()
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sCompileDir') . "xxx.file";
        $oNavTree = new oxnavigationtree();
        $this->assertNull($oNavTree->UNITcheckDynFile($sFilePath));

    }

    /**
     * OxNavigationTree::_checkDynFile() test case
     * dyn file must not be created if content is not valid
     *
     * @return null
     */
    public function testCheckDynFileWrongFileContent()
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sWrongDynfile}";
        $oNavTree = new oxnavigationtree();
        $this->assertNull($oNavTree->UNITcheckDynFile($sFilePath));
    }

    /**
     * OxNavigationTree::_checkDynFile() test case
     * wheter content is a valid xml file, same content will return
     *
     * @return null
     */
    public function testCheckDynFileFileIsValidXml()
    {
        $sFilePath = oxRegistry::getConfig()->getConfigParam('sCompileDir') . "{$this->_sValidDynfile}";
        $oNavTree = new oxnavigationtree();
        $this->assertEquals($sFilePath, $oNavTree->UNITcheckDynFile($sFilePath));
    }

    /**
     * test if the method find all denial link in menu xml
     *
     * @param object $oDom     XML Dom Object
     * @param int    $iNeedCnt amount of 'to remove links'
     *
     * @return null
     */
    protected function _checkDemoShopDenialsInMenuXml($oDom, $iNeedCnt)
    {
        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//*[@disableForDemoShop]");
        $iFoundCnt = 0;

        foreach ($oNodeList as $oNode) {
            if ($oNode->getAttribute('disableForDemoShop')) {
                $iFoundCnt++;
            }
        }

        $this->assertEquals($iNeedCnt, $iFoundCnt);
    }

    /**
     * call the test method Unit_Admin_oxNavigationTreeTest::_checkDemoShopDenialsInMenuXml()
     *
     * @return null
     */
    public function testcheckDemoShopDenialsDefaultMenuXml()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->_getDomXml();

        $this->_checkDemoShopDenialsInMenuXml($oDom, 4);
    }

    /**
     * test if not all denial links removed when it isn't a demoshop
     *
     * @return null
     */
    public function testcheckDemoShopDenialsDefaultNormal()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->_getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '1');
        }

        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 4);

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDemoShop'))->will($this->returnValue(false));
        $oNavTree->setConfig($oConfig);
        $oNavTree->UNITcheckDemoShopDenials($oDom);

        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 4);
    }

    /**
     * test if not all denial links are removed when it is a demoshop
     *
     * @return null
     */
    public function testcheckDemoShopDenialsDefaultDemo()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->_getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '1');
        }
        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 4, '1');

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDemoShop'))->will($this->returnValue(true));
        $oNavTree->setConfig($oConfig);
        $oNavTree->UNITcheckDemoShopDenials($oDom);

        // removed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if no link is removed when it isn't a demoshop
     *
     * @return null
     */
    public function testcheckDemoShopDenialsInverseNormal()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->_getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '0');
        }
        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 0);

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDemoShop'))->will($this->returnValue(false));
        $oNavTree->setConfig($oConfig);
        $oNavTree->UNITcheckDemoShopDenials($oDom);

        // removed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if no link is removed when it is a demoshop
     *
     * @return null
     */
    public function testcheckDemoShopDenialsInverseDemo()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->_getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '0');
        }
        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 0);

        $oConfig = $this->getMock('oxconfig', array('getConfigParam'));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo('blDemoShop'))->will($this->returnValue(true));
        $oNavTree->setConfig($oConfig);
        $oNavTree->UNITcheckDemoShopDenials($oDom);

        // not changed
        $this->_checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if its possible to load the menu_xx.xml.
     * if all is ok, the method returns a dom object, else return nothing.
     *
     * @return object | null
     */
    protected function _getDomXml()
    {
        $sFullAdminDir = getShopBasePath() . '/application/views/admin';

        $sMenuFile = '/menu_ce.xml';

        $sFile = $sFullAdminDir . $sMenuFile;
        if (!file_exists($sFile) && file_exists($sFullAdminDir . '/menu.xml')) {
            $sFile = $sFullAdminDir . '/menu.xml';
        } elseif (file_exists($sFile) && !file_exists($sFullAdminDir . '/menu.xml')) {
            // all ok
        } else {
            $this->fail("menu.xml not found");
        }

        $oDomFile = new DomDocument();
        $oDomFile->preserveWhiteSpace = false;
        if (@$oDomFile->load($sFile)) {
            $oDom = new DOMDocument();
            $oDom->appendChild(new DOMElement('OX'));
            $oXPath = new DOMXPath($oDom);
            oxNew('oxNavigationTree')->UNITmergeNodes($oDom->documentElement, $oDomFile->documentElement, $oXPath, $oDom, '/OX');

            return $oDom;
        }
        $this->fail("menu.xml not found bad");
    }

    /**
     * test if empty node can removed.
     *
     * @return null
     */
    public function testCleanEmptyParents()
    {
        $oDom = $this->_getDomXml();

        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);

        // remove children
        foreach ($oXPath->query("//SUBMENU[@id='mxcoresett']/TAB") as $oNode) {
            $oNode->parentNode->removeChild($oNode);
        }

        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);

        oxNew('oxNavigationTree')->UNITcleanEmptyParents($oDom, '//SUBMENU[@id][@list]', 'TAB');
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertEquals(0, $oNodeList->length);
    }

    /**
     * test if the right class id will read out from a node
     *
     * @return null
     */
    public function testGetClassIdTakesFromOriginalXml()
    {
        modConfig::getInstance()->setConfigParam("blUseRightsRoles", true);

        $oNavTree = $this->getMock('oxNavigationTree', array("isAdmin"));
        $oNavTree->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oNavTree->getConfig()->setConfigParam('sAdminDir', 'admin');

        $this->assertEquals('mxcoresett', $oNavTree->getClassId('shop'));

        // now delete from dom
        $oDom = $oNavTree->getDomXml();
        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);
        foreach ($oNodeList as $oNode) {
            $oNode->parentNode->removeChild($oNode);
        }

        // check if not changed
        $this->assertEquals('mxcoresett', $oNavTree->getClassId('shop'));
    }

    /**
     * test if the admin URL will read out correct from config
     *
     * @return null
     */
    public function testGetAdminUrl1()
    {
        modConfig::getInstance()->setConfigParam("sAdminSSLURL", "testAdminSslUrl");

        $oNavTree = new oxnavigationtree();
        $this->assertEquals("testAdminSslUrl/index.php?", $oNavTree->UNITgetAdminUrl());
    }

    /**
     * test if the admin URL will read out correct from session
     *
     * @return null
     */
    public function testGetAdminUrl()
    {
        $oUU = $this->getMock('oxUtilsUrl', array('processUrl'));
        $oUU->expects($this->any())->method('processUrl')
            ->with($this->anything(), $this->equalTo(false))
            ->will($this->returnValue('sess:url?'));
        //modInstances::addMod('oxUtilsUrl', $oUU);
        oxTestModules::addModuleObject('oxUtilsUrl', $oUU);

        $o = new oxNavigationTree();

        $this->assertEquals('sess:url?', $o->UNITgetAdminUrl());
    }

    /**
     * OxNavigationTree::_processCachedFile() test case
     *
     * @return null
     */
    public function testProcessCachedFile()
    {
        $sString = 'http://url/lala?stoken=ASDddddd2454&amp;amp;&amp;lala';

        $o = $this->getMock('oxNavigationTree', array('_getAdminUrl'));
        $o->expects($this->never())->method('_getAdminUrl');

        $this->assertEquals($sString, $o->UNITprocessCachedFile($sString));
    }

    /**
     * test if parameter add correct to URL
     *
     * @return null
     */
    public function testSessionizeLocalUrls()
    {
        $oDom = new DOMDocument();
        $oEl1 = $oDom->createElement('OX');
        $oDom->appendChild($oEl1);
        $oEl2 = $oDom->createElement('OXMENU');
        $oEl1->appendChild($oEl2);

        $oEl31 = $oDom->createElement('MAINMENU');
        $oEl2->appendChild($oEl31);
        $oEl31->setAttribute('url', 'http://xxx');

        $oEl32 = $oDom->createElement('MAINMENU');
        $oEl2->appendChild($oEl32);
        $oEl32->setAttribute('url', 'index.php?loaa');


        $oCDom = clone $oDom;
        $o = $this->getMock('oxNavigationTree', array('_getAdminUrl'));
        $o->expects($this->once())->method('_getAdminUrl')->will($this->returnValue('http://url/lala?stoken=TOKEN111454&'));
        $o->UNITsessionizeLocalUrls($oCDom);

        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//*[@url=\"index.php?loaa\"]");
        $this->assertEquals(1, $oNodeList->length);
        $oNodeList->item(0)->setAttribute('url', 'http://url/lala?stoken=TOKEN111454&loaa');
        $this->assertEquals($oDom, $oCDom);
    }

    /**
     * OxNavigationTree::_mergeNodes() test case
     *
     * @return null
     */
    public function testMergeNodes()
    {
        $oNode1 = $this->getMock("stdClass", array("getAttribute"));
        $oNode1->expects($this->once())->method('getAttribute')->will($this->returnValue('testAttribute1'));
        $oNode1->nodeType = XML_ELEMENT_NODE;
        $oNode1->tagName = 'testTagName1';
        $oNode1->childNodes = new stdClass();
        $oNode1->childNodes->length = 1;

        $oNode2 = $this->getMock("stdClass", array("getAttribute"));
        $oNode2->expects($this->once())->method('getAttribute')->will($this->returnValue('testAttribute2'));
        $oNode2->nodeType = XML_ELEMENT_NODE;
        $oNode2->tagName = 'testTagName2';
        $oNode2->childNodes = new stdClass();
        $oNode2->childNodes->length = 1;

        $oDomElemTo = $this->getMock("stdClass", array("appendChild"));
        $oDomElemTo->expects($this->once())->method('appendChild');

        $oCurNode1 = $this->getMock("stdClass", array("item"));
        $oCurNode1->expects($this->never())->method('item');
        $oCurNode1->length = 0;

        $oCurNode2 = $this->getMock("stdClass", array("item"));
        $oCurNode2->expects($this->once())->method('item')->will($this->returnValue("childNode"));
        $oCurNode2->length = 1;

        $oXPathTo = $this->getMock("stdClass", array("query"));
        $oXPathTo->expects($this->at(0))->method('query')->will($this->returnValue($oCurNode1));
        $oXPathTo->expects($this->at(1))->method('query')->will($this->returnValue($oCurNode2));

        $oDomDocTo = $this->getMock("stdClass", array("importNode"));
        $oDomDocTo->expects($this->at(0))->method('importNode');

        $oDomElemFrom = new stdClass();
        $oDomElemFrom->childNodes = array($oNode1, $oNode2);

        $oTree = $this->getMock("oxNavigationTree", array("_copyAttributes"));
        $oTree->expects($this->once())->method('_copyAttributes');
        $oTree->UNITmergeNodes($oDomElemTo, $oDomElemFrom, $oXPathTo, $oDomDocTo, $sQueryStart);
    }

    /**
     * OxNavigationTree::init() test case
     *
     * @return null
     */
    public function testInit()
    {
        $oTree = new oxNavigationTree();
        if (method_exists($oTree, "init")) {
            $this->assertNull($oTree->init());
        }
    }
}
