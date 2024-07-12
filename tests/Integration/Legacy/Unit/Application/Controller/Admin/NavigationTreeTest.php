<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use DOMDocument;
use DOMElement;
use DOMXPath;
use oxField;
use OxidEsales\Eshop\Core\Base;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use oxTestModules;
use stdClass;

class NavigationTreeTest extends \PHPUnit\Framework\TestCase
{
    use ContainerTrait;

    /**
     * OxNavigationTree::getDomXml() test case
     */
    public function testGetDomXml()
    {
        $aTestMethods = ["getInitialDom", "checkGroups", "checkRights", "checkDemoShopDenials", "cleanEmptyParents", "removeInvisibleMenuNodes"];

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, $aTestMethods);
        $oNavTree->expects($this->once())->method('getInitialDom')->willReturn(new stdClass());
        $oNavTree->expects($this->once())->method('checkGroups');
        $oNavTree->expects($this->once())->method('checkRights');
        $oNavTree->expects($this->once())->method('checkDemoShopDenials');
        $oNavTree->expects($this->once())->method('removeInvisibleMenuNodes');
        $oNavTree->expects($this->exactly(2))->method('cleanEmptyParents');

        $oNavTree->getDomXml();
    }

    /**
     * OxNavigationTree::_hasGroup() test case
     */
    public function testHasGroup()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ["inGroup"]);
        $oUser->expects($this->once())->method('inGroup')->with("testGroupId")->willReturn(true);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getUser"]);
        $oNavTree->expects($this->once())->method('getUser')->willReturn($oUser);
        $this->assertTrue($oNavTree->hasGroup("testGroupId"));
    }

    /**
     * OxNavigationTree::_hasRights() test case
     */
    public function testHasRights()
    {
        $oUser = oxNew('oxuser');
        $oUser->oxuser__oxrights = new oxField("testRights");

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getUser"]);
        $oNavTree->expects($this->once())->method('getUser')->willReturn($oUser);
        $this->assertTrue($oNavTree->hasRights("testRights"));
    }

    /**
     * OxNavigationTree::getEditUrl() test case
     */
    public function testGetEditUrl()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" clparam="testTabParam2"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $this->assertSame("cl=testTabClass2&testTabParam2", $oNavTree->getEditUrl("testClass", 1));
    }

    /**
     * check if the external url read out correct
     */
    public function testGetEditUrlExternal()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" external="1" location="testExternalUrl"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $this->assertSame("testExternalUrl", $oNavTree->getEditUrl("testClass", 1));
    }

    /**
     * OxNavigationTree::getListUrl() test case
     */
    public function testGetListUrl()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <SUBMENU cl="testClass" list="testClass" listparam="testClassParam">
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $this->assertSame("cl=testClass&testClassParam", $oNavTree->getListUrl("testClass"));
    }

    /**
     * OxNavigationTree::getListNodes() test case
     */
    public function testGetListNodes()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <MAINMENU>
                   <SUBMENU cl="testClass1"><TAB></TAB></SUBMENU>
                   <SUBMENU cl="testClass2"><TAB></TAB></SUBMENU>
                 </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oNodeList = $oNavTree->getListNodes(["testClass1", "testClass2"]);

        $this->assertSame(2, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertSame("testClass1", $oNode->getAttribute("cl"));

        $oNode = $oNodeList->item(1);
        $this->assertNotNull($oNode);
        $this->assertSame("testClass2", $oNode->getAttribute("cl"));
    }

    /**
     * OxNavigationTree::markNodeActive() test case
     */
    public function testMarkNodeActive()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <MAINMENU>
                   <SUBMENU cl="testClass1"><TAB></TAB></SUBMENU>
                   <SUBMENU cl="testClass2" list="testClass1"><TAB></TAB></SUBMENU>
                 </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        // checking if attribute is not set
        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1']");

        $this->assertSame(1, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertSame("", $oNode->getAttribute("active"));

        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1' or @list='testClass1']");

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oNavTree->markNodeActive("testClass1");

        // checking if attribute is set correct
        $oXPath = new DOMXPath($oDom);
        $oNodeList = $oXPath->query("//*[@cl='testClass1' or @list='testClass1']");

        $this->assertSame(2, $oNodeList->length);
        $oNode = $oNodeList->item(0);
        $this->assertNotNull($oNode);
        $this->assertSame("1", $oNode->getAttribute("active"));

        $oNode = $oNodeList->item(1);
        $this->assertNotNull($oNode);
        $this->assertSame("1", $oNode->getAttribute("active"));
    }

    /**
     * OxNavigationTree::getBtn() test case
     */
    public function testGetBtn()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                   <SUBMENU>
                     <TAB cl="testClass" />
                     <BTN id="testBtn1" />
                     <BTN id="testBtn2" />
                   </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oBtnList = $oNavTree->getBtn("testClass");

        $this->assertNotNull($oBtnList);
        $this->assertInstanceOf(\stdClass::class, $oBtnList);
        $this->assertNotNull($oBtnList->testBtn1);
        $this->assertSame(1, $oBtnList->testBtn1);
        $this->assertNotNull($oBtnList->testBtn2);
        $this->assertSame(1, $oBtnList->testBtn2);
    }

    /**
     * OxNavigationTree::getActiveTab() test case
     */
    public function testGetActiveTab()
    {
        $oTab = $this->getMock("stdClass", ["getAttribute"]);
        $oTab->expects($this->once())->method('getAttribute')->willReturn("testClassName");

        $oTabs = $this->getMock("stdClass", ["item"]);
        $oTabs->expects($this->once())->method('item')->willReturn($oTab);
        $oTabs->length = 2;

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getTabs"]);
        $oNavTree->expects($this->once())->method('getTabs')->willReturn($oTabs);

        $this->assertSame("testClassName", $oNavTree->getActiveTab("testClass", 1));
    }

    /**
     * OxNavigationTree::getTabs() test case
     * test if the returned value of tabs equals the expected amount.
     */
    public function testGetTabs()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                 <SUBMENU cl="testClass">
                   <TAB cl="testTabClass1" clparam="testTabParam1"></TAB>
                   <TAB cl="testTabClass2" external="1" location="testExternalUrl"></TAB>
                 </SUBMENU>';

        $oDom = new DOMDocument();
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml"]);
        $oNavTree->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oTabs = $oNavTree->getTabs("testClass", 1, true);

        $this->assertNotNull($oTabs);
        $this->assertSame(2, $oTabs->length);
        $oTab = $oTabs->item(0);
        $this->assertNotNull($oTab);
        $this->assertSame("testTabClass1", $oTab->getAttribute("cl"));

        $oTab = $oTabs->item(1);
        $this->assertNotNull($oTab);
        $this->assertSame("testTabClass2", $oTab->getAttribute("cl"));
        $this->assertSame(1, $oTab->getAttribute("active"));
    }

    /**
     * OxNavigationTree::_copyAttributes() test case
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
        $oDomElemFrom->attributes = [$oAttr1, $oAttr2];

        $oDomElemTo = $this->getMock("stdClass", ["setAttribute"]);
        $oDomElemTo
            ->method('setAttribute')
            ->withConsecutive(['nodeName1', 'nodeValue1'], ['nodeName2', 'nodeValue2']);

        $oNavTree = oxNew('oxnavigationtree');
        $oNavTree->copyAttributes($oDomElemTo, $oDomElemFrom);
    }

    /**
     * OxNavigationTree::_checkGroups() test case
     */
    public function testCheckGroups()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                   <MAINMENU>
                     <SUBMENU cl="testClass1" group="testGroup1">
                       <TAB cl="testTabClass1" />
                     </SUBMENU>
                     <SUBMENU cl="testClass2" nogroup="testGroup2">
                       <TAB cl="testTabClass2" />
                     </SUBMENU>
                   </MAINMENU>';

        $sResXml = '<?xml version="1.0" encoding="UTF-8"?><MAINMENU>   </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["hasGroup"]);
        $oNavTree
            ->method('hasGroup')
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );

        $oNavTree->checkGroups($oDom);
        $this->assertSame(str_replace(["\t", " ", "\n", "\r"], "", $sResXml), str_replace(["\t", " ", "\n", "\r"], "", $oDom->saveXML()));
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case when menu is marked as invisible,
     * also if it marked as visible and default behaviour, if attribute visible is not present.
     */
    public function testRemoveInvisibleMenuNodes()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
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

        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        $navTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getInitialDom"]);
        $navTree->method('getInitialDom')->willReturn($dom);
        $resultDom = $navTree->getDomXml();

        $expectedMenuClasses = ["MenuEntry-Visible", "MenuEntry-DefaultVisibility"];
        foreach ($resultDom->documentElement->childNodes as $menuItem) {
            if ($menuItem->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($menuItem->getAttribute('cl'), $expectedMenuClasses);
            }
        }
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case wen main menu is marked as invisible.
     */
    public function testRemoveInvisibleMainMenuNodes()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
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

        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        $navTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getInitialDom"]);
        $navTree->method('getInitialDom')->willReturn($dom);
        $resultDom = $navTree->getDomXml();

        $expectedMenuItems = ["MainMenu-Visible"];
        foreach ($resultDom->documentElement->childNodes as $menuNode) {
            if ($menuNode->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($menuNode->getAttribute('id'), $expectedMenuItems);
            }
        }
    }

    /**
     * OxNavigationTree::removeInvisibleMenuNodes() test case when tab is marked as not visible.
     */
    public function testRemoveInvisibleTabs()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
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

        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->loadXML($xml);

        $navTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getInitialDom"]);
        $navTree->method('getInitialDom')->willReturn($dom);
        $resultDom = $navTree->getDomXml();

        $expectedMenuClasses = ["MenuTab-Visible", null, "MenuTab-DefaultVisibility"];
        foreach ($resultDom->documentElement->childNodes as $menuItem) {
            if ($menuItem->nodeType == XML_ELEMENT_NODE) {
                $this->assertContains($navTree->getActiveTab($menuItem->getAttribute('cl'), 0), $expectedMenuClasses);
            }
        }
    }

    /**
     * OxNavigationTree::_checkRights() test case
     */
    public function testCheckRights()
    {
        $sXml = '<?xml version="1.0" encoding="UTF-8"?>
                   <MAINMENU>
                     <SUBMENU cl="testClass1" rights="testGroup1">
                       <TAB cl="testTabClass1" />
                     </SUBMENU>
                     <SUBMENU cl="testClass2" norights="testGroup2">
                       <TAB cl="testTabClass2" />
                     </SUBMENU>
                   </MAINMENU>';

        $sResXml = '<?xml version="1.0" encoding="UTF-8"?><MAINMENU>   </MAINMENU>';

        $oDom = new DOMDocument();
        $oDom->formatOutput = true;
        $oDom->loadXML($sXml);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["hasRights"]);
        $oNavTree
            ->method('hasRights')
            ->willReturnOnConsecutiveCalls(
                false,
                true
            );
        $oNavTree->checkRights($oDom);
        $this->assertSame(str_replace(["\t", " ", "\n", "\r"], "", $sResXml), str_replace(["\t", " ", "\n", "\r"], "", $oDom->saveXML()));
    }

    /**
     * test if the method find all denial link in menu xml
     *
     * @param object $oDom     XML Dom Object
     * @param int    $iNeedCnt amount of 'to remove links'
     */
    protected function checkDemoShopDenialsInMenuXml($oDom, $iNeedCnt)
    {
        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//*[@disableForDemoShop]");
        $iFoundCnt = 0;

        foreach ($oNodeList as $oNode) {
            if ($oNode->getAttribute('disableForDemoShop')) {
                $iFoundCnt++;
            }
        }

        $this->assertSame($iNeedCnt, $iFoundCnt);
    }

    /**
     * call the test method Unit_Admin_oxNavigationTreeTest::_checkDemoShopDenialsInMenuXml()
     */
    public function testcheckDemoShopDenialsDefaultMenuXml()
    {
        oxNew('oxNavigationTree');
        $oDom = $this->getDomXml();

        $this->checkDemoShopDenialsInMenuXml($oDom, 4);
    }

    /**
     * test if not all denial links removed when it isn't a demoshop
     */
    public function testcheckDemoShopDenialsDefaultNormal()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '1');
        }

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 4);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blDemoShop')->willReturn(false);
        Registry::set(Config::class, $oConfig);
        $oNavTree->checkDemoShopDenials($oDom);

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 4);
    }

    /**
     * test if not all denial links are removed when it is a demoshop
     */
    public function testcheckDemoShopDenialsDefaultDemo()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '1');
        }

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 4);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blDemoShop')->willReturn(true);
        Registry::set(Config::class, $oConfig);
        $oNavTree->checkDemoShopDenials($oDom);

        // removed
        $this->checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if no link is removed when it isn't a demoshop
     */
    public function testcheckDemoShopDenialsInverseNormal()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '0');
        }

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 0);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blDemoShop')->willReturn(false);
        Registry::set(Config::class, $oConfig);
        $oNavTree->checkDemoShopDenials($oDom);

        // removed
        $this->checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if no link is removed when it is a demoshop
     */
    public function testcheckDemoShopDenialsInverseDemo()
    {
        $oNavTree = oxNew('oxNavigationTree');
        $oDom = $this->getDomXml();

        $oXPath = new DomXPath($oDom);
        foreach ($oXPath->query("//*[@disableForDemoShop]") as $oNode) {
            $oNode->setAttribute('disableForDemoShop', '0');
        }

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 0);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->expects($this->once())->method('getConfigParam')->with('blDemoShop')->willReturn(true);
        Registry::set(Config::class, $oConfig);
        $oNavTree->checkDemoShopDenials($oDom);

        // not changed
        $this->checkDemoShopDenialsInMenuXml($oDom, 0);
    }

    /**
     * test if its possible to load the menu_xx.xml.
     * if all is ok, the method returns a dom object, else return nothing.
     *
     * @return object | null
     */
    protected function getDomXml()
    {
        $adminViewsDirectory = $this->getTestConfig()->getShopPath() .
            '/Application/views/' .
            $this->get(AdminThemeBridgeInterface::class)->getActiveTheme();

        $edition = strtolower((string) $this->getTestConfig()->getShopEdition());
        $menuFile = sprintf('/menu_%s.xml', $edition);

        $sFile = $adminViewsDirectory . $menuFile;
        if (!file_exists($sFile)) {
            if (file_exists($adminViewsDirectory . '/menu.xml')) {
                $sFile = $adminViewsDirectory . '/menu.xml';
            } else {
                $this->fail("menu.xml not found");
            }
        }

        $oDomFile = new DomDocument();
        $oDomFile->preserveWhiteSpace = false;
        if (@$oDomFile->load($sFile)) {
            $oDom = new DOMDocument();
            $oDom->appendChild(new DOMElement('OX'));
            $oXPath = new DOMXPath($oDom);
            oxNew('oxNavigationTree')->mergeNodes($oDom->documentElement, $oDomFile->documentElement, $oXPath, $oDom, '/OX');

            return $oDom;
        }

        $this->fail("menu.xml not found bad");
        return null;
    }

    /**
     * test if empty node can removed.
     */
    public function testCleanEmptyParents()
    {
        $oDom = $this->getDomXml();

        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);

        // remove children
        foreach ($oXPath->query("//SUBMENU[@id='mxcoresett']/TAB") as $oNode) {
            $oNode->parentNode->removeChild($oNode);
        }

        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);

        oxNew('oxNavigationTree')->cleanEmptyParents($oDom, '//SUBMENU[@id][@list]', 'TAB');
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertSame(0, $oNodeList->length);
    }

    /**
     * test if the right class id will read out from a node
     */
    public function testGetClassIdTakesFromOriginalXml(): void
    {
        (new Base())->setAdminMode(true);
        $this->getConfig()->setConfigParam("blUseRightsRoles", true);

        $oNavTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["isAdmin"]);
        $oNavTree->method('isAdmin')->willReturn(true);
        Registry::getConfig()->setConfigParam('sAdminDir', 'admin');

        $this->assertSame('mxcoresett', $oNavTree->getClassId('shop'));

        // now delete from dom
        $oDom = $oNavTree->getDomXml();
        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query("//SUBMENU[@id='mxcoresett']");
        $this->assertGreaterThan(0, $oNodeList->length);
        foreach ($oNodeList as $oNode) {
            $oNode->parentNode->removeChild($oNode);
        }

        // check if not changed
        $this->assertSame('mxcoresett', $oNavTree->getClassId('shop'));
    }

    /**
     * test if the admin URL will read out correct from config
     */
    public function testGetAdminUrl1()
    {
        $this->getConfig()->setConfigParam("sAdminSSLURL", "testAdminSslUrl");

        $oNavTree = oxNew('oxnavigationtree');
        $this->assertSame("testAdminSslUrl/index.php?", $oNavTree->getAdminUrl());
    }

    /**
     * test if the admin URL will read out correct from session
     */
    public function testGetAdminUrl()
    {
        $oUU = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ['processUrl']);
        $oUU->method('processUrl')
            ->with($this->anything(), false)
            ->willReturn('sess:url?');
        oxTestModules::addModuleObject('oxUtilsUrl', $oUU);

        $o = oxNew('oxNavigationTree');

        $this->assertSame('sess:url?', $o->getAdminUrl());
    }

    /**
     * OxNavigationTree::_processCachedFile() test case
     */
    public function testProcessCachedFile()
    {
        $sString = 'http://url/lala?stoken=ASDddddd2454&amp;amp;&amp;lala';

        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getAdminUrl']);
        $o->expects($this->never())->method('getAdminUrl');

        $this->assertSame($sString, $o->processCachedFile($sString));
    }

    /**
     * test if parameter add correct to URL
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
        $o = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getAdminUrl']);
        $o->expects($this->once())->method('getAdminUrl')->willReturn('http://url/lala?stoken=TOKEN111454&');
        $o->sessionizeLocalUrls($oCDom);

        $oXPath = new DomXPath($oDom);
        $oNodeList = $oXPath->query('//*[@url="index.php?loaa"]');
        $this->assertSame(1, $oNodeList->length);
        $oNodeList->item(0)->setAttribute('url', 'http://url/lala?stoken=TOKEN111454&loaa');
        $this->assertEquals($oDom, $oCDom);
    }

    /**
     * OxNavigationTree::_mergeNodes() test case
     */
    public function testMergeNodes()
    {
        $oNode1 = $this->getMock("stdClass", ["getAttribute"]);
        $oNode1->expects($this->once())->method('getAttribute')->willReturn('testAttribute1');
        $oNode1->nodeType = XML_ELEMENT_NODE;
        $oNode1->tagName = 'testTagName1';
        $oNode1->childNodes = new stdClass();
        $oNode1->childNodes->length = 1;

        $oNode2 = $this->getMock("stdClass", ["getAttribute"]);
        $oNode2->expects($this->once())->method('getAttribute')->willReturn('testAttribute2');
        $oNode2->nodeType = XML_ELEMENT_NODE;
        $oNode2->tagName = 'testTagName2';
        $oNode2->childNodes = new stdClass();
        $oNode2->childNodes->length = 1;

        $oDomElemTo = $this->getMock("stdClass", ["appendChild"]);
        $oDomElemTo->expects($this->once())->method('appendChild');

        $oCurNode1 = $this->getMock("stdClass", ["item"]);
        $oCurNode1->expects($this->never())->method('item');
        $oCurNode1->length = 0;

        $oCurNode2 = $this->getMock("stdClass", ["item"]);
        $oCurNode2->expects($this->once())->method('item')->willReturn("childNode");
        $oCurNode2->length = 1;

        $oXPathTo = $this->getMock("stdClass", ["query"]);
        $oXPathTo
            ->method('query')
            ->willReturnOnConsecutiveCalls(
                $oCurNode1,
                $oCurNode2
            );

        $oDomDocTo = $this->getMock("stdClass", ["importNode"]);
        $oDomDocTo->expects($this->atLeastOnce())->method('importNode');

        $oDomElemFrom = new stdClass();
        $oDomElemFrom->childNodes = [$oNode1, $oNode2];

        $oTree = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["copyAttributes"]);
        $oTree->expects($this->once())->method('copyAttributes');
        $oTree->mergeNodes($oDomElemTo, $oDomElemFrom, $oXPathTo, $oDomDocTo, $sQueryStart);
    }

    /**
     * OxNavigationTree::init() test case
     */
    public function testInit()
    {
        $oTree = oxNew('oxNavigationTree');
        if (method_exists($oTree, "init")) {
            $this->assertNull($oTree->init());
        }
    }
}
