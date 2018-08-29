<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
