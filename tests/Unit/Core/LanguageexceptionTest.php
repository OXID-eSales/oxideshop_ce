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

class LanguageexceptionTest extends \OxidTestCase
{

    private $_oTestObject = null;
    private $_sMsg = 'Erik was here..';
    private $_sLanguageConstant = 'a language';

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_oTestObject = oxNew('oxLanguageException', $this->_sMsg);
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\LanguageException', get_class($this->_oTestObject));
        $this->_oTestObject->setLangConstant($this->_sLanguageConstant);
    }

    public function testSetGetLangConstant()
    {
        $this->assertEquals($this->_sLanguageConstant, $this->_oTestObject->getLangConstant());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $sStringOut = $this->_oTestObject->getString();
        $this->assertContains($this->_sMsg, $sStringOut); // Message
        $this->assertContains('LanguageException', $sStringOut); // Exception class name
        $this->assertContains($this->_sLanguageConstant, $sStringOut); // Language constant
    }

    public function testGetValues()
    {
        $aRes = $this->_oTestObject->getValues();
        $this->assertArrayHasKey('langConstant', $aRes);
        $this->assertTrue($this->_sLanguageConstant === $aRes['langConstant']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxLanguageException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
