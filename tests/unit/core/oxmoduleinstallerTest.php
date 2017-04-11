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

class Unit_Core_oxModuleInstallerTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxconfig');
        $this->cleanUpTable('oxconfigdisplay');
        $this->cleanUpTable('oxtplblocks');

        parent::tearDown();
    }

    /**
     * oxModuleInstaller::activate() test case, empty array
     *
     * @return null
     */
    public function testActivate()
    {
        $aModulesBefore = array();
        $aModulesAfter = array('oxtest' => 'testdir/mytest');

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $aExtends = array('oxtest' => 'testdir/mytest');
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue($aExtends));

        $oModuleInstaller = new oxModuleInstaller();

        oxRegistry::getConfig()->setConfigParam("aModules", $aModulesBefore);

        $this->assertEquals($aModulesBefore, oxRegistry::getConfig()->getConfigParam("aModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));
        $this->assertEquals($aModulesAfter, oxRegistry::getConfig()->getConfigParam("aModules"));
    }

    /**
     * oxModuleInstaller::activate() test case, already activated
     *
     * @return null
     */
    public function testActivateActive()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter = array('oxtest' => 'test/mytest');
        $aDisabledModulesBefore = array('test');
        $aDisabledModulesAfter = array();

        $oModuleInstaller = new oxModuleInstaller();

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue(array('oxtest' => 'test/mytest')));

        oxRegistry::getConfig()->setConfigParam("aModules", $aModulesBefore);
        oxRegistry::getConfig()->setConfigParam("aDisabledModules", $aDisabledModulesBefore);

        $this->assertEquals($aModulesBefore, $this->getConfig()->getConfigParam("aModules"));
        $this->assertEquals($aDisabledModulesBefore, $this->getConfig()->getConfigParam("aDisabledModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));

        $this->assertEquals($aModulesAfter, $this->getConfig()->getConfigParam("aModules"));
        $this->assertEquals($aDisabledModulesAfter, $this->getConfig()->getConfigParam("aDisabledModules"));
    }

    /**
     * oxModuleInstaller::activate() test case, append to chain
     *
     * @return null
     */
    public function testActivateChain()
    {
        $aModulesBefore = array('oxtest' => 'test/mytest');
        $aModulesAfter = array('oxtest' => 'test/mytest&test1/mytest1');

        $oModule = $this->getMock('oxModule', array('getId', 'getExtensions'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test1'));
        $oModule->expects($this->any())->method('getExtensions')->will($this->returnValue(array('oxtest' => 'test1/mytest1')));

        $oModuleInstaller = new oxModuleInstaller();

        oxRegistry::getConfig()->setConfigParam("aModules", $aModulesBefore);
        $this->assertEquals($aModulesBefore, $this->getConfig()->getConfigParam("aModules"));

        $this->assertTrue($oModuleInstaller->activate($oModule));
        $this->assertEquals($aModulesAfter, $this->getConfig()->getConfigParam("aModules"));
    }

    /**
     * 0005319: Modules which not extending anything is not active
     *
     * @deprecated
     */
    public function testActivate_moduleDoNotExtend_activateSuccess()
    {
        $oModule = $this->getProxyClass('oxmodule');
        $sModuleId = 'oxtest';
        $aModule = array(
            'id'     => $sModuleId,
            'files'  => array(
                'oxpsmyemptymodulemodule' => 'oxps/myemptymodule/core/oxpsmyemptymodulemodule.php',
            ),
            'blocks' => array(
                array('template' => 'footer.tpl', 'block' => 'footer_main', 'file' => '/application/views/blocks/myemptymodulefooter.tpl'),
            ),
        );
        $oModule->setNonPublicVar('_aModule', $aModule);
        $oModule->setNonPublicVar('_blMetadata', true);

        $oModuleInstaller = new oxModuleInstaller();

        $aDisabledModules = $this->getConfigParam('aDisabledModules');
        $aDisabledModules[] = $sModuleId;
        $this->getConfig()->saveShopConfVar('arr', 'aDisabledModules', $aDisabledModules);

        $this->assertFalse($oModule->isActive(), 'Module should not be active before activating.');
        $this->assertTrue($oModuleInstaller->activate($oModule), 'Module should activate successfully.');

        $aDisabledModules = $this->getConfigParam('aDisabledModules');
        $this->assertFalse(in_array($sModuleId, $aDisabledModules), 'Module should be removed from not active module list.');

        $this->assertTrue($oModule->isActive(), 'Module should be active after activating.');
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case, empty
     */
    public function testBuildModuleChainsEmpty()
    {
        $oModuleInstaller = new oxModuleInstaller();

        $aModules = array();
        $aModulesArray = array();
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case, single
     */
    public function testBuildModuleChainsSingle()
    {
        $oModuleInstaller = new oxModuleInstaller();

        $aModules = array('oxtest' => 'test/mytest');
        $aModulesArray = array('oxtest' => array('test/mytest'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * oxModuleInstaller::buildModuleChains() test case
     */
    public function testBuildModuleChains()
    {
        $oModuleInstaller = new oxModuleInstaller();

        $aModules = array('oxtest' => 'test/mytest&test1/mytest1');
        $aModulesArray = array('oxtest' => array('test/mytest', 'test1/mytest1'));
        $this->assertEquals($aModules, $oModuleInstaller->buildModuleChains($aModulesArray));
    }

    /**
     * Test for bug #5656
     * Checks if call order of protected methods is correct
     *
     */
    public function testDeactivate_eventCalledBeforeDeactivating()
    {
        $oModule = $this->getMock('oxModule', array('getId'));
        $oModule->expects($this->any())->method('getId')->will($this->returnValue('test'));

        $oModuleInstaller = $this->getMock('oxModuleInstaller', array('_addToDisabledList', '_callEvent'));
        $oModuleInstaller->expects($this->at(0))->method('_callEvent')->with();
        $oModuleInstaller->expects($this->at(1))->method('_addToDisabledList')->with();

        $oModuleInstaller->deactivate($oModule);
    }

}