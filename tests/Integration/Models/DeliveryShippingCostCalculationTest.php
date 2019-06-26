<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Models;

use oxArticle;
use oxDb;
use oxDelivery;
use oxField;
use oxRegistry;
use oxUser;

/**
 * Integration test for ESDEV-2954 delivery rules and shipping cost calculation.
 */
class DeliveryShippingCostCalculationTest extends \OxidTestCase
{
    /**
     * Make a copy of Stewart+Brown Shirt Kisser Fish parent and variant L violet for testing
     */
    const SOURCE_ARTICLE_ID = '6b6d966c899dd9977e88f842f67eb751';
    const SOURCE_ARTICLE_PARENT_ID = '6b6099c305f591cb39d4314e9a823fc1';

    const TEST_ARTICLE_PRICE = 10.0;

    const TESTVOUCHER_ID_PREFIX = 'testvoucher_relative_';

    /**
     * Test delivery rules oxid/name map.
     *
     * @var array
     */
    private $deliveries = array();

    private $testUserId = null;

    /** @var string Generated oxids for test article, user, order, discount and vouchers. */
    private $categoryIds = array(
        '943927cd5d60751015b567794d3239bb',
        '943202124f58e02e84bb228a9a2a9f1e',
        '94342f1d6f3b6fe9f1520d871f566511'
    );

    /**
     * Store original shop configuration and session values.
     *
     * @var mixed
     */
    private $originalSessionChallenge = null;

    /**
     * Fixture setUp.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->originalSessionChallenge = oxRegistry::getSession()->getVariable('sess_challenge');

        $query = 'update oxdelivery set oxactive = 0';
        oxDb::getDb()->execute($query);
    }

    /*
    * Fixture tearDown.
    */
    protected function tearDown()
    {
        $this->cleanUpTable('oxarticles');
        $this->cleanUpTable('oxuser');
        $this->cleanUpTable('oxdelivery');
        $this->cleanUpTable('oxdeliveryset');
        $this->cleanUpTable('oxdel2delset');
        $this->cleanUpTable('oxobject2delivery');
        $this->cleanUpTable('oxobject2payment');
        $this->cleanUpTable('oxobject2category');

        $query = 'update oxdelivery set oxactive = 1';
        oxDb::getDb()->execute($query);

        $query = 'update oxdelivery set oxactive = 1';
        oxDb::getDb()->execute($query);

        oxRegistry::getSession()->delBasket();
        oxRegistry::getSession()->deleteVariable('_newitem');
        oxRegistry::getSession()->setVariable('sess_challenge', $this->originalSessionChallenge);
        $_POST = array();

        parent::tearDown();
    }

    /**
     * Data provider for testeliveryCostRules.
     */
    public function providerDeliveryCostRules()
    {
        $data = array();

        //add 10 EUR if basket value is between 0 and 100 EUR, once per cart, not stopping further rules
        $data['basket_value_50'][0]['rules'][0] = array(
            'oxtitle'      => 'first',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '10',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '100',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 100,
            'oxfinalize'   => 0
        );

        //add 20 EUR if basket value is between 100 and 200 EUR (what happens at 100 sharp?), once per cart,
        //not stopping further rules
        $data['basket_value_50'][0]['rules'][1] = array(
            'oxtitle'      => 'second',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '20',
            'oxdeltype'    => 'p',
            'oxparam'      => '100',
            'oxparamend'   => '200',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 200,
            'oxfinalize'   => 0
        );

        //add 30 EUR if basket value is between 0 and 200 EUR, once per cart,
        //not stopping further rules
        $data['basket_value_50'][0]['rules'][2] = array(
            'oxtitle'      => 'third',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '30',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '200',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 300,
            'oxfinalize'   => 0
        );

        //amount of articles to purchase
        $data['basket_value_50'][0]['buyamount'] = 5;

        //what do we expect as result for shippings costs?
        $data['basket_value_50'][0]['expected_deliverycost'] = 40.0;

        //test with same rules once more, change basket value
        $data['basket_value_100'][0]['rules']                 = $data['basket_value_50'][0]['rules'];
        $data['basket_value_100'][0]['buyamount']             = 10;   //exactly 100 EUR basket value matches all three rules
        $data['basket_value_100'][0]['expected_deliverycost'] = 60.0; //

        //test again but set stopper on all rules. first match should stop further calculation.
        $data['basket_value_100_finalize'][0]['rules']                  = $data['basket_value_50'][0]['rules'];
        $data['basket_value_100_finalize'][0]['rules'][0]['oxfinalize'] = 1;
        $data['basket_value_100_finalize'][0]['rules'][1]['oxfinalize'] = 1;
        $data['basket_value_100_finalize'][0]['rules'][2]['oxfinalize'] = 1;
        $data['basket_value_100_finalize'][0]['buyamount']              = 10;   //exactly 100 EUR basket value matches all three rules
        $data['basket_value_100_finalize'][0]['expected_deliverycost']  = 10.0; //expect first rule to match

        return $data;
    }

    /**
     * Test if shop matches the delivery cost rules as expected.
     *
     * @dataProvider providerDeliveryCostRules
     */
    public function testDeliveryCostRules($data)
    {
        $testArticleId = $this->insertArticle();
        $this->insertUser();

        $user = oxNew('oxUser');
        $user->load($this->testUserId);

        $deliveryIds = array();
        foreach ($data['rules'] as $rule) {
            $deliveryIds[] = $this->createRule($rule)->getId();
        }
        $deliverySetId = $this->createDeliverySet($deliveryIds);

        $basket = oxNew('oxBasket');
        $basket->addToBasket($testArticleId, $data['buyamount']);
        $basket->setPayment('oxidinvoice');
        $basket->setBasketUser($user);
        $basket->setShipping($deliverySetId);
        $basket->calculateBasket();

        $deliveryCost = $basket->getDeliveryCost()->getPrice();
        $this->assertSame($data['expected_deliverycost'], $deliveryCost);
    }

    /**
     * Data provider for testDeliveryCostRulesWithCategoryAssigned.
     */
    public function providerDeliveryCostRulesWithCategoryAssigned()
    {
        $data = array();

        //add 10 EUR if basket value is between 0 and 100 EUR, once per cart, stopping further rules
        $data['once_per_cart'][0]['rules'][0] = array(
            'oxtitle'      => 'first',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '10',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '100',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 100,
            'oxfinalize'   => 1
        );

        //add 20 EUR if basket value is between 200 and 600 EUR (what happens at 100 sharp?), once per cart,
        //stopping further rules
        $data['once_per_cart'][0]['rules'][1] = array(
            'oxtitle'      => 'second',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '20',
            'oxdeltype'    => 'p',
            'oxparam'      => '200',
            'oxparamend'   => '600',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 200,
            'oxfinalize'   => 1
        );

        //add 30 EUR if basket value is between 0 and 600 EUR, once per cart,
        //stopping further rules
        $data['once_per_cart'][0]['rules'][2] = array(
            'oxtitle'      => 'third',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '30',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '600',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 300,
            'oxfinalize'   => 1
        );

        //amount of articles to purchase
        $data['once_per_cart'][0]['buyamount'] = 10;

        //what do we expect as result for shippings costs?
        $data['once_per_cart'][0]['expected_delivery_costs'] = 10.0;

        //which rule should fit the basket
        $data['once_per_cart'][0]['fits'] = array('first', 'third');

        $data['once_per_product'][0]                        = $data['once_per_cart'][0];
        $data['once_per_product'][0]['rules'][0]['oxfixed'] = 1;
        $data['once_per_product'][0]['rules'][1]['oxfixed'] = 1;
        $data['once_per_product'][0]['rules'][2]['oxfixed'] = 1;

        return $data;
    }

    /**
     * Test if shop matches the delivery cost rules as expected.
     * Three categories are assigned to each delivery rule in this set,
     * the basket content fits all three. All rules are set to finalize.
     * 'once_per_cart'    => Rule is to be applied once per cart.
     *                       NOTE: test breaks cause article amount is added up for each matching category.
     *                             Testing $blUser && !$blForBasket && _checkDeliveryAmount fails cause
     *                             the delivery amount is wrong (thrice as much in our example).
     * 'once_per_product' => Rule is to be applied once per product.
     *                       As amounts gets piled up for multiple category matches, second rule fits as well
     *                       although it should not.
     *
     * @dataProvider providerDeliveryCostRulesWithCategoryAssigned
     */
    public function testDeliveryCostRulesWithCategoryAssigned($data)
    {
        $testArticleId = $this->insertArticle();
        $user          = $this->insertUser();

        $deliveryIds = array();
        foreach ($data['rules'] as $rule) {
            $deliveryId    = $this->createRule($rule)->getId();
            $deliveryIds[] = $deliveryId;
            foreach ($this->categoryIds as $categoryId) {
                $this->attachObject2Delivery($deliveryId, $categoryId, 'oxcategories');
            }
        }
        $deliverySetId = $this->createDeliverySet($deliveryIds);

        $basket = oxNew('oxBasket');
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $basket->addToBasket($testArticleId, $data['buyamount']);
        $basket->setPayment('oxidinvoice');
        $basket->setBasketUser($user);
        $basket->setShipping($deliverySetId);
        $basket->calculateBasket();

        foreach ($deliveryIds as $deliveryId) {
            $delivery = oxNew('oxDelivery');
            $delivery->load($deliveryId);

            if (in_array($delivery->oxdelivery__oxtitle->value, $data['fits'])) {
                $this->assertTrue($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            } else {
                $this->assertFalse($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            }
        }

        $deliveryList = oxRegistry::get("oxDeliveryList")->getDeliveryList(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue(0 < count($deliveryList));

        $hasDeliveries = oxRegistry::get("oxDeliveryList")->hasDeliveries(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue($hasDeliveries);

        $deliveryCost = $basket->getDeliveryCost()->getPrice();
        $this->assertSame($data['expected_delivery_costs'], $deliveryCost);
    }

    /**
     * Data provider for testQuantityCostRulesWithCategoryAssigned.
     */
    public function providerQuantityCostRulesWithCategoryAssigned()
    {
        $data = array();

        $data['once_per_different_product'][0]['rules'][0] = array(
            'oxtitle'      => 'first',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '4.90',
            'oxdeltype'    => 'a',
            'oxparam'      => '1',
            'oxparamend'   => '3',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT,
            'oxsort'       => 100,
            'oxfinalize'   => 1
        );

        $data['once_per_different_product'][0]['rules'][1] = array(
            'oxtitle'      => 'second',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '5.90',
            'oxdeltype'    => 'a',
            'oxparam'      => '4',
            'oxparamend'   => '11',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT,
            'oxsort'       => 200,
            'oxfinalize'   => 1
        );

        $data['once_per_different_product'][0]['rules'][2] = array(
            'oxtitle'      => 'third',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '0.0',
            'oxdeltype'    => 'a',
            'oxparam'      => '12',
            'oxparamend'   => '9999',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT,
            'oxsort'       => 300,
            'oxfinalize'   => 1
        );

        $data['once_per_different_product'][0]['rules'][3] = array(
            'oxtitle'      => 'fourth',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '2.90',
            'oxdeltype'    => 'a',
            'oxparam'      => '0',
            'oxparamend'   => '9999',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT,
            'oxsort'       => 400,
            'oxfinalize'   => 1
        );

        //what do we expect as result for shippings costs?
        $data['once_per_different_product'][0]['expected_costs'] = 0.0;

        //assign categories to delivery rules
        $data['once_per_different_product'][0]['assign_cat'] = array(
            'first'  => array('943927cd5d60751015b567794d3239bb', '94342f1d6f3b6fe9f1520d871f566511'),
            'second' => array('943927cd5d60751015b567794d3239bb', '94342f1d6f3b6fe9f1520d871f566511'),
            'third'  => array('943927cd5d60751015b567794d3239bb', '94342f1d6f3b6fe9f1520d871f566511'),
            'fourth' => array('943202124f58e02e84bb228a9a2a9f1e')
        );

        //which rules should fit the basket
        $data['once_per_different_product'][0]['rules_fit'] = array('third', 'fourth');

        //buy amount of articles
        $data['once_per_different_product'][0]['buyamount'] = array(12, 0, 1);

        //same dataset, but now once per cart
        $data['once_per_cart']                           = $data['once_per_different_product'];
        $data['once_per_cart'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart'][0]['expected_costs']      = 0.0;
        $data['once_per_cart'][0]['buyamount']           = array(12, 0, 1);

        //same dataset, but now once per different product
        $data['once_per_product']                           = $data['once_per_different_product'];
        $data['once_per_product'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product'][0]['expected_costs']      = 0.0;
        $data['once_per_product'][0]['buyamount']           = array(12, 0, 1);

        //only third article
        $data['once_per_product_goody_only']                      = $data['once_per_different_product'];
        $data['once_per_product_goody_only'][0]['expected_costs'] = 2.9;
        $data['once_per_product_goody_only'][0]['rules_fit']      = array('fourth');
        $data['once_per_product_goody_only'][0]['buyamount']      = array(0, 0, 1);
        ;

        //similar dataset, once per cart with three different products
        $data['once_per_cart_three_products']                           = $data['once_per_different_product'];
        $data['once_per_cart_three_products'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart_three_products'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart_three_products'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart_three_products'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_ONCE_PER_CART;
        $data['once_per_cart_three_products'][0]['expected_costs']      = 4.9;
        $data['once_per_cart_three_products'][0]['rules_fit']           = array('first', 'fourth');
        $data['once_per_cart_three_products'][0]['buyamount']           = array(1, 1, 1);

        //similar dataset, once per product with three different products
        $data['once_per_product_three_products']                           = $data['once_per_different_product'];
        $data['once_per_product_three_products'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product_three_products'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product_three_products'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product_three_products'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_PRODUCT;
        $data['once_per_product_three_products'][0]['expected_costs']      = 3 * 4.9;
        $data['once_per_product_three_products'][0]['rules_fit']           = array('first', 'fourth');
        $data['once_per_product_three_products'][0]['buyamount']           = array(2, 1, 1);

        //similar dataset, once per product with three different products
        $data['once_per_different_product_three_products']                           = $data['once_per_different_product'];
        $data['once_per_different_product_three_products'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products'][0]['expected_costs']      = 2 * 4.9;
        $data['once_per_different_product_three_products'][0]['rules_fit']           = array('first', 'fourth');
        $data['once_per_different_product_three_products'][0]['buyamount']           = array(1, 1, 1);

        //similar dataset, once per different product with three different products
        $data['once_per_different_product_three_products_rules']                           = $data['once_per_different_product'];
        $data['once_per_different_product_three_products_rules'][0]['rules'][0]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products_rules'][0]['rules'][1]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products_rules'][0]['rules'][2]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products_rules'][0]['rules'][3]['oxfixed'] = oxDelivery::CALCULATION_RULE_FOR_EACH_DIFFERENT_PRODUCT;
        $data['once_per_different_product_three_products_rules'][0]['expected_costs']      = 4.9; //first matching rule is used
        $data['once_per_different_product_three_products_rules'][0]['rules_fit']           = array(
            'first',
            'second',
            'fourth'
        );
        $data['once_per_different_product_three_products_rules'][0]['buyamount']           = array(1, 4, 1);

        return $data;
    }

    /**
     * Test if shop matches the delivery cost rules as expected.
     * Testing issues according to https://bugs.oxid-esales.com/view.php?id=4730
     *
     * @dataProvider providerQuantityCostRulesWithCategoryAssigned
     */
    public function testQuantityDeliveryCostRulesWithCategoryAssigned($data)
    {
        $firstArticleId  = $this->insertArticle('UNIT-666', array('943927cd5d60751015b567794d3239bb'));
        $secondArticleId = $this->insertArticle('UNIT-777', array('94342f1d6f3b6fe9f1520d871f566511'));
        $thirdArticleId  = $this->insertArticle('UNIT-888', array('943202124f58e02e84bb228a9a2a9f1e')); //goody
        $user            = $this->insertUser();

        $deliveryIds = array();
        foreach ($data['rules'] as $rule) {
            $deliveryId    = $this->createRule($rule)->getId();
            $deliveryIds[] = $deliveryId;
            if (isset($data['assign_cat'][$rule['oxtitle']])) {
                foreach ($data['assign_cat'][$rule['oxtitle']] as $categoryId) {
                    $this->attachObject2Delivery($deliveryId, $categoryId, 'oxcategories');
                }
            }
        }
        $deliverySetId = $this->createDeliverySet($deliveryIds);

        $basket = oxNew('oxBasket');
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $basket->addToBasket($firstArticleId, $data['buyamount'][0]);
        $basket->addToBasket($secondArticleId, $data['buyamount'][1]);
        $basket->addToBasket($thirdArticleId, $data['buyamount'][2]);
        $basket->setPayment('oxidinvoice');
        $basket->setBasketUser($user);
        $basket->setShipping($deliverySetId);
        $basket->calculateBasket();

        foreach ($deliveryIds as $deliveryId) {
            $delivery = oxNew('oxDelivery');
            $delivery->load($deliveryId);

            if (in_array($delivery->oxdelivery__oxtitle->value, $data['rules_fit'])) {
                $this->assertTrue($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            } else {
                $this->assertFalse($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            }
        }

        $deliveryList = oxRegistry::get("oxDeliveryList")->getDeliveryList(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue(0 < count($deliveryList));

        $hasDeliveries = oxRegistry::get("oxDeliveryList")->hasDeliveries(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue($hasDeliveries);

        $deliveryCost = $basket->getDeliveryCost()->getPrice();
        $this->assertSame($data['expected_costs'], $deliveryCost);
    }

    /**
     * Data provider for testDeliveryCostRulesWithCategoryAssigned.
     */
    public function providerDeliveryCostRulesWithArticleAssigned()
    {
        $data = array();

        //add 10 EUR if basket value is between 0 and 100 EUR, once per cart, stopping further rules
        $data['once_per_cart'][0]['rules'][0] = array(
            'oxtitle'      => 'first',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '10',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '100',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 100,
            'oxfinalize'   => 1
        );

        //add 20 EUR if basket value is between 200 and 600 EUR (what happens at 100 sharp?), once per cart,
        //stopping further rules
        $data['once_per_cart'][0]['rules'][1] = array(
            'oxtitle'      => 'second',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '20',
            'oxdeltype'    => 'p',
            'oxparam'      => '200',
            'oxparamend'   => '600',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 200,
            'oxfinalize'   => 1
        );

        //add 30 EUR if basket value is between 0 and 600 EUR, once per cart,
        //stopping further rules
        $data['once_per_cart'][0]['rules'][2] = array(
            'oxtitle'      => 'third',
            'oxactive'     => 1,
            'oxactivefrom' => '0000-00-00 00:00:00',
            'oxactiveto'   => '0000-00-00 00:00:00',
            'oxaddsumtype' => 'abs',
            'oxaddsum'     => '30',
            'oxdeltype'    => 'p',
            'oxparam'      => '0',
            'oxparamend'   => '600',
            'oxfixed'      => oxDelivery::CALCULATION_RULE_ONCE_PER_CART,
            'oxsort'       => 300,
            'oxfinalize'   => 1
        );

        //amount of articles to purchase
        $data['once_per_cart'][0]['buyamount'] = 10;

        //what do we expect as result for shippings costs?
        $data['once_per_cart'][0]['expected_delivery_costs'] = 10.0;

        //which rule should fit the basket
        $data['once_per_cart'][0]['fits'] = array('first', 'third');

        $data['once_per_product'][0]                        = $data['once_per_cart'][0];
        $data['once_per_product'][0]['rules'][0]['oxfixed'] = 1;
        $data['once_per_product'][0]['rules'][1]['oxfixed'] = 1;
        $data['once_per_product'][0]['rules'][2]['oxfixed'] = 1;

        return $data;
    }

    /**
     * Test if shop matches the delivery cost rules as expected.
     * Three categories are assigned to each delivery rule in this set,
     * the basket content fits all three. All rules are set to finalize.
     * 'once_per_cart'    => Rule is to be applied once per cart.
     *                       NOTE: test breaks cause article amount is added up for each matching category.
     *                             Testing $blUser && !$blForBasket && _checkDeliveryAmount fails cause
     *                             the delivery amount is wrong (thrice as much in our example).
     * 'once_per_product' => Rule is to be applied once per product.
     *                       As amounts gets piled up for multiple category matches, second rule fits as well
     *                       although it should not.
     *
     * @dataProvider providerDeliveryCostRulesWithArticleAssigned
     */
    public function testDeliveryCostRulesWithArticleAssigned($data)
    {
        $testArticleId = $this->insertArticle();
        $user          = $this->insertUser();

        $deliveryIds = array();
        foreach ($data['rules'] as $rule) {
            $deliveryId    = $this->createRule($rule)->getId();
            $deliveryIds[] = $deliveryId;
            $this->attachObject2Delivery($deliveryId, $testArticleId, 'oxarticles');
        }
        $deliverySetId = $this->createDeliverySet($deliveryIds);

        $basket = oxNew('oxBasket');
        $this->assertEquals(0, $basket->getBasketSummary()->iArticleCount);

        $basket->addToBasket($testArticleId, $data['buyamount']);
        $basket->setPayment('oxidinvoice');
        $basket->setBasketUser($user);
        $basket->setShipping($deliverySetId);
        $basket->calculateBasket();

        foreach ($deliveryIds as $deliveryId) {
            $delivery = oxNew('oxDelivery');
            $delivery->load($deliveryId);

            if (in_array($delivery->oxdelivery__oxtitle->value, $data['fits'])) {
                $this->assertTrue($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            } else {
                $this->assertFalse($delivery->isForBasket($basket), $delivery->oxdelivery__oxtitle->value);
            }
        }

        $deliveryList = oxRegistry::get("oxDeliveryList")->getDeliveryList(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue(0 < count($deliveryList));

        $hasDeliveries = oxRegistry::get("oxDeliveryList")->hasDeliveries(
            $basket,
            $user,
            $user->getActiveCountry(),
            $deliverySetId
        );
        $this->assertTrue($hasDeliveries);

        $deliveryCost = $basket->getDeliveryCost()->getPrice();
        $this->assertSame($data['expected_delivery_costs'], $deliveryCost);
    }



    /**
     * Make a copy of article and variant for testing.
     *
     * @return oxArticle
     */
    private function insertArticle($oxArtNum = '666-T-V', $categories = null)
    {
        $testArticleId       = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $testArticleParentId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        //copy from original article parent and variant
        $articleParent = oxNew('oxarticle');
        $articleParent->disableLazyLoading();
        $articleParent->load(self::SOURCE_ARTICLE_PARENT_ID);
        $articleParent->setId($testArticleParentId);
        $articleParent->oxarticles__oxartnum = new oxField($oxArtNum . '-P', oxField::T_RAW);
        $articleParent->save();

        $article = oxNew('oxarticle');
        $article->disableLazyLoading();
        $article->load(self::SOURCE_ARTICLE_ID);
        $article->setId($testArticleId);
        $article->oxarticles__oxparentid = new oxField($testArticleParentId, oxField::T_RAW);
        $article->oxarticles__oxprice    = new oxField(self::TEST_ARTICLE_PRICE, oxField::T_RAW);
        $article->oxarticles__oxartnum   = new oxField($oxArtNum, oxField::T_RAW);
        $article->oxarticles__oxactive   = new oxField('1', oxField::T_RAW);
        $article->oxarticles__oxstock    = new oxField('1000', oxField::T_RAW);
        $article->save();

        //attach article to category in oxobject2category
        $categories = is_null($categories) ? $categories = $this->categoryIds : $categories;
        foreach ($categories as $categoryId) {
            $this->attachArticle2Category($testArticleId, $categoryId);
            $this->attachArticle2Category($testArticleParentId, $categoryId);
            $this->assertTrue($article->isAssignedToCategory($categoryId));
            $article->getCategoryIds(false, true); //forces loading of categories
            $this->assertTrue($article->inCategory($categoryId));
        }

        return $article->getId();
    }

    /**
     * insert test user
     *
     * @return oxUser;
     */
    private function insertUser()
    {
        $this->testUserId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);

        $user = oxNew('oxUser');
        $user->setId($this->testUserId);

        $user->oxuser__oxactive    = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxrights    = new oxField('user', oxField::T_RAW);
        $user->oxuser__oxshopid    = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxusername  = new oxField('testuser@oxideshop.dev', oxField::T_RAW);
        $user->oxuser__oxpassword  = new oxField(
            'c630e7f6dd47f9ad60ece4492468149bfed3da3429940181464baae99941d0ffa5562' .
                                                 'aaecd01eab71c4d886e5467c5fc4dd24a45819e125501f030f61b624d7d',
            oxField::T_RAW
        ); //password is asdfasdf
        $user->oxuser__oxpasssalt  = new oxField('3ddda7c412dbd57325210968cd31ba86', oxField::T_RAW);
        $user->oxuser__oxcustnr    = new oxField('666', oxField::T_RAW);
        $user->oxuser__oxfname     = new oxField('Bla', oxField::T_RAW);
        $user->oxuser__oxlname     = new oxField('Foo', oxField::T_RAW);
        $user->oxuser__oxstreet    = new oxField('blafoostreet', oxField::T_RAW);
        $user->oxuser__oxstreetnr  = new oxField('123', oxField::T_RAW);
        $user->oxuser__oxcity      = new oxField('Hamburg', oxField::T_RAW);
        $user->oxuser__oxcountryid = new oxField('a7c40f631fc920687.20179984', oxField::T_RAW);
        $user->oxuser__oxzip       = new oxField('22769', oxField::T_RAW);
        $user->oxuser__oxsal       = new oxField('MR', oxField::T_RAW);
        $user->oxuser__oxactive    = new oxField('1', oxField::T_RAW);
        $user->oxuser__oxboni      = new oxField('1000', oxField::T_RAW);
        $user->oxuser__oxcreate    = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxregister  = new oxField('2015-05-20 22:10:51', oxField::T_RAW);
        $user->oxuser__oxboni      = new oxField('1000', oxField::T_RAW);

        $user->save();

        $newId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $oDb   = oxDb::getDb();
        $sQ    = 'insert into `oxobject2delivery` (oxid, oxdeliveryid, oxobjectid, oxtype ) ' .
                 " values ('$newId', 'oxidstandard', '" . $this->testUserId . "', 'oxdelsetu')";
        $oDb->execute($sQ);

        $user->addToGroup('oxidnewcustomer');

        return $user;
    }

    /**
     * Create a shipping cost rule.
     *
     * @param $data
     *
     * @return oxDelivery
     */
    private function createRule($data)
    {
        $deliveryId                    = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $this->deliveries[$deliveryId] = $data['oxtitle'];
        $delivery                      = oxNew('oxDelivery');
        $delivery->setId($deliveryId);

        foreach ($data as $oxcolumn => $value) {
            $oxkey            = 'oxdelivery__' . $oxcolumn;
            $delivery->$oxkey = new oxField($value, oxField::T_RAW);
        }
        $delivery->save();

        return $delivery;
    }

    /**
     * Create a delivery set for country germany and payment method oxidinvoice.
     *
     * @param array  $ruleIds
     * @param string $title
     *
     * @return string oxid of created set.
     */
    private function createDeliverySet($ruleIds, $title = 'shippingCostRulesTest')
    {
        $deliverySetId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $deliveryset   = oxNew('oxDeliverySet');
        $deliveryset->setId($deliverySetId);
        $deliveryset->oxdeliveryset__oxtitle  = new oxField($title, oxField::T_RAW);
        $deliveryset->oxdeliveryset__oxactive = new oxField('1', oxField::T_RAW);
        $deliveryset->save();

        $this->attachDeliveryset2Payment($deliverySetId);
        $countryId = oxNew('oxCountry')->getIdByCode('DE');
        $this->attachObject2Delivery($deliverySetId, $countryId, 'oxdelset');

        foreach ($ruleIds as $id) {
            $this->attachDelivery2DeliverySet($deliverySetId, $id);
        }

        return $deliverySetId;
    }

    /**
     *  Store deliveryset to e.g. country, user, delivery rule relation.
     */
    private function attachDelivery2DeliverySet($deliverySetId, $deliveryId)
    {
        $delivery2deliverySetId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $delivery2deliverySet   = oxNew('oxBase');
        $delivery2deliverySet->init('oxdel2delset');

        $delivery2deliverySet->setId($delivery2deliverySetId);
        $delivery2deliverySet->oxdel2delset__oxdelid    = new oxField($deliveryId, oxField::T_RAW);
        $delivery2deliverySet->oxdel2delset__oxdelsetid = new oxField($deliverySetId, oxField::T_RAW);
        $delivery2deliverySet->save();
    }

    /**
     * Store deliveryset to e.g. country, user, delivery rule relation.
     *
     * @param string $deliveryId
     * @param string $objectId
     * @param string $type
     */
    private function attachObject2Delivery($deliveryId, $objectId, $type)
    {
        $object2DeliveryId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $object2Delivery   = oxNew('oxBase');
        $object2Delivery->init('oxobject2delivery');

        $object2Delivery->setId($object2DeliveryId);
        $object2Delivery->oxobject2delivery__oxdeliveryid = new oxField($deliveryId, oxField::T_RAW);
        $object2Delivery->oxobject2delivery__oxobjectid   = new oxField($objectId, oxField::T_RAW);
        $object2Delivery->oxobject2delivery__oxtype       = new oxField($type, oxField::T_RAW);
        $object2Delivery->save();
    }

    /**
     *  Store deliveryset to payment relation.
     */
    private function attachDeliveryset2Payment($deliverySetId)
    {
        $object2PaymenId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $object2Payment  = oxNew('oxBase');
        $object2Payment->init('oxobject2payment');

        $object2Payment->setId($object2PaymenId);
        $object2Payment->oxobject2payment__oxpaymentid = new oxField('oxidinvoice', oxField::T_RAW);
        $object2Payment->oxobject2payment__oxobjectid  = new oxField($deliverySetId, oxField::T_RAW);
        $object2Payment->oxobject2payment__oxtype      = new oxField('oxdelset', oxField::T_RAW);
        $object2Payment->save();
    }

    /**
     *  Store deliveryset to payment relation.
     */
    private function attachArticle2Category($articleId, $categoryId)
    {
        $object2CategoryId = substr_replace(oxRegistry::getUtilsObject()->generateUId(), '_', 0, 1);
        $object2Category   = oxNew('oxObject2Category');

        $object2Category->setId($object2CategoryId);
        $object2Category->oxobject2category__oxobjectid = new oxField($articleId, oxField::T_RAW);
        $object2Category->oxobject2category__oxcatnid   = new oxField($categoryId, oxField::T_RAW);
        $object2Category->save();
    }
}
