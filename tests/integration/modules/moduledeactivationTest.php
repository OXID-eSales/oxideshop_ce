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
                    'files' => '',
                    'settings' => array(
                        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                        array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
                    ),
                    'templates' => array(),
                    'disabledModules' => array( 'with_everything' ),
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

        //$aSettings = $oModule->getInfo("settings");
        $this->_runAsserts( $aResultToAsserts, $sModuleId );
    }

    private function _runAsserts( $aExpectedResult, $sModuleId )
    {
        $this->_assertTemplates( $aExpectedResult, $sModuleId );
        $this->_assertBlocks(  $aExpectedResult );
        $this->_assertExtensions( $aExpectedResult );
        //$this->_assertConfigs( $aExpectedResult, $sModuleId );
    }

    private function _assertTemplates( $aExpectedResult, $sModule )
    {
        $aTemplates = $aExpectedResult['templates'];
        $aTemplatesToCheck = $this->getConfig()->getConfigParam( 'aModuleTemplates' );
        $aTemplatesToCheck = is_null( $aTemplatesToCheck[$sModule] ) ? array() : $aTemplatesToCheck[$sModule];

        $this->assertSame( $aTemplates, $aTemplatesToCheck );
    }

    private function _assertBlocks( $aExpectedResult )
    {
        $aBlocks = $aExpectedResult['blocks'];
        $oDb = oxDb::getDb();
        $aBlocksToCheck = $oDb->getAll( "select * from oxtplblocks" );

        $this->assertSame( $aBlocks, $aBlocksToCheck );
    }

    private function _assertExtensions( $aExpectedResult )
    {
        $aExtensionsToCheck = $this->getConfig()->getConfigParam( 'aModules' );
        $aDisabledModules = $this->getConfig()->getConfigParam( 'aDisabledModules' );

        $this->assertSame( $aExpectedResult['extend'], $aExtensionsToCheck );
        $this->assertEquals( $aExpectedResult['disabledModules'], $aDisabledModules );
    }


    private function _assertFiles()
    {
    }


    private function _assertConfigs( $aExpectedResult, $sModuleId )
    {
    }

}
