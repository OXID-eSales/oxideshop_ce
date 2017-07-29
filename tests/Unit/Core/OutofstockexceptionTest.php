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

class OutofstockexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    private $testObject = null;
    private $message = 'Erik was here..';
    private $amount = 13;
    private $basketIndex = "05848170643ab0deb9914566391c0c63";

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $this->testObject = oxNew(\OxidEsales\Eshop\Core\Exception\OutOfStockException::class, $this->message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\OutOfStockException::class, get_class($this->testObject));
        $this->testObject->setRemainingAmount($this->amount);
        $this->testObject->setBasketIndex($this->basketIndex);
    }

    public function testSetDestination()
    {
        $this->assertEquals($this->message, $this->testObject->getMessage());

        $this->testObject->setDestination(null);
        $this->assertEquals($this->message . ": " . $this->amount, $this->testObject->getMessage());
    }

    public function testSetGetRemainingAmount()
    {
        $this->assertEquals($this->amount, $this->testObject->getRemainingAmount());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $stringOut = $this->testObject->getString();
        $this->assertContains($this->message, $stringOut); // Message
        $this->assertContains('OutOfStockException', $stringOut); // Exception class name
        $this->assertContains((string) $this->amount, $stringOut); // Amount remaining
    }

    public function testGetValues()
    {
        $aRes = $this->testObject->getValues();
        $this->assertArrayHasKey('remainingAmount', $aRes);
        $this->assertTrue($this->amount === $aRes['remainingAmount']);
        $this->assertTrue($this->basketIndex === $aRes['basketIndex']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxOutOfStockException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }

}
