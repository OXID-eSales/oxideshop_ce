<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

use OxidEsales\Eshop\Core\Module\Module;

/**
 * @group module
 * @package Integration\Modules
 */
class ModuleWithNoMetadataSupportTest extends BaseModuleTestCase
{
    public function testModulesWithoutMetadataShouldBeAddToCleanup()
    {
        $this->installAndActivateModule('extending_1_class');

        //adding module without metadata
        $aModules = $this->getConfig()->getConfigParam('aModules');
        $aModules['oxClass'] = 'no_metadata/myClass';

        $this->getConfig()->setConfigParam('aModules', $aModules);

        $oModuleList = oxNew('oxModuleList');
        $aGarbage = $oModuleList->getDeletedExtensions();

        $this->assertSame(array('no_metadata' => array('files' => array('no_metadata/metadata.php'))), $aGarbage);
    }

    public function testModulesWithoutMetadataShouldBeAddToCleanupAllModulesWithMetadata()
    {
        $this->installAndActivateModule('extending_1_class');

        $oModuleList = oxNew('oxModuleList');
        $aGarbage = $oModuleList->getDeletedExtensions();

        $this->assertSame(array(), $aGarbage);
    }

    public function testModuleMissMatchMetadata()
    {
        $this->markTestSkipped('Currently we are not checking if metadata matches configs.');
        // modules to be activated during test preparation
        $aInstallModules = array(
            'extending_1_class', 'with_2_files'
        );

        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $aModules = $this->getConfig()->getConfigParam('aModules');
        $aModules['oxClass'] = 'extending_1_class/myClass';

        $aModuleFiles = $this->getConfig()->getConfigParam('aModuleFiles');
        $aModuleFiles['with_2_files']['myconnection'] = 'with_2_files/core/exception/myconnectionwrong.php';

        $this->getConfig()->setConfigParam('aModuleFiles', $aModuleFiles);

        $oModuleList = oxNew('oxModuleList');
        $aGarbage = $oModuleList->getDeletedExtensions();

        $aExpect = array(
            'extending_1_class' => array('oxClass' => 'extending_1_class/myClass'),
            'with_2_files'      => array('myconnection' => 'with_2_files/core/exception/myconnectionwrong.php'),
        );

        $this->assertSame($aExpect, $aGarbage);
    }

    public function testModulesWithMissingFiles()
    {
        $this->markTestSkipped('Currently we are not checking module files.');
        // modules to be activated during test preparation
        $aInstallModules = array(
            'with_1_extension', 'with_2_files'
        );

        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModuleList = oxNew('oxModuleList');
        $aGarbage = $oModuleList->getDeletedExtensions();

        $aExpect = array(
            'with_1_extension' => array(
                'extensions' => array('oxarticle' => 'with_1_extension/mybaseclass')),
            'with_2_files'     => array(
                'files' => array(
                    'myexception'  => 'with_2_files/core/exception/myexception.php',
                    'myconnection' => 'with_2_files/core/exception/myconnection.php',
                )
            ),
        );

        $this->assertSame($aExpect, $aGarbage);
    }
}
