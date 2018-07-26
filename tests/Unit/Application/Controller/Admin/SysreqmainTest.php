<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for sysreq_main class
 */
class SysreqmainTest extends \OxidTestCase
{

    /**
     * sysreq_main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = oxNew('sysreq_main');
        $this->assertEquals('sysreq_main.tpl', $oView->render());
    }

    /**
     * sysreq_main::GetModuleClass() test case
     *
     * @return null
     */
    public function testGetModuleClass()
    {
        // defining parameters
        $oView = oxNew('sysreq_main');
        $this->assertEquals('pass', $oView->getModuleClass(2));
        $this->assertEquals('pmin', $oView->getModuleClass(1));
        $this->assertEquals('null', $oView->getModuleClass(-1));
        $this->assertEquals('fail', $oView->getModuleClass(0));
    }


    /**
     * sysreq_main::getReqInfoUrl test. #1744 case.
     *
     * @return null
     */
    public function testGetReqInfoUrl()
    {
        $sUrl = "https://oxidforge.org/en/system-requirements";

        $oSubj = oxNew('sysreq_main');
        $this->assertEquals($sUrl . "#PHP_version_at_least_7.0", $oSubj->getReqInfoUrl("php_version", false));
        $this->assertEquals($sUrl, $oSubj->getReqInfoUrl("none", false));
        $this->assertEquals($sUrl . "#Zend_Optimizer", $oSubj->getReqInfoUrl("zend_optimizer", false));
    }


    /**
     * base test
     *
     * @return null
     */
    public function testGetMissingTemplateBlocks()
    {
        $oSubj = oxNew('sysreq_main');
        oxTestModules::addFunction('oxSysRequirements', 'getMissingTemplateBlocks', '{return "lalalax";}');
        $this->assertEquals('lalalax', $oSubj->getMissingTemplateBlocks());
    }
}
