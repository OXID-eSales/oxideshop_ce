<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: $
 */

require_once realpath(dirname(__FILE__).'/../../') . '/unit/OxidTestCase.php';
require_once realpath( dirname(__FILE__) ) . '/environment.php';

class Integration_Modules_ModuleDeactivationTest extends OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->clean();
        parent::tearDown();
    }

    public function providerModuleDeactivation()
    {
        return array(
            array(
                array( 'extending_1_class', 'with_2_templates', 'with_everything' ),
                'with_everything',
                array(
                    'blocks' => array(),
                    'extend' => array(
                        'oxorder' => 'extending_1_class/myorder&with_everything/myorder1&with_everything/myorder2&with_everything/myorder3',
                        'oxarticle' => 'with_everything/myarticle',
                        'oxuser' => 'with_everything/myuser',
                    ),
                    'files' =>  array(),
                    'settings' => array(
                        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                        array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
                    ),
                    'templates' => array(),
                    'disabledModules' => array( 'with_everything' ),
                    'versions' => array(),
                )
            ),
        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleDeactivation( $aInstallModules, $sModuleId, $aResultToAsserts )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );
        $oModule = new oxModule();
        $oModule->load( $sModuleId );
        $oModule->deactivate();

        $this->_runAsserts( $aResultToAsserts, $sModuleId );
    }

    /**
     * Runs all asserts
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _runAsserts( $aExpectedResult, $sModuleId )
    {
        $this->_assertExtensions( $aExpectedResult );
        $this->_assertBlocks( $aExpectedResult );
        $this->_assertTemplates( $aExpectedResult, $sModuleId );
        $this->_assertFiles( $aExpectedResult, $sModuleId  );
        $this->_assertConfigs( $aExpectedResult, $sModuleId );
        $this->_assertVersions( $aExpectedResult, $sModuleId );
    }

    /**
     * Asserts that module templates match expected templates
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _assertTemplates( $aExpectedResult, $sModuleId )
    {
        $aExpectedTemplates = $aExpectedResult['templates'];
        $aTemplatesToCheck = $this->getConfig()->getConfigParam( 'aModuleTemplates' );
        $aTemplatesToCheck = is_null( $aTemplatesToCheck[$sModuleId] ) ? array() : $aTemplatesToCheck[$sModuleId];

        $this->assertSame( $aExpectedTemplates, $aTemplatesToCheck, 'Module Templates were not cleared' );
    }

    /**
     * Asserts that module blocks match expected blocks
     *
     * @param $aExpectedResult
     */
    private function _assertBlocks( $aExpectedResult )
    {
        $aExpectedBlocks = $aExpectedResult['blocks'];
        $oDb = oxDb::getDb();
        $aBlocksToCheck = $oDb->getAll( 'select * from oxtplblocks' );

        $this->assertSame( $aExpectedBlocks, $aBlocksToCheck, 'Module Blocks were not cleared' );
    }

    /**
     * Asserts that module extensions match expected extensions
     *
     * @param $aExpectedResult
     */
    private function _assertExtensions( $aExpectedResult )
    {
        $aExpectedExtensions = $aExpectedResult['extend'];
        $aExpectedDisabledModules = $aExpectedResult['disabledModules'];

        $aExtensionsToCheck = $this->getConfig()->getConfigParam( 'aModules' );
        $aDisabledModules = $this->getConfig()->getConfigParam( 'aDisabledModules' );

        $this->assertSame( $aExpectedExtensions, $aExtensionsToCheck, 'Extensions were changed on deactivation' );
        $this->assertEquals( $aExpectedDisabledModules, $aDisabledModules, 'Module does not appear among disabled modules' );
    }

    /**
     * Asserts that module files match expected files
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _assertFiles( $aExpectedResult, $sModuleId )
    {
        $aExpectedFiles = $aExpectedResult['files'];
        $aModuleFilesToCheck = $this->getConfig()->getConfigParam( 'aModuleFiles' );
        $aModuleFilesToCheck = is_null( $aModuleFilesToCheck[$sModuleId] ) ? array() : $aModuleFilesToCheck[$sModuleId];

        $this->assertSame( $aExpectedFiles, $aModuleFilesToCheck, 'Module files were not cleared on deactivation' );
    }


    /**
     * Asserts that module configs match expected configs
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _assertConfigs( $aExpectedResult, $sModuleId )
    {
        $aExpectedConfigs = $aExpectedResult['settings'];
        $oDb = oxDb::getDb(  );
        $aConfigsToCheck = $oDb->getAll(
            "select c.oxvarname as `name`
            from  oxconfig c inner join oxconfigdisplay d
                on c.oxvarname = d.oxcfgvarname  and c.oxmodule = d.oxcfgmodule
            where oxmodule = 'module:{$sModuleId}'" );

        $this->assertEquals( count($aExpectedConfigs), count($aConfigsToCheck), 'Number of config settings changed on deactivation' );
    }

    /**
     * Asserts that module version match expected version
     *
     * @param $aExpectedResult
     * @param $sModuleId
     */
    private function _assertVersions( $aExpectedResult, $sModuleId )
    {
        $aExpectedVersions = $aExpectedResult['versions'];
        $aModuleVersionsToCheck = $this->getConfig()->getConfigParam( 'aModuleVersions' );
        $aModuleVersionsToCheck = is_null( $aModuleVersionsToCheck[$sModuleId] ) ? array() : $aModuleVersionsToCheck[$sModuleId];

        $this->assertSame( $aExpectedVersions, $aModuleVersionsToCheck, 'Module versions were not cleared on deactivation' );
    }

}
 