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

class Integration_Modules_ModuleActivateWithSimilarNameTest extends BaseModuleTestCase
{

    public function providerModuleReactivation()
    {
        return array(
            $this->_caseActivateModuleFirstTime_OtherModuleWithSimilarNameIsDisabled_ExtensionsDoNotChange(),
            $this->_caseReactivateModule_OtherModuleWithSimilarNameIsDisabled_ExtensionsDoNotChange(),
        );
    }

    /**
     * Test check shop environment after activation of module with similar name as deactivated module
     *
     * @dataProvider providerModuleReactivation
     */
    public function testModuleActivateWithSimilarName($aInstallModules, $sReactivateModule, $aResultToAssert)
    {
        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        foreach ($aInstallModules as $sModule) {
            $oModule = new oxModule();
            $this->_deactivateModule($oModule, $sModule);
        }

        $oModule = new oxModule();
        $this->_activateModule($oModule, $sReactivateModule);

        $this->_runAsserts($aResultToAssert);
    }

    /**
     * Activate module first time with other module with similar name is disabled
     * expects that extensions do not change
     */
    protected function _caseActivateModuleFirstTime_OtherModuleWithSimilarNameIsDisabled_ExtensionsDoNotChange()
    {
        return array(
            // modules to be activated during test preparation
            array(
                'extending_3_classes_with_1_extension',
            ),

            // Bug 5634: Reactivating disabled module removes extensions of other deactivated modules with similar name
            // modules to be reactivated
            'with_1_extension',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass&with_1_extension/mybaseclass',
                    'oxorder'   => 'extending_3_classes_with_1_extension/mybaseclass',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'extending_3_classes_with_1_extension',
                ),
                'templates'       => array(),
                'versions'        => array(
                    'with_1_extension' => '1.0',
                ),
                'events'          => array(
                    'with_1_extension' => null,
                ),
            ),
        );
    }

    /**
     * Activate module first time with other module with similar name is disabled
     * expects that extensions do not change
     */
    protected function _caseReactivateModule_OtherModuleWithSimilarNameIsDisabled_ExtensionsDoNotChange()
    {
        return array(
            // modules to be activated during test preparation
            array(
                'extending_3_classes_with_1_extension', 'with_1_extension',
            ),

            // Bug 5634: Reactivating disabled module removes extensions of other deactivated modules with similar name
            // modules to be reactivated
            'with_1_extension',

            // environment asserts
            array(
                'blocks'          => array(),
                'extend'          => array(
                    'oxarticle' => 'extending_3_classes_with_1_extension/mybaseclass&with_1_extension/mybaseclass',
                    'oxorder'   => 'extending_3_classes_with_1_extension/mybaseclass',
                    'oxuser'    => 'extending_3_classes_with_1_extension/mybaseclass',
                ),
                'files'           => array(),
                'settings'        => array(),
                'disabledModules' => array(
                    'extending_3_classes_with_1_extension',
                ),
                'templates'       => array(),
                'versions'        => array(
                    'with_1_extension' => '1.0',
                ),
                'events'          => array(
                    'with_1_extension' => null,
                ),
            ),
        );
    }

}
 