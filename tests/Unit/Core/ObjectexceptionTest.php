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

use \stdClass;

class ObjectexceptionTest extends \OxidTestCase
{

    public function testSetGetObject()
    {
        $oTestObject = oxNew('oxObjectException');
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\ObjectException', get_class($oTestObject));
        $oObject = new stdClass();
        $oObject->sAtrib = "Atribute";
        $oTestObject->setObject($oObject);
        $this->assertEquals("Atribute", $oTestObject->getObject()->sAtrib);
    }

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxObjectException', $sMsg);
        $this->assertEquals('OxidEsales\EshopCommunity\Core\Exception\ObjectException', get_class($oTestObject));
        $oObject = new stdClass();
        $oTestObject->setObject($oObject);
        $sStringOut = $oTestObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($sMsg, $sStringOut);
        $this->assertContains('ObjectException', $sStringOut);
        $this->assertContains(get_class($oObject), $sStringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxObjectException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
