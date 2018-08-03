<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\Theme;
use \stdClass;
use \oxRegistry;
use \oxTestModules;

class UtilsViewTest extends \OxidTestCase
{
    public function setUp()
    {
        parent::setUp();

        $theme = oxNew(Theme::class);
        $theme->load('azure');
        $theme->activate();
    }

    public function testGetTemplateDirsContainsAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $expectedTemplateDirs = $this->getTemplateDirsAzure();
        $utilsView = $this->getUtilsViewMockNotAdmin();

        $this->assertArraySubset($expectedTemplateDirs, $utilsView->getTemplateDirs());
    }

    public function testGetTemplateDirsOnlyAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $expectedTemplateDirs = $this->getTemplateDirsAzure();
        $utilsView = $this->getUtilsViewMockNotAdmin();

        $this->assertEquals($expectedTemplateDirs, $utilsView->getTemplateDirs());
    }

    public function testGetEditionTemplateDirsContainsAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $shopPath = $this->getShopPath();

        $dirs = [
            $shopPath . 'Application/views/azure/tpl/',
            $shopPath . 'out/azure/tpl/',
        ];

        $utilsView = $this->getUtilsViewMockNotAdmin();

        $this->assertArraySubset($dirs, $utilsView->getTemplateDirs());
    }

    public function testGetEditionTemplateDirsOnlyAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $shopPath = $this->getShopPath();

        $dirs = [
            $shopPath . 'Application/views/azure/tpl/',
            $shopPath . 'out/azure/tpl/',
        ];

        $utilsView = $this->getUtilsViewMockNotAdmin();

        $this->assertEquals($dirs, $utilsView->getTemplateDirs());
    }

    public function testGetEditionTemplateDirsForAdminContainsAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $shopPath = $this->getShopPath();

        $dirs = [
            $shopPath . 'Application/views/admin/tpl/',
        ];

        $utilsView = $this->getUtilsViewMockBeAdmin();

        $this->assertArraySubset($dirs, $utilsView->getTemplateDirs());
    }

    public function testGetEditionTemplateDirsForAdminOnlyAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $shopPath = $this->getShopPath();

        $dirs = [
            $shopPath . 'Application/views/admin/tpl/',
        ];

        $utilsView = $this->getUtilsViewMockBeAdmin();

        $this->assertEquals($dirs, $utilsView->getTemplateDirs());
    }

    public function testSetTemplateDirContainsAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $myConfig = $this->getConfig();
        $aDirs[] = "testDir1";
        $aDirs[] = "testDir2";
        $aDirs[] = $myConfig->getTemplateDir(false);
        $sDir = $myConfig->getOutDir(true) . $myConfig->getConfigParam('sTheme') . "/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        $sDir = $myConfig->getOutDir(true) . "azure/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        $utilsView = $this->getUtilsViewMockNotAdmin();
        $utilsView->setTemplateDir("testDir1");
        $utilsView->setTemplateDir("testDir2");
        $utilsView->setTemplateDir("testDir1");

        $this->assertArraySubset($aDirs, $utilsView->getTemplateDirs());
    }

    public function testSetTemplateDirOnlyAzure()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $myConfig = $this->getConfig();
        $aDirs[] = "testDir1";
        $aDirs[] = "testDir2";
        $aDirs[] = $myConfig->getTemplateDir(false);
        $sDir = $myConfig->getOutDir(true) . $myConfig->getConfigParam('sTheme') . "/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        $sDir = $myConfig->getOutDir(true) . "azure/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        $utilsView = $this->getUtilsViewMockNotAdmin();
        $utilsView->setTemplateDir("testDir1");
        $utilsView->setTemplateDir("testDir2");
        $utilsView->setTemplateDir("testDir1");

        $this->assertEquals($aDirs, $utilsView->getTemplateDirs());
    }

    /**
     * Testing template processign code + skipped debug output code
     */
    public function testGetTemplateOutput()
    {
        $this->getConfig()->setConfigParam('iDebug', 0);
        $sTpl = __DIR__ ."/../testData//misc/testTempOut.tpl";

        $oView = oxNew('oxview');
        $oView->addTplParam('articletitle', 'xxx');

        $oUtilsView = oxNew('oxutilsview');

        $this->assertEquals('xxx', $oUtilsView->getTemplateOutput($sTpl, $oView));
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
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

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

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "");
        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['myDest'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $aErrorController = oxRegistry::getSession()->getVariable('ErrorController');
        $this->assertEquals("oxwminibasket", $aErrorController['myDest']);
    }

    public function testAddErrorToDisplayDefaultDestination()
    {
        $this->setRequestParameter('actcontrol', 'start');
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "");
        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['default'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $aErrorController = oxRegistry::getSession()->getVariable('ErrorController');
        $this->assertEquals("start", $aErrorController['default']);
    }

    public function testAddErrorToDisplayUsingExeptionObject()
    {
        $oTest = oxNew('oxException');
        $oTest->setMessage("testMessage");

        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay($oTest, false, false, "");

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['default'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
    }

    public function testAddErrorToDisplayIfNotSet()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay(null, false, false, "");

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        //$oEx = unserialize($aErrors['default'][0]);
        //$this->assertEquals("", $oEx->getOxMessage());
        $this->assertFalse(isset($aErrors['default'][0]));
        $this->assertNull(oxRegistry::getSession()->getVariable('ErrorController'));
    }

    public function testAddErrorToDisplay_startsSessionIfNotStarted()
    {
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getId', 'isHeaderSent', 'setForceNewSession', 'start']);
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('isHeaderSent')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('setForceNewSession');
        $oSession->expects($this->once())->method('start');

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['getSession']);
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay(null, false, false, "");
    }

    /**
     * Testing smarty processor
     */
    public function testParseThroughSmarty()
    {
        $aData['shop'] = new stdClass();
        $aData['shop']->urlSeparator = '?';

        $oActView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getViewData']);
        $oActView->expects($this->once())->method('getViewData')->will($this->returnValue($aData));

        $oUtilsView = oxNew('oxutilsview');
        $this->assertEquals('?', $oUtilsView->parseThroughSmarty('[{$shop->urlSeparator}]', time(), $oActView));

        $oActView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getViewData']);
        $oActView->expects($this->once())->method('getViewData')->will($this->returnValue($aData));

        $oUtilsView = oxNew('oxutilsview');
        $this->assertEquals(['!' => '?'], $oUtilsView->parseThroughSmarty(['!' => ['%', '[{$shop->urlSeparator}]']], time(), $oActView));
    }

    public function testParseThroughSmartyInDiffLang()
    {
        $templateEngine = $this->getContainer()->get(TemplateEngineBridgeInterface::class)->getEngine();
        $templateEngine->compile_check = false;
        $lang = oxRegistry::getLang()->getTplLanguage();

        oxRegistry::getLang()->setTplLanguage(0);
        $text1 = \OxidEsales\Eshop\Core\Registry::getUtilsView()->parseThroughSmarty('aaa', 'aaa');
        oxRegistry::getLang()->setTplLanguage(1);
        $text2 = \OxidEsales\Eshop\Core\Registry::getUtilsView()->parseThroughSmarty('bbb', 'aaa');

        $templateEngine->compile_check = true;
        oxRegistry::getLang()->setTplLanguage($lang);

        $this->assertEquals('aaa', $text1);
        $this->assertEquals('bbb', $text2);
    }

    /**
     * base test
     */
    public function testGetActiveModuleInfo()
    {
        oxTestModules::addFunction('oxModulelist', 'getActiveModuleInfo', '{ return true; }');
        $oUV = $this->getProxyClass('oxUtilsView');

        $this->assertTrue($oUV->UNITgetActiveModuleInfo());
    }

    /**
     * tests oxutilsView::getSmartyDir()
     */
    public function testGetSmartyDir()
    {
        $config = oxNew('oxConfig');

        $oUV = oxNew('oxUtilsView');
        $oUV->setConfig($config);

        $compileDirectory = $this->getCompileDirectory();
        $config->setConfigParam('sCompileDir', $compileDirectory);

        $sExp = $compileDirectory . "/smarty/";

        $this->assertSame($sExp, $oUV->getSmartyDir());

    }

    /**
     * @return array
     */
    private function getTemplateDirsAzure()
    {
        $config = $this->getConfig();
        $dirs = [];
        $dirs[] = $config->getTemplateDir(false);
        $dir = $config->getOutDir(true) . $config->getConfigParam('sTheme') . "/tpl/";
        if (!in_array($dir, $dirs)) {
            $dirs[] = $dir;
        }
        $dir = $config->getOutDir(true) . "azure/tpl/";
        if (!in_array($dir, $dirs)) {
            $dirs[] = $dir;
        }
        return $dirs;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getUtilsViewMockNotAdmin()
    {
        $utilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["isAdmin"]);
        $utilsView->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        return $utilsView;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getUtilsViewMockBeAdmin()
    {
        $utilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["isAdmin"]);
        $utilsView->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        return $utilsView;
    }

    /**
     * @return string
     */
    private function getShopPath()
    {
        $config = $this->getConfig();
        $shopPath = rtrim($config->getConfigParam('sShopDir'), '/') . '/';
        return $shopPath;
    }

    /**
     * @return string
     */
    private function getCompileDirectory()
    {
        $oVfsStreamWrapper = $this->getVfsStreamWrapper();
        $oVfsStreamWrapper->createStructure(['tmp_directory' => []]);
        $compileDirectory = $oVfsStreamWrapper->getRootPath() . 'tmp_directory';
        return $compileDirectory;
    }

    /**
     * @internal
     *
     * @return \Psr\Container\ContainerInterface
     */
    private function getContainer()
    {
        return \OxidEsales\EshopCommunity\Internal\Application\ContainerFactory::getInstance()->getContainer();
    }
}
