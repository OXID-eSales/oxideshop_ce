<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Model\DeliveryList;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

final class DeliveryCostTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        DatabaseProvider::getDb()
            ->execute(
                'UPDATE oxdelivery SET oxactive = 0'
            );
    }

    public static function providerDeliveryCostRules(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/delivery_cost/rules/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }
        return $testCases;
    }

    #[DataProvider('providerDeliveryCostRules')]
    public function testDeliveryCostRules(array $testCase): void
    {
        $basket = (new BasketConstruct())->calculateBasket($testCase);

        $this->assertEquals(
            $testCase['expected']['costs']['totals']['delivery']['brutto'],
            $basket->getDeliveryCosts(),
        );
    }

    public static function providerDeliveryCostRulesWithRuleAssignment(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/delivery_cost/rules_assigned_to/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }
        return $testCases;
    }

    #[DataProvider('providerDeliveryCostRulesWithRuleAssignment')]
    public function testGetDeliveryCostsWithRuleAssignedWillUseCorrectRuleCost($testCase): void
    {
        $basket = (new BasketConstruct())->calculateBasket($testCase);

        $this->assertEquals(
            $testCase['expected']['costs']['totals']['delivery']['brutto'],
            $basket->getDeliveryCosts(),
        );
    }

    #[DataProvider('providerDeliveryCostRulesWithRuleAssignment')]
    public function testGetListWithRuleAssigned($testCase): void
    {
        (new BasketConstruct())->calculateBasket($testCase);

        $activeDeliveries = oxNew(DeliveryList::class)->getList();
        $this->assertCount(3, $activeDeliveries);
    }

    #[DataProvider('providerDeliveryCostRulesWithRuleAssignment')]
    public function testHasDeliveriesWithRuleAssigned($testCase): void
    {
        $basket = (new BasketConstruct())->calculateBasket($testCase);

        $user = oxNew(User::class);
        $user->load($testCase['user']['oxid']);
        $deliveryList = oxNew(DeliveryList::class);

        $hasDeliveries = $deliveryList->hasDeliveries(
            $basket,
            $user,
            BasketConstruct::getDefaultCountryId(),
            $basket->getShippingId(),
        );

        $this->assertTrue($hasDeliveries);
    }

    #[DataProvider('providerDeliveryCostRulesWithRuleAssignment')]
    public function testGetDeliveryListWithRuleAssignedWillSkipWrongRule($testCase): void
    {
        $basket = (new BasketConstruct())->calculateBasket($testCase);

        $user = oxNew(User::class);
        $user->load($testCase['user']['oxid']);
        $deliveryList = oxNew(DeliveryList::class);

        $suitableDeliveries = $deliveryList->getDeliveryList(
            $basket,
            $user,
            BasketConstruct::getDefaultCountryId(),
            $basket->getShippingId(),
        );

        $this->assertCount(1, $suitableDeliveries);
        $this->assertEquals('ok-rule', reset($suitableDeliveries)->getFieldData('oxtitle'));
    }
}
