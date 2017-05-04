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

class Integration_Modules_ModuleWithNoMetadataSupportTest extends BaseModuleTestCase
{

    public function testModulesWithoutMetadataShouldBeAddToCleanup()
    {
        // modules to be activated during test preparation
        $aInstallModules = array(
            'extending_1_class'
        );

        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        //adding module without metadata
        $aModules = $this->getConfig()->getConfigParam('aModules');
        $aModules['oxClass'] = 'no_metadata/myClass';

        $this->getConfig()->setConfigParam('aModules', $aModules);

        $oModuleList = new oxModuleList();
        $aGarbage = $oModuleList->getDeletedExtensions();

        $this->assertSame(array('no_metadata' => array('files' => array('no_metadata/metadata.php'))), $aGarbage);
    }

    public function testModulesWithoutMetadataShouldBeAddToCleanupAllModulesWithMetadata()
    {
        // modules to be activated during test preparation
        $aInstallModules = array(
            'extending_1_class'
        );

        $oEnvironment = new Environment();
        $oEnvironment->prepare($aInstallModules);

        $oModuleList = new oxModuleList();
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

        $oModuleList = new oxModuleList();
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

        $oModuleList = new oxModuleList();
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
 