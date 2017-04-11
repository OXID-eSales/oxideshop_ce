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

/**
 * Tests for sysreq_main class
 */
class Unit_Admin_sysreqmainTest extends OxidTestCase
{

    /**
     * sysreq_main::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new sysreq_main();
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
        $oView = new sysreq_main();
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
        $sUrl = "http://oxidforge.org/en/installation.html";

        $oSubj = new sysreq_main();
        $this->assertEquals($sUrl . "#PHP_version_at_least_5.3.25", $oSubj->getReqInfoUrl("php_version", false));
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
        $oSubj = new sysreq_main();
        oxTestModules::addFunction('oxSysRequirements', 'getMissingTemplateBlocks', '{return "lalalax";}');
        $this->assertEquals('lalalax', $oSubj->getMissingTemplateBlocks());
    }
}
