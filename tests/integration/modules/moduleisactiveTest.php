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

require_once realpath(dirname(__FILE__)) . '/basemoduleTestCase.php';

class Integration_Modules_ModuleIsActiveTest extends BaseModuleTestCase
{

    public function providerModuleIsActive()
    {
        return array(
            array(
                array('extending_1_class', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'with_everything'),
                array(
                    'active'    => array('with_2_templates'),
                    'notActive' => array('extending_1_class', 'with_everything'),
                )
            ),
            array(
                array('extending_1_class', 'with_2_templates', 'with_everything'),
                array(),
                array(
                    'active'    => array('extending_1_class', 'with_2_templates', 'with_everything'),
                    'notActive' => array(),
                )
            ),

            array(
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array(
                    'active'    => array(),
                    'notActive' => array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                )
            ),

            array(
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                array(
                    'active'    => array(),
                    'notActive' => array('extending_1_class', 'extending_1_class_3_extensions', 'no_extending', 'with_2_templates', 'with_everything'),
                )
            ),
            array(
                array('no_extending'),
                array(),
                array(
                    'active'    => array('no_extending'),
                    'notActive' => array(),
                )
            ),
            array(
                array('no_extending'),
                array('no_extending'),
                array(
                    'active'    => array(),
                    'notActive' => array('no_extending'),
                )
            ),

        );
    }

    /**
     * Tests if module was activated.
     *
     * @dataProvider providerModuleIsActive
     */
    public function testIsActive($aInstallModules, $aDeactivateModules, $aResultToAssert)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        //deactivation
        $oModule = new oxModule();

        foreach ($aDeactivateModules as $sModule) {
            $this->_deactivateModule($oModule, $sModule);
        }

        //assertion
        foreach ($aResultToAssert['active'] as $sModule) {
            $oModule->load($sModule);
            $this->assertTrue($oModule->isActive());
        }

        foreach ($aResultToAssert['notActive'] as $sModule) {
            $oModule->load($sModule);
            $this->assertFalse($oModule->isActive());
        }
    }
}
 