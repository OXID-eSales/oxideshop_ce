<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
