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
     * Testing smarty getter + its caching
     */
    public function testGetSmartyCacheCheck()
    {
        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['_fillCommonSmartyProperties', '_smartyCompileCheck']);
        $oUtilsView->expects($this->once())->method('_fillCommonSmartyProperties');
        $oUtilsView->expects($this->once())->method('_smartyCompileCheck');

        // on second call defined methods should not be executed again
        $oUtilsView->getSmarty(true);
        $oUtilsView->getSmarty();
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
        $oUtilsView->getSmarty(true);

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

    public function testFillCommonSmartyPropertiesAndSmartyCompileCheckDemoShopContains()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $config = oxNew('oxConfig');

        $config->setConfigParam('iDebug', 1);
        $config->setConfigParam('blDemoShop', 1);

        $templateDirs = [];

        $sTplDir = $config->getTemplateDir($config->isAdmin());
        if ($sTplDir) {
            $templateDirs[] = $sTplDir;
        }

        $sTplDir = $config->getOutDir() . $config->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $templateDirs)) {
            $templateDirs[] = $sTplDir;
        }

        $compileDirectory = $this->getCompileDirectory();
        $config->setConfigParam('sCompileDir', $compileDirectory);

        $smartyCheckArray = $this->getSmartyCheckArray($compileDirectory, $config);

        $smarty = $this->getSmartyMock();

        $oUtilsView = oxNew('oxUtilsView');
        $oUtilsView->setConfig($config);
        $oUtilsView->UNITfillCommonSmartyProperties($smarty);
        $oUtilsView->UNITsmartyCompileCheck($smarty);

        foreach ($smartyCheckArray as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName));
            $this->assertEquals($varValue, $smarty->$varName, $varName);
        }

        $this->assertArraySubset($templateDirs, $smarty->template_dir);
    }

    /**
     * Testing smarty config data setter
     */
    // demo mode
    public function testFillCommonSmartyPropertiesAndSmartyCompileCheckDemoShopExactMatch()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $config = oxNew('oxConfig');

        $config->setConfigParam('iDebug', 1);
        $config->setConfigParam('blDemoShop', 1);

        $sTplDir = $config->getTemplateDir($config->isAdmin());

        $aTemplatesDir = [];

        if ($sTplDir) {
            $aTemplatesDir[] = $sTplDir;
        }

        $sTplDir = $config->getOutDir() . $config->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $aTemplatesDir)) {
            $aTemplatesDir[] = $sTplDir;
        }

        $compileDirectory = $this->getCompileDirectory();
        $config->setConfigParam('sCompileDir', $compileDirectory);

        $smartyCheckArray = $this->getSmartyCheckArray($compileDirectory, $config);
        $smartyCheckArray['template_dir'] = $aTemplatesDir;

        $smarty = $this->getSmartyMock();

        $oUtilsView = oxNew('oxUtilsView');
        $oUtilsView->setConfig($config);
        $oUtilsView->UNITfillCommonSmartyProperties($smarty);
        $oUtilsView->UNITsmartyCompileCheck($smarty);

        foreach ($smartyCheckArray as $sVarName => $sVarValue) {
            $this->assertTrue(isset($smarty->$sVarName));
            $this->assertEquals($sVarValue, $smarty->$sVarName, $sVarName);
        }
    }

    public function testFillCommonSmartyPropertiesAndSmartyCompileCheckContains()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $config = oxNew('oxConfig');

        $config->setConfigParam('iDebug', 1);
        $config->setConfigParam('blDemoShop', 0);

        $aTemplatesDir = [];

        $sTplDir = $config->getTemplateDir($config->isAdmin());
        if ($sTplDir) {
            $aTemplatesDir[] = $sTplDir;
        }

        $sTplDir = $config->getOutDir() . $config->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $aTemplatesDir)) {
            $aTemplatesDir[] = $sTplDir;
        }

        $compileDirectory = $this->getCompileDirectory();
        $config->setConfigParam('sCompileDir', $compileDirectory);

        $aCheck = $this->getSmartyCheckArrayForFillCommonSmartyPropertiesAndSmartyCompileCheck($config, $compileDirectory);

        $oSmarty = $this->getMock('\Smarty', ['register_resource']);
        $oSmarty->expects($this->once())->method('register_resource');

        $oUtilsView = oxNew('oxUtilsView');
        $oUtilsView->setConfig($config);
        $oUtilsView->UNITfillCommonSmartyProperties($oSmarty);
        $oUtilsView->UNITsmartyCompileCheck($oSmarty);

        foreach ($aCheck as $sVarName => $sVarValue) {
            $this->assertTrue(isset($oSmarty->$sVarName));
            $this->assertEquals($sVarValue, $oSmarty->$sVarName, $sVarName);
        }

        $this->assertArraySubset($aTemplatesDir, $oSmarty->template_dir);
    }

    // non demo mode
    public function testFillCommonSmartyPropertiesAndSmartyCompileCheckExactMatch()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $config = oxNew('oxConfig');

        $config->setConfigParam('iDebug', 1);
        $config->setConfigParam('blDemoShop', 0);

        $aTemplatesDir = [];

        $sTplDir = $config->getTemplateDir($config->isAdmin());
        if ($sTplDir) {
            $aTemplatesDir[] = $sTplDir;
        }

        $sTplDir = $config->getOutDir() . $config->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $aTemplatesDir)) {
            $aTemplatesDir[] = $sTplDir;
        }

        $compileDirectory = $this->getCompileDirectory();
        $config->setConfigParam('sCompileDir', $compileDirectory);

        $aCheck = $this->getSmartyCheckArrayForFillCommonSmartyPropertiesAndSmartyCompileCheck($config, $compileDirectory);
        $aCheck['template_dir'] = $aTemplatesDir;

        $oSmarty = $this->getMock('\Smarty', ['register_resource']);
        $oSmarty->expects($this->once())->method('register_resource');

        $oUtilsView = oxNew('oxUtilsView');
        $oUtilsView->setConfig($config);
        $oUtilsView->UNITfillCommonSmartyProperties($oSmarty);
        $oUtilsView->UNITsmartyCompileCheck($oSmarty);

        foreach ($aCheck as $sVarName => $sVarValue) {
            $this->assertTrue(isset($oSmarty->$sVarName));
            $this->assertEquals($sVarValue, $oSmarty->$sVarName, $sVarName);
        }
    }

    /**
     * Initialize the fixture.
     *
     * @return null
     */

    public function testFillCommonSmartyPropertiesANDSmartyCompileCheckDemoShop()
    {
        $config = oxNew(\OxidEsales\Eshop\Core\Config::class);

        $config->setConfigParam('iDebug', 1);
        $config->setConfigParam('blDemoShop', 1);

        $templatesDirectories = [];

        $tplDir = $config->getTemplateDir($config->isAdmin());
        if ($tplDir) {
            $templatesDirectories[] = $tplDir;
        }

        $tplDir = $config->getOutDir() . $config->getConfigParam('sTheme') . "/tpl/";
        if ($tplDir && !in_array($tplDir, $templatesDirectories)) {
            $templatesDirectories[] = $tplDir;
        }

        $config->setConfigParam('sCompileDir', $this->getCompileDirectory());

        $smarty = $this->getMock('\Smarty', ['register_resource', 'register_prefilter']);
        $smarty->expects($this->once())->method('register_resource')
            ->with(
                $this->equalTo('ox'),
                $this->equalTo(
                    [
                        'ox_get_template',
                        'ox_get_timestamp',
                        'ox_get_secure',
                        'ox_get_trusted',
                    ]
                )
            );
        $smarty->expects($this->once())->method('register_prefilter')
            ->with($this->equalTo('smarty_prefilter_oxblock'));

        $utilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $utilsView->setConfig($config);
        $utilsView->UNITfillCommonSmartyProperties($smarty);
        $utilsView->UNITsmartyCompileCheck($smarty);

        $smarty = new \smarty();
        $mockedConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['isProductiveMode']);
        $mockedConfig->expects($this->once())->method('isProductiveMode')->will($this->returnValue(true));
        $utilsView = oxNew(\OxidEsales\Eshop\Core\UtilsView::class);
        $utilsView->setConfig($mockedConfig);
        $utilsView->UNITsmartyCompileCheck($smarty);
        $this->assertFalse($smarty->compile_check);
    }

    public function testParseThroughSmartyInDiffLang()
    {
        $smarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
        $smarty->compile_check = false;
        $lang = oxRegistry::getLang()->getTplLanguage();

        oxRegistry::getLang()->setTplLanguage(0);
        $text1 = \OxidEsales\Eshop\Core\Registry::getUtilsView()->parseThroughSmarty('aaa', 'aaa');
        oxRegistry::getLang()->setTplLanguage(1);
        $text2 = \OxidEsales\Eshop\Core\Registry::getUtilsView()->parseThroughSmarty('bbb', 'aaa');

        $smarty->compile_check = true;
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
     * @param $compileDirectory
     * @param $config
     * @return array
     */
    private function getSmartyCheckArray($compileDirectory, $config)
    {
        $aCheck = [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $compileDirectory . "/smarty/",
            'cache_dir' => $compileDirectory . "/smarty/",
            'compile_id' => md5($config->getTemplateDir(false) . '__' . $config->getShopId()),
            'debugging' => true,
            'compile_check' => true,
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ]
        ];
        return $aCheck;
    }

    /**
     * @param $config
     * @param $compileDirectory
     * @return array
     */
    private function getSmartyCheckArrayForFillCommonSmartyPropertiesAndSmartyCompileCheck($config, $compileDirectory)
    {
        $aCheck = [
            'security' => false,
            'php_handling' => (int)$config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $compileDirectory . "/smarty/",
            'cache_dir' => $compileDirectory . "/smarty/",
            'compile_id' => md5($config->getTemplateDir(false) . '__' . $config->getShopId()),
            'debugging' => true,
            'compile_check' => true,
            'plugins_dir' => [$this->getConfigParam('sCoreDir') . 'Smarty/Plugin', 'plugins'],
        ];
        return $aCheck;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getSmartyMock()
    {
        $oSmarty = $this->getMock('\Smarty', ['register_resource', 'register_prefilter']);
        $oSmarty->expects($this->once())->method('register_resource')
            ->with(
                $this->equalTo('ox'),
                $this->equalTo(
                    [
                        'ox_get_template',
                        'ox_get_timestamp',
                        'ox_get_secure',
                        'ox_get_trusted',
                    ]
                )
            );
        $oSmarty->expects($this->once())->method('register_prefilter')
            ->with($this->equalTo('smarty_prefilter_oxblock'));
        return $oSmarty;
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
}
