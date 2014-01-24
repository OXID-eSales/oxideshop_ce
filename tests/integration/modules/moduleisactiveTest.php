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

class Integration_Modules_ModuleIsActiveTest extends OxidTestCase
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
                array( 'extending_1_class', 'with_everything' ),
                array(
                    'active' => array('with_2_templates'),
                    'notActive' => array('extending_1_class', 'with_everything'),
                )
            ),
            array(
                array( 'extending_1_class', 'with_2_templates', 'with_everything' ),
                array(),
                array(
                    'active' => array('extending_1_class', 'with_2_templates', 'with_everything'),
                    'notActive' => array(),
                )
            ),
        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleDeactivation
     */
    public function testIsActive( $aInstallModules, $aDeactivateModules, $aResultToAssert )
    {
        $oModuleEnvironment = new Environment();
        $oModuleEnvironment->prepare( $aInstallModules );

        //deactivation
        $oModule = new oxModule();
        foreach( $aDeactivateModules as $sModule){
            $oModule->load( $sModule );
            $oModule->deactivate();
        }

        //assertion
        foreach( $aResultToAssert['active'] as $sModule ){
            $oModule->load( $sModule );
            $this->assertTrue( $oModule->isActive());
        }

        foreach( $aResultToAssert['notActive'] as $sModule ){
            $oModule->load( $sModule );
            $this->assertFalse( $oModule->isActive());
        }
    }


}
