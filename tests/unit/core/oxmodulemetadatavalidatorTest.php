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

class Unit_Core_oxModuleMetadataValidatorTest extends OxidTestCase
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

    public function testValidateModuleWithoutMetadataFile()
    {
        $sPathToMetadata = '';
        $oModuleStub = $this->getMock('oxModule', array('getMetadataPath'));
        $oModuleStub->expect($this->any())
            ->method('getMetadataPath')
            ->will($this->returnValue($sPathToMetadata));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataValidator = new oxModuleMetadataValidator();
        $oMetadataValidator->setModule($oModule);
        $this->assertSame(false, $oMetadataValidator->validate());
    }

    public function testValidateModuleWithInvalidMetadataFile()
    {
        $sMetadataFileName = 'metadata.php';
        $sMetadataContent = '<?php php syntax error';

        $sPathToMetadata = $this->createFile($sMetadataFileName, $sMetadataContent);

        $oModuleStub = $this->getMock('oxModule', array('getMetadataPath'));
        $oModuleStub->expects($this->any())
            ->method('getMetadataPath')
            ->will($this->returnValue($sPathToMetadata));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataValidator = new oxModuleMetadataValidator();
        $oMetadataValidator->setModule($oModule);

        $this->assertSame(false, $oMetadataValidator->validate());
    }

    public function testValidateModuleWithNonPHPMetadataFile()
    {
        $sMetadataFileName = 'metadata.php';
        $sMetadataContent = 'not php content';

        $sPathToMetadata = $this->createFile($sMetadataFileName, $sMetadataContent);

        $oModuleStub = $this->getMock('oxModule', array('getMetadataPath'));
        $oModuleStub->expects($this->any())
            ->method('getMetadataPath')
            ->will($this->returnValue($sPathToMetadata));

        /** @var oxModule $oModule */
        $oModule = $oModuleStub;

        $oMetadataValidator = new oxModuleMetadataValidator();
        $oMetadataValidator->setModule($oModule);

        $this->assertSame(false, $oMetadataValidator->validate());
    }
}