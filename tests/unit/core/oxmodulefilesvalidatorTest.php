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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxModuleFilesValidatorTest extends OxidTestCase
{
    public function testSetModuleGetModule()
    {
        $aModuleInformation = array('id' => 'notExistingModule');
        $oModule = new oxModule();
        $oModule->setModuleData($aModuleInformation);

        $oMetadataValidator = new oxModuleMetadataValidator();
        $oMetadataValidator->setModule($oModule);

        $this->assertSame($oModule, $oMetadataValidator->getModule(), 'Module from getter should be same as set in setter.');
    }

    /**
     * Module without any file is valid.
     */
    public function testValidateWhenModuleHasNoFiles()
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

        $oModuleFilesValidator = new oxModuleFilesValidator();
        $oModuleFilesValidator->setModule($oModule);
        $this->assertTrue($oModuleFilesValidator->validate());
    }

    public function providerValidateWhenFilesMissing()
    {
        $aExtendedFileNotExist = array('class' => 'vendor/module/path/class');
        $aFilesNotExist = array('class' => 'vendor/module/path/class.php');

        return array(
            array($aExtendedFileNotExist, array()),
            array(array(), $aFilesNotExist),
            array($aExtendedFileNotExist, $aFilesNotExist),
        );
    }

    /**
     * @param $aExtended
     * @param $aFiles
     *
     * @dataProvider providerValidateWhenFilesMissing
     */
    public function testValidateWhenFilesMissing($aExtended, $aFiles)
    {
        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtended));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFiles));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oModuleFilesValidator = new oxModuleFilesValidator();
        $oModuleFilesValidator->setModule($oModule);
        $this->assertFalse($oModuleFilesValidator->validate());
    }

    public function providerValidateWhenFilesExists()
    {
        $sFileContent = '<?php ';
        $sExtendFileName = 'class1';
        $sFileName = 'class2.php';

        $sExtendFilePath = $this->createFile($sExtendFileName.'.php', $sFileContent);
        $sFilesPath = $this->createFile($sFileName, $sFileContent);
        $sPathToModules = dirname($sFilesPath);

        $aExtendedFile = array('class1' => $sExtendFileName);
        $aFiles = array('class2' => $sFileName);
        return array(
            array($aExtendedFile, array(), $sPathToModules),
            array(array(), $aFiles, $sPathToModules),
            array($aExtendedFile, $aFiles, $sPathToModules),
        );
    }

    /**
     * @param $aExtended
     * @param $aFiles
     * @param $sPathToModules
     *
     * @dataProvider providerValidateWhenFilesExists
     */
    public function testValidateWhenFilesExists($aExtended, $aFiles, $sPathToModules)
    {
        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtended));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFiles));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oModuleFilesValidator = new oxModuleFilesValidator();
        $oModuleFilesValidator->setModule($oModule);
        $oModuleFilesValidator->setPathToModuleDirectory($sPathToModules);
        $this->assertTrue($oModuleFilesValidator->validate());
    }

    public function providerGetMissingFilesAfterValidate()
    {
        $aExtendedFileNotExist = array('class1' => 'vendor/module/path/class');
        $aFilesNotExist = array('class2' => 'vendor/module/path/class.php');
        return array(
            array($aExtendedFileNotExist, array(), $aExtendedFileNotExist),
            array(array(), $aFilesNotExist, $aFilesNotExist),
            array($aExtendedFileNotExist, $aFilesNotExist, array_merge($aExtendedFileNotExist, $aFilesNotExist)),
            array(array(), array(), array()),
        );
    }

    /**
     * @param $aExtended
     * @param $aFiles
     * @param $aMissingFiles
     *
     * @dataProvider providerGetMissingFilesAfterValidate
     */
    public function testGetMissingFilesAfterValidate($aExtended, $aFiles, $aMissingFiles)
    {
        $oModuleStub = $this->getMock('oxModule', array('getExtensions', 'getFiles'));
        $oModuleStub->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue($aExtended));
        $oModuleStub->expects($this->any())
            ->method('getFiles')
            ->will($this->returnValue($aFiles));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oModuleFilesValidator = new oxModuleFilesValidator();
        $oModuleFilesValidator->setModule($oModule);
        $oModuleFilesValidator->validate();
        $this->assertSame($aMissingFiles, $oModuleFilesValidator->getMissingFiles());
    }
}