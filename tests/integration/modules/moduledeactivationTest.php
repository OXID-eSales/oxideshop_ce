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
                    'extend' => '',
                    'files' => '',
                    'settings' => array(
                        array('group' => 'my_checkconfirm', 'name' => 'blCheckConfirm', 'type' => 'bool', 'value' => 'true'),
                        array('group' => 'my_displayname',  'name' => 'sDisplayName',   'type' => 'str',  'value' => 'Some name'),
                    ),
                    'templates' => array(),
                )
            ),
        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testModuleDeactivation( $aInstallModules, $sModule, $aResultToAsserts )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        $oModule = new oxModule();
        $oModule->load( $sModule );
        $oModule->deactivate();

        $this->_runAsserts( $aResultToAsserts, $sModule );
    }

    private function _runAsserts( $aAsserts, $sModule )
    {
        $this->_assertTemplates( $aAsserts['templates'], $sModule );
        $this->_assertBlocks(  $aAsserts['blocks'], $sModule );
        /*$this->_assertBlocks();
        $this->_assertBlocks();*/
    }

    private function _assertTemplates( $aTemplates, $sModule )
    {
        $aTemplatesToCheck = $this->getConfig()->getConfigParam( 'aModuleTemplates' );
        $aModuleTemplates = is_null( $aTemplatesToCheck[$sModule] ) ? array() : $aTemplatesToCheck[$sModule];

        $this->assertSame( $aTemplates, $aModuleTemplates );
    }

    private function _assertBlocks( $aBlocks, $sModule )
    {
        $oDb = oxDb::getDb();
        $aBlocksToCheck = $oDb->getAll( "select * from oxtplblocks where oxmodule = '$sModule'" );

        $this->assertSame( $aBlocks, $aBlocksToCheck );
    }

    private function _assertExtensions()
    {
        $aModules = $this->getConfig()->getConfigParam( 'aModules' );
    }


    private function _assertFiles()
    {
        $aModuleFiles = $this->getConfig()->getConfigParam( 'aModuleFiles' );

    }

    /**
     * @param $aSettings
     */
    private function _assertConfigs( $aSettings )
    {
        $oConfig = oxConfig::getInstance();
        foreach( $aSettings as $sKey => $sVal ) {
            $this->assertSame( $sVal, $oConfig->getConfigParam( $sKey ) );
        }
    }

}
