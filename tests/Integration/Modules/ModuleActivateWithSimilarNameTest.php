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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

class ModuleActivateWithSimilarNameTest extends BaseModuleTestCase
{
    /**
     * @return array
     */
    public function providerModuleReactivation()
    {
        return array(
            $this->caseActivateModuleFirstTimeOtherModuleWithSimilarNameIsDisabledExtensionsDoNotChange(),
            $this->caseReactivateModuleOtherModuleWithSimilarNameIsDisabledExtensionsDoNotChange(),
        );
    }

    /**
     * Test check shop environment after activation of module with similar name as deactivated module
     *
     * @dataProvider providerModuleReactivation
     *
     * @param array  $installModules
     * @param string $sReactivateModule
     * @param array  $aResultToAssert
     */
    public function testModuleActivateWithSimilarName($installModules, $sReactivateModule, $aResultToAssert)
    {
        $environment = new Environment();
        $environment->prepare($installModules);

        foreach ($installModules as $sModule) {
            $oModule = oxNew('oxModule');
            $this->deactivateModule($oModule, $sModule);
        }

        $oModule = oxNew('oxModule');
        $this->activateModule($oModule, $sReactivateModule);

        $this->runAsserts($aResultToAssert);
    }

    /**
     * Activate module first time with other module with similar name is disabled
     * expects that extensions do not change
     *
     * @return array
     */
    protected function caseActivateModuleFirstTimeOtherModuleWithSimilarNameIsDisabledExtensionsDoNotChange()
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
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_3_classes_with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass',
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
     *
     * @return array
     */
    protected function caseReactivateModuleOtherModuleWithSimilarNameIsDisabledExtensionsDoNotChange()
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
                    \OxidEsales\Eshop\Application\Model\Article::class => 'extending_3_classes_with_1_extension/mybaseclass&with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\Order::class   => 'extending_3_classes_with_1_extension/mybaseclass',
                    \OxidEsales\Eshop\Application\Model\User::class    => 'extending_3_classes_with_1_extension/mybaseclass',
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
