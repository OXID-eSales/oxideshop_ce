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

class Unit_Core_oxModuleMetadataAgainstShopValidatorTest extends OxidTestCase
{

    public function testValidateWhenMetadataHasNoExtensionsAndNoFiles()
    {
        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue(array()));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue(array()));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataAgainstShop = new oxModuleMetadataAgainstShopValidator();
        $this->assertTrue($oMetadataAgainstShop->validate($oModule));
    }


    public function providerValidateWhenShopConfigMissInformation()
    {
        $aExtended = array('shop_class1' => 'vendor/module/path/module_class1');
        $aFiles = array('module_class2' => 'vendor/module/path/module_class2.php');

        return array(
            array($aExtended, array()),
            array(array(), $aFiles),
            array($aExtended, $aFiles),
        );
    }

    /**
     * @param $aExtended
     * @param $aFiles
     *
     * @dataProvider providerValidateWhenShopConfigMissInformation
     */
    public function testValidateWhenShopConfigMissInformation($aExtended, $aFiles)
    {
        $this->setConfigParam('aModules', array());

        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtended));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFiles));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataAgainstShop = new oxModuleMetadataAgainstShopValidator();
        $this->assertFalse($oMetadataAgainstShop->validate($oModule));
    }


    public function providerValidateModuleAndShopInformationMatch()
    {
        $aExtended = array(
            'shop_class1' => 'vendor/module/path/module_class1',
            'shop_class3' => 'vendor/module/path/module_class3',
        );
        $aFiles = array('module_class2' => 'vendor/module/path/module_class2.php');

        return array(
            array($aExtended, array()),
            array(array(), $aFiles),
            array($aExtended, $aFiles),
        );
    }

    /**
     * @param $aExtended
     * @param $aFiles
     *
     * @dataProvider providerValidateModuleAndShopInformationMatch
     */
    public function testValidateModuleAndShopInformationMatch($aExtended, $aFiles)
    {
        $this->setConfigParam('aModules', $aExtended);
        $this->setConfigParam('aModuleFiles', $aFiles);

        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtended));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFiles));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataAgainstShop = new oxModuleMetadataAgainstShopValidator();
        $this->assertTrue($oMetadataAgainstShop->validate($oModule));
    }

    public function providerValidateWhenModuleMissFile()
    {
        $aExtendedModule = array(
            'shop_class1' => 'vendor/module/path/module_class1',
        );
        $aExtendedShop = array(
            'shop_class1' => 'vendor/module/path/module_class1',
            'shop_class3' => 'vendor/module/path/module_class3',
        );
        $aFilesModule = array('module_class2' => 'vendor/module/path/module_class2.php');
        $aFilesShop = array('module_class2' => 'vendor/module/path/module_class2.php');

        return array(
            array($aExtendedModule, $aExtendedShop, $aFilesModule, $aFilesShop),
        );
    }

    /**
     * @param $aExtendedModule
     * @param $aExtendedShop
     * @param $aFilesModule
     * @param $aFilesShop
     *
     * @dataProvider providerValidateWhenModuleMissFile
     */
    public function testValidateWhenModuleMissFile($aExtendedModule, $aExtendedShop, $aFilesModule, $aFilesShop)
    {
        $this->markTestSkipped('Check if module has all files not implemented yet. Finalise this functionality with next iteration.');

        $this->setConfigParam('aModules', $aExtendedShop);
        $this->setConfigParam('aModuleFiles', $aFilesShop);

        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles', 'getId'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtendedModule));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFilesModule));
        $oModuleStub->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('vendor'));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataAgainstShop = new oxModuleMetadataAgainstShopValidator();
        $this->assertFalse($oMetadataAgainstShop->validate($oModule));
    }
}
