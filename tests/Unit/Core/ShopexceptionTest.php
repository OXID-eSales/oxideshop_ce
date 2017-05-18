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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class ShopexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\ShopException::class, $message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\ShopException::class, get_class($testObject));
        $stringOut = $testObject->getString(); // (string)$testObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($message, $stringOut);
        $this->assertContains('ShopException', $stringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxShopException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
