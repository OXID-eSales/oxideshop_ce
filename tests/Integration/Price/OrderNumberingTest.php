<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Price;

use oxDb;
use OxidEsales\Eshop\Application\Model\Order;
use oxOrder;
use PHPUnit\Framework\MockObject\MockObject;

require_once __DIR__. '/BasketConstruct.php';

/**
 * Class Integration_Price_OrderNumberingTest
 */
class OrderNumberingTest extends BaseTestCase
{
    /** @var string Test case directory */
    private $testCasesDirectory = "testcases/numbering";

    /** @var array Specified test cases (optional) */
    private $testCases = array(
        // "test_case.php",
    );

    /**
     * Remove admin user as test fail with sql error: duplicate users.
     *
     * @see OxidTestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();
        oxDb::getDb()->execute('DELETE FROM `oxuser` WHERE oxusername = \'admin\'');
    }

    /**
     * Truncate tables so counter reset to zero.
     *
     * @see OxidTestCase::tearDown()
     */
    protected function tearDown()
    {
        oxDb::getDb()->execute('TRUNCATE TABLE `oxorder`');
        oxDb::getDb()->execute('TRUNCATE TABLE `oxcounters`');

        parent::tearDown();
    }

    /**
     * Order startup data and expected calculations results
     *
     * @return array
     */
    public function providerOrderNumberingForDifferentShops()
    {
        return $this->getTestCases($this->testCasesDirectory, $this->testCases);
    }

    /**
     * Tests order numbering with separateNumbering parameter.
     *
     * @dataProvider providerOrderNumberingForDifferentShops
     *
     * @param array $testCase
     */
    public function testOrderNumberingForDifferentShops($testCase)
    {
        if ($testCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $options = $testCase['options'];

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order2->setSeparateNumbering($options['separateNumbering']);

        // if basket has products
        if ($basket->getProductsCount()) {
            $order2->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order2Nr = $order2->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order2Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($order1Nr, ($order2Nr - 1), 'Second order must had bigger number if no separate numbering.');
        }
    }

    /**
     * Tests order numbering when middle one is deleted.
     *
     * @dataProvider providerOrderNumberingForDifferentShops
     *
     * @param array $testCase
     */
    public function testOrderNumberingForDifferentShops2($testCase)
    {
        if ($testCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $options = $testCase['options'];

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();

        // if basket has products
        if ($basket->getProductsCount()) {
            $order2->finalizeOrder($basket, $user);
        }

        $order2->delete();

        $order3 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order3->setSeparateNumbering($options['separateNumbering']);

        // if basket has products
        if ($basket->getProductsCount()) {
            $order3->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order3Nr = $order3->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order3Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($order1Nr, ($order3Nr - 2), 'Second order must had bigger number if no separate numbering.');
        }
    }

    /**
     * Tests order numbering when middle one is saved without finalizing.
     *
     * @dataProvider providerOrderNumberingForDifferentShops
     *
     * @param array $testCase
     */
    public function testOrderNumberingForDifferentShops3($testCase)
    {
        if ($testCase['skipped'] == 1) {
            $this->markTestSkipped("testcase is skipped");
        }

        $options = $testCase['options'];

        // load calculated basket from provided data
        $basketConstruct = new BasketConstruct();
        $basket = $basketConstruct->calculateBasket($testCase);

        $user = $basket->getBasketUser();

        $order1 = $this->getOrderMock();

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        $order2 = $this->getOrderMock();
        $order2->save();

        $order3 = $this->getOrderMock();
        // If separate numbering, then it must be restarted.
        $order3->setSeparateNumbering($options['separateNumbering']);

        // if basket has products
        if ($basket->getProductsCount()) {
            $order3->finalizeOrder($basket, $user);
        }

        $order1Nr = $order1->oxorder__oxordernr->value;
        $order3Nr = $order3->oxorder__oxordernr->value;
        if ($options['separateNumbering']) {
            $this->assertEquals(1, $order3Nr, 'Second order must start from begining if separate numbering.');
        } else {
            $this->assertEquals($order1Nr, ($order3Nr - 1), 'Second order must had bigger number if no separate numbering.');
        }
    }

    private function getOrderMock()
    {
        $order = $this->getMock(Order::class, array(
            '_sendOrderByEmail',
            'validateDeliveryAddress',
            'validateDelivery',
            'validatePayment'
        ));

        $order
            ->expects($this->any())
            ->method('_sendOrderByEmail')
            ->will($this->returnValue(0));

        return $order;
    }
}
