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

class Unit_Core_oxModuleFilesValidatorTest extends OxidTestCase
{

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
        $this->assertTrue($oModuleFilesValidator->validate($oModule));
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
        $this->assertFalse($oModuleFilesValidator->validate($oModule));
    }

    public function providerValidateWhenFilesExists()
    {
        $sFileContent = '<?php ';
        $sExtendFileName = 'class1';
        $sFileName = 'class2.php';

        $sExtendFilePath = $this->createFile($sExtendFileName . '.php', $sFileContent);
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
        $oModuleFilesValidator->setPathToModuleDirectory($sPathToModules);
        $this->assertTrue($oModuleFilesValidator->validate($oModule));
    }

    public function providerGetMissingFilesAfterValidate()
    {
        $aExtendedFileNotExist = array('class1' => 'vendor/module/path/class');
        $aFilesNotExist = array('class2' => 'vendor/module/path/class.php');

        return array(
            array($aExtendedFileNotExist, array(), array('extensions' => $aExtendedFileNotExist)),
            array(array(), $aFilesNotExist, array('files' => $aFilesNotExist)),
            array($aExtendedFileNotExist, $aFilesNotExist, array('extensions' => $aExtendedFileNotExist, 'files' => $aFilesNotExist)),
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
        $oModuleFilesValidator->validate($oModule);
        $this->assertSame($aMissingFiles, $oModuleFilesValidator->getMissingFiles());
    }

    public function testResettingOfMissingFilesAfterValidation()
    {
        /** @var oxModule $oModuleStub1 */
        $oModuleStub1 = $this->getMock('oxModule', array('getExtensions'));
        $oModuleStub1->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue(array('class1' => 'vendor/module/path/class1')));

        /** @var oxModule $oModuleStub2 */
        $oModuleStub2 = $this->getMock('oxModule', array('getExtensions'));
        $oModuleStub2->expects($this->any())
            ->method('getExtensions')
            ->will($this->returnValue(array('class2' => 'vendor/module/path/class2')));

        $oModuleFilesValidator = new oxModuleFilesValidator();
        $oModuleFilesValidator->validate($oModuleStub1);
        $oModuleFilesValidator->validate($oModuleStub2);

        $this->assertSame(
            array('extensions' =>
                      array('class2' => 'vendor/module/path/class2')
            ), $oModuleFilesValidator->getMissingFiles()
        );
    }
}