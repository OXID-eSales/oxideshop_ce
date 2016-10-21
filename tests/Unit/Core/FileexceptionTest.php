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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Core;

class FileexceptionTest extends \OxidTestCase
{

    private $_oTestObject = null;
    private $_sMsg = 'Erik was here..';
    private $_sFileName = 'a file name';
    private $_sFileError = 'a error text';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oTestObject = oxNew('oxFileException', $this->_sMsg);
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\FileException', get_class($this->_oTestObject));
        $this->_oTestObject->setFileName($this->_sFileName);
        $this->_oTestObject->setFileError($this->_sFileError);
    }

    public function testSetGetFileName()
    {
        $this->assertEquals($this->_sFileName, $this->_oTestObject->getFileName());
    }

    public function testSetGetFileError()
    {
        $this->assertEquals($this->_sFileError, $this->_oTestObject->getFileError());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $sStringOut = $this->_oTestObject->getString();
        $this->assertContains($this->_sMsg, $sStringOut); // Message
        $this->assertContains('FileException', $sStringOut); // Exception class name
        $this->assertContains($this->_sFileName, $sStringOut); // File name
        $this->assertContains($this->_sFileError, $sStringOut); // File error
    }

    public function testGetValues()
    {
        $aRes = $this->_oTestObject->getValues();
        $this->assertArrayHasKey('fileName', $aRes);
        $this->assertTrue($this->_sFileName === $aRes['fileName']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxFileException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
