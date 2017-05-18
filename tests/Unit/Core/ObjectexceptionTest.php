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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class ObjectexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    public function testSetGetObject()
    {
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\ObjectException::class, get_class($testObject));
        $object = new \stdClass();
        $object->sAtrib = "Atribute";
        $testObject->setObject($object);
        $this->assertEquals("Atribute", $testObject->getObject()->sAtrib);
    }

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew('oxObjectException', $message);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\ObjectException', get_class($testObject));
        $object = new \stdClass();
        $testObject->setObject($object);
        $sStringOut = $testObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($message, $sStringOut);
        $this->assertContains('ObjectException', $sStringOut);
        $this->assertContains(get_class($object), $sStringOut);
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
