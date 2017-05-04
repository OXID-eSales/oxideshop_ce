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

class Unit_Core_oxUtilsViewTest extends OxidTestCase
{

    /**
     * setup test data
     */
    public function setUp()
    {
        parent::setUp();
        if (strpos($this->getName(), 'testGetTemplateBlocks') === 0) {
            oxDb::getDb()->Execute(
                "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                       'test_1',
                                                       '1',
                                                       '15',
                                                       'filename.tpl',
                                                       'blockname1',
                                                       1,
                                                       'contentfile1',
                                                       'module1'
                                                    )"
            );
            oxDb::getDb()->Execute(
                "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                       'test_2',
                                                       '1',
                                                       '15',
                                                       'filename.tpl',
                                                       'blockname2',
                                                       2,
                                                       'contentfile2',
                                                       'module2'
                                                    )"
            );
            oxDb::getDb()->Execute(
                "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                       'test_3',
                                                       '1',
                                                       '15',
                                                       'filename.tpl',
                                                       'blockname2',
                                                       0,
                                                       'contentfile3',
                                                       'module2'
                                                    )"
            );
            // one non active - to be sure it is not loaded
            oxDb::getDb()->Execute(
                "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                       'test_4',
                                                       '0',
                                                       '15',
                                                       'filename.tpl',
                                                       'blockname3',
                                                       3,
                                                       'contentfile3',
                                                       'module2'
                                                    )"
            );
            oxDb::getDb()->Execute(
                "insert into oxtplblocks (OXID,OXACTIVE,OXSHOPID,OXTEMPLATE,OXBLOCKNAME,OXPOS,OXFILE,OXMODULE) values (
                                                       'test_5',
                                                       '1',
                                                       '15',
                                                       'inc/filename.tpl',
                                                       'blockname99',
                                                       4,
                                                       'contentfile99',
                                                       'module99'
                                                    )"
            );
        }
    }

    /**
     * remove test data
     */
    public function  tearDown()
    {
        if (strpos($this->getName(), 'testGetTemplateBlocks') === 0) {
            oxDb::getDb()->Execute("delete from oxtplblocks where oxid like 'test_%'");
        }
        parent::tearDown();
    }

    /**
     * oxUtilsView::getTemplateDirs() test case
     *
     * @return null
     */
    public function testGetTemplateDirs()
    {
        $myConfig = oxRegistry::getConfig();
        $aDirs = array();
        $aDirs[] = $myConfig->getTemplateDir(false);
        $sDir = $myConfig->getOutDir(true) . $myConfig->getConfigParam('sTheme') . "/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        $sDir = $myConfig->getOutDir(true) . "azure/tpl/";
        if (!in_array($sDir, $aDirs)) {
            $aDirs[] = $sDir;
        }

        //
        $oUtilsView = $this->getMock("oxUtilsView", array("isAdmin"));
        $oUtilsView->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $this->assertEquals($aDirs, $oUtilsView->getTemplateDirs());
    }

    /**
     * oxUtilsView::setTemplateDir() test case
     *
     * @return null
     */
    public function testSetTemplateDir()
    {
        $myConfig = oxRegistry::getConfig();
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

        //
        $oUtilsView = $this->getMock("oxUtilsView", array("isAdmin"));
        $oUtilsView->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oUtilsView->setTemplateDir("testDir1");
        $oUtilsView->setTemplateDir("testDir2");
        $oUtilsView->setTemplateDir("testDir1");

        $this->assertEquals($aDirs, $oUtilsView->getTemplateDirs());
    }

    /**
     * Testing smarty getter + its caching
     */
    public function testGetSmartyCacheCheck()
    {
        $oUtilsView = $this->getMock('oxutilsview', array('_fillCommonSmartyProperties', '_smartyCompileCheck'));
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
        modConfig::getInstance()->setConfigParam('iDebug', 0);
        $sPath = getTestsBasePath();
        $sTpl = $sPath . "/misc/testTempOut.tpl";

        $oView = new oxview();
        $oView->addTplParam('articletitle', 'xxx');

        $oUtilsView = new oxutilsview();
        $oUtilsView->getSmarty(true);

        $this->assertEquals('xxx', $oUtilsView->getTemplateOutput($sTpl, $oView));
    }

    public function testPassAllErrorsToView()
    {
        $aView = array();
        $aErrors[1][2] = serialize("foo");
        oxRegistry::get("oxUtilsView")->passAllErrorsToView($aView, $aErrors);
        $this->assertEquals($aView['Errors'][1][2], "foo");
    }

    public function testAddErrorToDisplayCustomDestinationFromParam()
    {
        $oSession = $this->getMock('oxSession', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay("testMessage", false, true, "myDest");


        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['myDest'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
        $this->assertNull(oxRegistry::getSession()->getVariable('ErrorController'));
    }

    public function testAddErrorToDisplayCustomDestinationFromPost()
    {
        $myConfig = oxRegistry::getConfig();
        $this->setRequestParam('CustomError', 'myDest');
        $this->setRequestParam('actcontrol', 'oxwminibasket');

        $oSession = $this->getMock('oxSession', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
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
        $this->setRequestParam('actcontrol', 'start');
        $oSession = $this->getMock('oxSession', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
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
        $aTest = array();
        $oTest = oxNew('oxException');
        $oTest->setMessage("testMessage");

        $oSession = $this->getMock('oxSession', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
        $oxUtilsView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));

        $oxUtilsView->addErrorToDisplay($oTest, false, false, "");

        $aErrors = oxRegistry::getSession()->getVariable('Errors');
        $oEx = unserialize($aErrors['default'][0]);
        $this->assertEquals("testMessage", $oEx->getOxMessage());
    }

    public function testAddErrorToDisplayIfNotSet()
    {
        $oSession = $this->getMock('oxSession', array('getId'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(true));

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
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
        $oSession = $this->getMock('oxSession', array('getId', 'isHeaderSent', 'setForceNewSession', 'start'));
        $oSession->expects($this->once())->method('getId')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('isHeaderSent')->will($this->returnValue(false));
        $oSession->expects($this->once())->method('setForceNewSession');
        $oSession->expects($this->once())->method('start');

        $oxUtilsView = $this->getMock('oxUtilsView', array('getSession'));
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

        $oActView = $this->getMock('oxview', array('getViewData'));
        $oActView->expects($this->once())->method('getViewData')->will($this->returnValue($aData));

        $oUtilsView = new oxutilsview();
        $this->assertEquals('?', $oUtilsView->parseThroughSmarty('[{$shop->urlSeparator}]', time(), $oActView));

        $oActView = $this->getMock('oxview', array('getViewData'));
        $oActView->expects($this->once())->method('getViewData')->will($this->returnValue($aData));

        $oUtilsView = new oxutilsview();
        $this->assertEquals(array('!' => '?'), $oUtilsView->parseThroughSmarty(array('!' => array('%', '[{$shop->urlSeparator}]')), time(), $oActView));
    }

    /**
     * Testing smarty config data setter
     */
    // demo mode
    public function testFillCommonSmartyPropertiesANDSmartyCompileCheckDemoShop()
    {
        modConfig::getInstance()->setConfigParam('iDebug', 1);
        modConfig::getInstance()->setConfigParam('blDemoShop', 1);

        $myConfig = oxRegistry::getConfig();

        $sTplDir = $myConfig->getTemplateDir($myConfig->isAdmin());

        $aTemplatesDir = array();
        if ($sTplDir) {
            $aTemplatesDir[] = $sTplDir;
        }

        $sTplDir = $myConfig->getOutDir() . $myConfig->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $aTemplatesDir)) {
            $aTemplatesDir[] = $sTplDir;
        }

        $aCheck = array('php_handling'      => 2,
                        'security'          => true,
                        'php_handling'      => SMARTY_PHP_REMOVE,
                        'left_delimiter'    => '[{',
                        'right_delimiter'   => '}]',
                        'caching'           => false,
                        'compile_dir'       => $myConfig->getConfigParam('sCompileDir') . "/smarty/",
                        'cache_dir'         => $myConfig->getConfigParam('sCompileDir') . "/smarty/",
                        'template_dir'      => $aTemplatesDir,
                        'compile_id'        => md5($myConfig->getTemplateDir(false) . '__' . $myConfig->getShopId()),
                        'debugging'         => true,
                        'compile_check'     => true,
                        'security_settings' => array(
                            'PHP_HANDLING'        => false,
                            'IF_FUNCS'            =>
                                array(
                                    0  => 'array',
                                    1  => 'list',
                                    2  => 'isset',
                                    3  => 'empty',
                                    4  => 'count',
                                    5  => 'sizeof',
                                    6  => 'in_array',
                                    7  => 'is_array',
                                    8  => 'true',
                                    9  => 'false',
                                    10 => 'null',
                                    11 => 'XML_ELEMENT_NODE',
                                    12 => 'is_int',
                                ),
                            'INCLUDE_ANY'         => false,
                            'PHP_TAGS'            => false,
                            'MODIFIER_FUNCS'      =>
                                array(
                                    0 => 'count',
                                    1 => 'round',
                                    2 => 'floor',
                                    3 => 'trim',
                                    4 => 'implode',
                                    5 => 'is_array',
                                    6 => 'getimagesize',
                                ),
                            'ALLOW_CONSTANTS'     => true,
                            'ALLOW_SUPER_GLOBALS' => true,
                        )
        );


        $oSmarty = $this->getMock('smarty', array('register_resource', 'register_prefilter'));
        $oSmarty->expects($this->once())->method('register_resource')
            ->with(
                $this->equalTo('ox'),
                $this->equalTo(
                    array(
                         'ox_get_template',
                         'ox_get_timestamp',
                         'ox_get_secure',
                         'ox_get_trusted',
                    )
                )
            );
        $oSmarty->expects($this->once())->method('register_prefilter')
            ->with($this->equalTo('smarty_prefilter_oxblock'));

        $oUtilsView = new oxutilsview();
        $oUtilsView->UNITfillCommonSmartyProperties($oSmarty);
        $oUtilsView->UNITsmartyCompileCheck($oSmarty);

        foreach ($aCheck as $sVarName => $sVarValue) {
            $this->assertTrue(isset($oSmarty->$sVarName));
            $this->assertEquals($oSmarty->$sVarName, $sVarValue, $sVarName);
        }
    }

    // non demo mode
    public function testFillCommonSmartyPropertiesANDSmartyCompileCheck()
    {
        modConfig::getInstance()->setConfigParam('iDebug', 1);
        modConfig::getInstance()->setConfigParam('blDemoShop', 0);

        $myConfig = oxRegistry::getConfig();

        $sTplDir = $myConfig->getTemplateDir($myConfig->isAdmin());

        $aTemplatesDir = array();
        if ($sTplDir) {
            $aTemplatesDir[] = $sTplDir;
        }

        $sTplDir = $myConfig->getOutDir() . $myConfig->getConfigParam('sTheme') . "/tpl/";
        if ($sTplDir && !in_array($sTplDir, $aTemplatesDir)) {
            $aTemplatesDir[] = $sTplDir;
        }

        $aCheck = array('php_handling'    => 2,
                        'security'        => false,
                        'php_handling'    => (int) $myConfig->getConfigParam('iSmartyPhpHandling'),
                        'left_delimiter'  => '[{',
                        'right_delimiter' => '}]',
                        'caching'         => false,
                        'compile_dir'     => $myConfig->getConfigParam('sCompileDir') . "/smarty/",
                        'cache_dir'       => $myConfig->getConfigParam('sCompileDir') . "/smarty/",
                        'template_dir'    => $aTemplatesDir,
                        'compile_id'      => md5($myConfig->getTemplateDir(false) . '__' . $myConfig->getShopId()),
                        'debugging'       => true,
                        'compile_check'   => true);


        $oSmarty = $this->getMock('smarty', array('register_resource'));
        $oSmarty->expects($this->once())->method('register_resource');

        $oUtilsView = new oxutilsview();
        $oUtilsView->UNITfillCommonSmartyProperties($oSmarty);
        $oUtilsView->UNITsmartyCompileCheck($oSmarty);

        foreach ($aCheck as $sVarName => $sVarValue) {
            $this->assertTrue(isset($oSmarty->$sVarName));
            $this->assertEquals($oSmarty->$sVarName, $sVarValue, $sVarName);
        }
    }

    public function testParseThroughSmartyInDiffLang()
    {
        $smarty = oxRegistry::get("oxUtilsView")->getSmarty();
        $smarty->compile_check = false;
        $lang = oxRegistry::getLang()->getTplLanguage();

        oxRegistry::getLang()->setTplLanguage(0);
        $text1 = oxRegistry::get("oxUtilsView")->parseThroughSmarty('aaa', 'aaa');
        oxRegistry::getLang()->setTplLanguage(1);
        $text2 = oxRegistry::get("oxUtilsView")->parseThroughSmarty('bbb', 'aaa');

        $smarty->compile_check = true;
        oxRegistry::getLang()->setTplLanguage($lang);

        $this->assertEquals('aaa', $text1);
        $this->assertEquals('bbb', $text2);
    }

    /**
     * base test
     */
    public function testGetTemplateBlock()
    {
        $sMdir = realpath((dirname(__FILE__) . '/../moduleTestBlock'));

        $oCfg = $this->getMock('oxConfig', array('getConfigParam'));
        $oCfg->expects($this->any())->method('getConfigParam')->with($this->equalTo('sShopDir'))->will($this->returnValue($sMdir));
        $aInfo = array('test1' => 'test1', '__sModule__' => '__sModule__');

        $oUV = $this->getMock(
            oxTestModules::publicize('oxUtilsView', '_getTemplateBlock'),
            array('getConfig', '_getActiveModuleInfo')
        );
        $oUV->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));
        $oUV->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));

        $this->assertEquals('*this is module test block*', $oUV->p_getTemplateBlock('test1', 'test2'));

        try {
            $oUV->p_getTemplateBlock('__sModule__', '__sFile__');
            $this->fail("no exception");
        } catch (oxException $oE) {
            $this->assertEquals("Template block file (__basepath__/modules/__sModule__/out/blocks/__sFile__.tpl) not found for '__sModule__' module.", str_replace($sMdir, '__basepath__', $oE->getMessage()));
        }
    }

    /**
     * base test
     */
    public function testGetTemplateBlocks()
    {
        $oCfg = $this->getMock('oxConfig', array('getShopId'));
        $oCfg->expects($this->at(0))->method('getShopId')->will($this->returnValue('15'));
        $oCfg->expects($this->at(1))->method('getShopId')->will($this->returnValue('15'));
        $oCfg->expects($this->at(2))->method('getShopId')->will($this->returnValue('25'));
        $aInfo = array('module1' => 'module1', 'module2' => 'module2');

        $o = $this->getMock('oxUtilsView', array('getConfig', '_getActiveModuleInfo', '_getTemplateBlock'));
        $o->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));
        $o->expects($this->at(3))->method('_getTemplateBlock')
            ->with($this->equalTo('module2'), $this->equalTo('contentfile3'))
            ->will($this->returnValue('content3'));
        $o->expects($this->at(4))->method('_getTemplateBlock')
            ->with($this->equalTo('module1'), $this->equalTo('contentfile1'))
            ->will($this->returnValue('content1'));
        $o->expects($this->at(5))->method('_getTemplateBlock')
            ->with($this->equalTo('module2'), $this->equalTo('contentfile2'))
            ->will($this->returnValue('content2'));

        $this->assertEquals(
            array(
                 'blockname1' => array(
                     'content1',
                 ),
                 'blockname2' => array(
                     'content3',
                     'content2',
                 ),
            ),
            $o->getTemplateBlocks('filename.tpl')
        );

        $this->assertEquals(
            array(),
            $o->getTemplateBlocks('filename.tpl')
        );
    }

    /**
     * exception log test
     */
    public function testGetTemplateBlocksLogsExceptions()
    {
        $oCfg = $this->getMock('oxConfig', array('getShopId'));
        $oCfg->expects($this->at(0))->method('getShopId')->will($this->returnValue('15'));
        $oCfg->expects($this->at(1))->method('getShopId')->will($this->returnValue('15'));
        $oCfg->expects($this->at(2))->method('getShopId')->will($this->returnValue('25'));
        $aInfo = array('module1' => 'module1', 'module2' => 'module2');

        $oE = $this->getMock('oxException', array('debugOut'));
        $oE->expects($this->once())->method('debugOut');

        $o = $this->getMock('oxUtilsView', array('getConfig', '_getActiveModuleInfo', '_getTemplateBlock'));
        $o->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));
        $o->expects($this->at(3))->method('_getTemplateBlock')
            ->with($this->equalTo('module2'), $this->equalTo('contentfile3'))
            ->will($this->throwException($oE));
        $o->expects($this->at(4))->method('_getTemplateBlock')
            ->with($this->equalTo('module1'), $this->equalTo('contentfile1'))
            ->will($this->returnValue('content1'));
        $o->expects($this->at(5))->method('_getTemplateBlock')
            ->with($this->equalTo('module2'), $this->equalTo('contentfile2'))
            ->will($this->returnValue('content2'));

        $this->assertEquals(
            array(
                 'blockname1' => array(
                     'content1',
                 ),
                 'blockname2' => array(
                     'content2',
                 ),
            ),
            $o->getTemplateBlocks('filename.tpl')
        );

        $this->assertEquals(
            array(),
            $o->getTemplateBlocks('filename.tpl')
        );
    }


    /**
     * file filtering test
     */
    public function testGetTemplateBlocksFileFilter()
    {
        $oCfg = $this->getMock('oxConfig', array('getShopId'));
        $oCfg->expects($this->at(0))->method('getShopId')->will($this->returnValue('15'));
        $oCfg->expects($this->at(1))->method('getShopId')->will($this->returnValue('15'));
        $aInfo = array('module99' => 'module99');

        $o = $this->getMock('oxUtilsView', array('getConfig', '_getTemplateBlock', '_getActiveModuleInfo'));
        $o->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));
        $o->expects($this->any())->method('_getActiveModuleInfo')->will($this->returnValue($aInfo));
        $o->expects($this->once())->method('_getTemplateBlock')
            ->with($this->equalTo('module99'), $this->equalTo('contentfile99'))
            ->will($this->returnValue('content99'));

        $this->assertEquals(
            array(
                 'blockname99' => array(
                     'content99',
                 ),
            ),
            $o->getTemplateBlocks('inc/filename.tpl')
        );
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
        $oUV = new oxUtilsView();

        $sExp = $this->getConfigParam('sCompileDir') . "/smarty/";

        $this->assertSame($sExp, $oUV->getSmartyDir());

    }

}
