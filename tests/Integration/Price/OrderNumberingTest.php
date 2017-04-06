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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Price;

use oxDb;
use oxOrder;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

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

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order1 */
        $order1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order2 */
        $order2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
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

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order1 */
        $order1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order2 */
        $order2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($basket->getProductsCount()) {
            $order2->finalizeOrder($basket, $user);
        }

        $order2->delete();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order3 */
        $order3 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order3->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order3->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order3->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
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

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order1 */
        $order1 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order1->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order1->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order1->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));

        // if basket has products
        if ($basket->getProductsCount()) {
            $order1->finalizeOrder($basket, $user);
        }

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order2 */
        $order2 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order2->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order2->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order2->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
        $order2->save();

        // Mocking _sendOrderByEmail, cause Jenkins return err, while mailing after saving order
        /** @var oxOrder|MockObject $order3 */
        $order3 = $this->getMock(\OxidEsales\Eshop\Application\Model\Order::class, array('_sendOrderByEmail', 'validateDeliveryAddress', 'validateDelivery'));
        $order3->expects($this->any())->method('_sendOrderByEmail')->will($this->returnValue(0));
        $order3->expects($this->any())->method('validateDeliveryAddress')->will($this->returnValue(null));
        $order3->expects($this->any())->method('validateDelivery')->will($this->returnValue(null));
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
}
