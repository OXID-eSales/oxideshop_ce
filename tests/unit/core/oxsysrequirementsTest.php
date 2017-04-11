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

class Unit_Core_oxSysRequirementsTest extends OxidTestCase
{

    public function testGetBytes()
    {
        $oSysReq = $this->getProxyClass('oxSysRequirements');
        $this->assertEquals(33554432, $oSysReq->UNITgetBytes('32M'));
        $this->assertEquals(32768, $oSysReq->UNITgetBytes('32K'));
        $this->assertEquals(34359738368, $oSysReq->UNITgetBytes('32G'));
    }

    public function testGetRequiredModules()
    {
        $oSysReq = new oxSysRequirements();
        $aRequiredModules = $oSysReq->getRequiredModules();
            $sCnt = 25;
        if (isAdmin()) {
            $sCnt++;
        }
        $sCnt++;
        $this->assertEquals($sCnt, count($aRequiredModules));
    }

    public function testGetModuleInfo()
    {
        $oSysReq = $this->getMock('oxSysRequirements', array('checkMbString', 'checkModRewrite'));
        $oSysReq->expects($this->once())->method('checkMbString');
        $oSysReq->expects($this->never())->method('checkModRewrite');
        $oSysReq->getModuleInfo('mb_string');
    }

    public function testGetSystemInfo()
    {
        $oSysReq = $this->getProxyClass('oxSysRequirements');
        $aSysInfo = $oSysReq->getSystemInfo();
        $this->assertEquals(3, count($aSysInfo));
        $sCnt = 13;
        $this->assertEquals($sCnt, count($aSysInfo['php_extennsions']));
        $this->assertEquals(10, count($aSysInfo['php_config']));
        $sCnt = 4;
            $sCnt = 2;
        if (isAdmin()) {
            $sCnt++;
        }
        $sCnt++;
        $this->assertEquals($sCnt, count($aSysInfo['server_config']));
    }

    /**
     * Testing oxSysRequirements::checkServerPermissions()
     *
     * @return null
     */
    public function testCheckServerPermissions()
    {
        /** @var oxSysRequirements|PHPUnit_Framework_MockObject_MockObject $oSysReq */
        $oSysReq = $this->getMock('oxSysRequirements', array('isAdmin'));
        $oSysReq->expects($this->any())->method('isAdmin')->will($this->returnValue(false));

        $this->assertEquals(2, $oSysReq->checkServerPermissions());
    }

    /**
     * Testing oxSysRequirements::checkMysqlVersion()
     *
     * @return null
     */
    public function testCheckMysqlVersion()
    {
        $aRez = oxDb::getDb()->getAll("SHOW VARIABLES LIKE 'version'");
        foreach ($aRez as $aRecord) {
            $sVersion = $aRecord[1];
            break;
        }

        $iModStat = 0;
        if (version_compare($sVersion, '5.0.3', '>=') && version_compare($sVersion, '5.0.37', '<>')) {
            $iModStat = 2;
        }

        //
        $oSysReq = new oxSysRequirements();
        $this->assertEquals($iModStat, $oSysReq->checkMysqlVersion());
        $this->assertEquals(0, $oSysReq->checkMysqlVersion('5'));
        $this->assertEquals(0, $oSysReq->checkMysqlVersion('5.0.1'));
        $this->assertEquals(0, $oSysReq->checkMysqlVersion('5.0.2'));
        $this->assertEquals(2, $oSysReq->checkMysqlVersion('5.0.3'));
    }

    public function testCheckCollation()
    {
        $oSysReq = $this->getProxyClass('oxSysRequirements');
        $aCollations = $oSysReq->checkCollation();
        $this->assertEquals(0, count($aCollations));
    }

    public function testGetSysReqStatus()
    {
        $oSysReq = $this->getMock('oxSysRequirements', array('getSystemInfo'));
        $oSysReq->expects($this->once())->method('getSystemInfo');
        $this->assertTrue($oSysReq->getSysReqStatus());
    }

    /**
     * Testing oxSysRequirements::getReqInfoUrl()
     *
     * @return null
     */
    public function testGetReqInfoUrl()
    {
        $sUrl = "http://oxidforge.org/en/installation.html";

        $oSubj = new oxSysRequirements();
        $this->assertEquals($sUrl . "#PHP_version_at_least_5.3.25", $oSubj->getReqInfoUrl("php_version"));
        $this->assertEquals($sUrl, $oSubj->getReqInfoUrl("none"));
        $this->assertEquals($sUrl . "#Zend_Optimizer", $oSubj->getReqInfoUrl("zend_optimizer"));
    }

    /**
     * Testing oxSysRequirements::_getShopHostInfoFromConfig()
     *
     * @return null
     */
    public function testGetShopHostInfoFromConfig()
    {
        modConfig::getInstance()->setConfigParam('sShopURL', 'http://www.testshopurl.lt/testsubdir1/insideit2/');
        $oSC = new oxSysRequirements();
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 80,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sShopURL', 'https://www.testshopurl.lt/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 443,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sShopURL', 'https://51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sShopURL', '51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopHostInfoFromConfig()
        );

    }

    /**
     * Testing oxSysRequirements::_getShopSSLHostInfoFromConfig()
     *
     * @return null
     */
    public function testGetShopSSLHostInfoFromConfig()
    {
        modConfig::getInstance()->setConfigParam('sSSLShopURL', 'http://www.testshopurl.lt/testsubdir1/insideit2/');
        $oSC = new oxSysRequirements();
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 80,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopSSLHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sSSLShopURL', 'https://www.testshopurl.lt/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 443,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopSSLHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sSSLShopURL', 'https://51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopSSLHostInfoFromConfig()
        );
        modConfig::getInstance()->setConfigParam('sSSLShopURL', '51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopSSLHostInfoFromConfig()
        );

    }

    /**
     * Testing oxSysRequirements::_getShopHostInfoFromServerVars()
     *
     * @return null
     */
    public function testGetShopHostInfoFromServerVars()
    {
        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = null;
        $_SERVER['SERVER_PORT'] = null;
        $_SERVER['HTTP_HOST'] = 'www.testshopurl.lt';

        $oSC = new oxSysRequirements();
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 80,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = null;
        $_SERVER['HTTP_HOST'] = 'www.testshopurl.lt';
        $this->assertEquals(
            array(
                 'host' => 'www.testshopurl.lt',
                 'port' => 443,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = 21;
        $_SERVER['HTTP_HOST'] = '51.1586.51.15';
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => true,
            ),
            $oSC->UNITgetShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = null;
        $_SERVER['SERVER_PORT'] = '21';
        $_SERVER['HTTP_HOST'] = '51.1586.51.15';
        $this->assertEquals(
            array(
                 'host' => '51.1586.51.15',
                 'port' => 21,
                 'dir'  => '/testsubdir1/insideit2/',
                 'ssl'  => false,
            ),
            $oSC->UNITgetShopHostInfoFromServerVars()
        );
    }

    /**
     * base functionality test
     */
    public function testCheckTemplateBlock()
    {
        $oCfg = $this->getMock('oxconfig', array('getTemplatePath'));
        $oCfg->expects($this->at(0))->method('getTemplatePath')
            ->with($this->equalTo('test0'), $this->equalTo(false))
            ->will($this->returnValue(dirname(__FILE__) . '/../moduleTestBlock/testTpl_nonexisting.tpl'));
        $oCfg->expects($this->at(1))->method('getTemplatePath')
            ->with($this->equalTo('test0'), $this->equalTo(true))
            ->will($this->returnValue(dirname(__FILE__) . '/../moduleTestBlock/testTpl_nonexisting.tpl'));
        $oCfg->expects($this->at(2))->method('getTemplatePath')
            ->with($this->equalTo('test1'), $this->equalTo(false))
            ->will($this->returnValue(dirname(__FILE__) . '/../moduleTestBlock/testTpl.tpl'));
        $oCfg->expects($this->at(3))->method('getTemplatePath')
            ->with($this->equalTo('test1'), $this->equalTo(false))
            ->will($this->returnValue(dirname(__FILE__) . '/../moduleTestBlock/testTpl.tpl'));
        $oCfg->expects($this->at(4))->method('getTemplatePath')
            ->with($this->equalTo('test1'), $this->equalTo(false))
            ->will($this->returnValue(dirname(__FILE__) . '/../moduleTestBlock/testTpl.tpl'));

        $oSR = $this->getMock('oxSysRequirements', array("getConfig"));
        $oSR->expects($this->any())->method('getConfig')->will($this->returnValue($oCfg));

        $this->assertFalse($oSR->UNITcheckTemplateBlock('test0', 'nonimportanthere'));
        $this->assertTrue($oSR->UNITcheckTemplateBlock('test1', 'block1'));
        $this->assertTrue($oSR->UNITcheckTemplateBlock('test1', 'block2'));
        $this->assertFalse($oSR->UNITcheckTemplateBlock('test1', 'block3'));
    }

    /**
     * base functionality test
     */
    public function testGetMissingTemplateBlocksIfNotFound()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopId'));
        $oCfg->expects($this->exactly(1))->method('getShopId')
            ->will($this->returnValue(15));
        oxTestModules::addModuleObject('oxconfig', $oCfg);

        $oRs = $this->getMock('stdclass', array('moveNext', 'recordCount'));
        $oRs->expects($this->exactly(1))->method('moveNext')
            ->will($this->evalFunction('{$_this->EOF = true;}'));
        $oRs->expects($this->exactly(1))->method('recordCount')
            ->will($this->returnValue(1));
        $oRs->fields = array(
            'OXTEMPLATE'  => '_OXTEMPLATE_',
            'OXBLOCKNAME' => '_OXBLOCKNAME_',
            'OXMODULE'    => '_OXMODULE_',
        );

        $oDb = $this->getMock('stdclass', array('execute', 'quote'));
        $oDb->expects($this->exactly(1))->method('execute')
            ->with($this->equalTo("select * from oxtplblocks where oxactive=1 and oxshopid='15'"))
            ->will($this->returnValue($oRs));
        $oDb->expects($this->exactly(1))->method('quote')
            ->with($this->equalTo(15))
            ->will($this->returnValue("'15'"));
        modDB::getInstance()->modAttach($oDb);

        $oSR = $this->getMock('oxSysRequirements', array('_checkTemplateBlock'));
        $oSR->expects($this->exactly(1))->method('_checkTemplateBlock')
            ->with($this->equalTo("_OXTEMPLATE_"), $this->equalTo("_OXBLOCKNAME_"))
            ->will($this->returnValue(false));

        $this->assertEquals(
            array(array(
                      'module'   => '_OXMODULE_',
                      'block'    => '_OXBLOCKNAME_',
                      'template' => '_OXTEMPLATE_',
                  )),
            $oSR->getMissingTemplateBlocks()
        );
    }

    /**
     * base functionality test
     */
    public function testGetMissingTemplateBlocksIfFound()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopId'));
        $oCfg->expects($this->exactly(1))->method('getShopId')
            ->will($this->returnValue(15));
        oxTestModules::addModuleObject('oxconfig', $oCfg);

        $oRs = $this->getMock('stdclass', array('moveNext', 'recordCount'));
        $oRs->expects($this->exactly(1))->method('moveNext')
            ->will($this->evalFunction('{$_this->EOF = true;}'));
        $oRs->expects($this->exactly(1))->method('recordCount')
            ->will($this->returnValue(1));
        $oRs->fields = array(
            'OXTEMPLATE'  => '_OXTEMPLATE_',
            'OXBLOCKNAME' => '_OXBLOCKNAME_',
            'OXMODULE'    => '_OXMODULE_',
        );

        $oDb = $this->getMock('stdclass', array('execute', 'quote'));
        $oDb->expects($this->exactly(1))->method('execute')
            ->with($this->equalTo("select * from oxtplblocks where oxactive=1 and oxshopid='15'"))
            ->will($this->returnValue($oRs));
        $oDb->expects($this->exactly(1))->method('quote')
            ->with($this->equalTo(15))
            ->will($this->returnValue("'15'"));
        modDB::getInstance()->modAttach($oDb);

        $oSR = $this->getMock('oxSysRequirements', array('_checkTemplateBlock'));
        $oSR->expects($this->exactly(1))->method('_checkTemplateBlock')
            ->with($this->equalTo("_OXTEMPLATE_"), $this->equalTo("_OXBLOCKNAME_"))
            ->will($this->returnValue(true));

        $this->assertEquals(
            array(),
            $oSR->getMissingTemplateBlocks()
        );
    }

    /**
     * Test case for oxSysRequirements::checkBug53632() when php 32bit
     *
     * @return null
     */
    public function testcheckBug53632_32bits()
    {
        $iState = 1;
        if (version_compare(PHP_VERSION, "5.3", ">=")) {
            $iState = version_compare(PHP_VERSION, "5.3.5", ">=") ? 2 : $iState;
        } elseif (version_compare(PHP_VERSION, '5.2', ">=")) {
            $iState = version_compare(PHP_VERSION, "5.2.17", ">=") ? 2 : $iState;
        }
        $oSysReq = $this->getMock('oxSysRequirements', array('_getPhpIntSize'));
        $oSysReq->expects($this->once())->method('_getPhpIntSize')->will($this->returnValue(4));
        $this->assertEquals($iState, $oSysReq->checkBug53632());
    }

    /**
     * Test case for oxSysRequirements::checkBug53632() when php 64bit
     *
     * @return null
     */
    public function testcheckBug53632_64bits()
    {
        $iState = 2;
        $oSysReq = $this->getMock('oxSysRequirements', array('_getPhpIntSize'));
        $oSysReq->expects($this->once())->method('_getPhpIntSize')->will($this->returnValue(8));
        $this->assertEquals($iState, $oSysReq->checkBug53632());
    }

    public function providerCheckPhpVersion()
    {
        return array(
            array('5.2', 0),
            array('5.2.3', 0),
            array('5.3.0', 1),
            array('5.3', 1),
            array('5.3.25', 2),
            array('5.4', 2),
            array('5.4.2', 2),
        );
    }

    /**
     * @param $sVersion
     * @param $iResult
     *
     * @dataProvider providerCheckPhpVersion
     */
    public function testCheckPhpVersion($sVersion, $iResult)
    {
        $oSysRequirements = $this->getMock('oxSysRequirements', array('getPhpVersion'));
        $oSysRequirements->expects($this->once())->method('getPhpVersion')->will($this->returnValue($sVersion));
        /** @var oxSysRequirements $oSysRequirements */

        $this->assertSame($iResult, $oSysRequirements->checkPhpVersion());
    }

    /**
     * Provides different server configuration to check memory limit.
     *
     * @return array
     */
    public function providerCheckMemoryLimit()
    {
            $aMemoryLimitsWithExpectedSystemHealth = array(
                array('8M', 0),
                array('14M', 1),
                array('30M', 2),
                array('-1', 2),
            );

        return $aMemoryLimitsWithExpectedSystemHealth;
    }

    /**
     * Testing oxSysRequirements::checkMemoryLimit()
     * contains assertion for bug #5083
     *
     * @param string $sMemoryLimit    how much memory allocated.
     * @param int    $iExpectedResult if fits system requirements.
     *
     * @dataProvider providerCheckMemoryLimit
     *
     * @return null
     */
    public function testCheckMemoryLimit($sMemoryLimit, $iExpectedResult)
    {
        /** @var oxSysRequirements $oSysReq */
        $oSysReq = oxNew('oxSysRequirements');
        $this->assertEquals($iExpectedResult, $oSysReq->checkMemoryLimit($sMemoryLimit));
    }
}
