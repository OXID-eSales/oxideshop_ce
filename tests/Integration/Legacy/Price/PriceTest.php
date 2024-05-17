<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Price;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Yaml\Yaml;

use function number_format;
use function round;

final class PriceTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->truncateDatabaseData();
    }

    public static function providerPrice(): array
    {
        $testCases = [];
        foreach (glob(__DIR__ . '/testcases/price/*.yaml') as $filePath) {
            $testCases[$filePath] = [Yaml::parseFile($filePath)];
        }
        return $testCases;
    }

    #[DataProvider('providerPrice')]
    public function testPrice(array $testCase): void
    {
        $basket = new BasketConstruct();
        // create user if specified
        $oUser = $basket->createObj($testCase['user'] ?? [], 'oxuser', 'oxuser');
        // create group and assign
        $basket->createGroup($testCase['group'] ?? []);
        // user login
        if ($oUser) {
            $oUser->load($oUser->getId());
            $oUser->login($testCase['user']['oxusername'], '');
        }
        // setup options
        $basket->setOptions($testCase['options']);
        // create categories
        $basket->createCategories($testCase['categories'] ?? []);
        // create articles
        $products = $basket->createProducts($testCase['articles']);
        // apply discounts
        $basket->createDiscounts($testCase['discounts'] ?? []);

        // iteration through expectations
        foreach ($products as $productData) {
            $expected = $testCase['expected'][$productData['id']] ?? null;
            if (empty($expected)) {
                continue;
            }
            $product = oxNew(Article::class);
            $product->load($productData['id']);

            $this->assertEquals(
                $expected['base_price'],
                number_format(round($product->getBasePrice(), 2), 2, ',', '.'),
                "Base Price of product #{$productData['id']}"
            );
            $this->assertEquals($expected['price'], $product->getFPrice(), "Price of product #{$productData['id']}");

            if (isset($expected['rrp_price'])) {
                $this->assertEquals(
                    $expected['rrp_price'],
                    $product->getFTPrice(),
                    "RRP price of product #{$productData['id']}"
                );
            }

            if (isset($expected['unit_price'])) {
                $this->assertEquals(
                    $expected['unit_price'],
                    $product->getFUnitPrice(),
                    "Unit Price of product #{$productData['id']}"
                );
            }

            if (isset($expected['is_range_price'])) {
                $this->assertEquals(
                    $expected['is_range_price'],
                    $product->isRangePrice(),
                    "Is range price check of product #{$productData['id']}"
                );
            }

            if (isset($expected['min_price'])) {
                $this->assertEquals(
                    $expected['min_price'],
                    $product->getFMinPrice(),
                    "Min price of product #{$productData['id']}"
                );
            }

            if (isset($expected['var_min_price'])) {
                $this->assertEquals(
                    $expected['var_min_price'],
                    $product->getFVarMinPrice(),
                    "Var min price of product #{$productData['id']}"
                );
            }

            if (isset($expected['show_rrp'])) {
                $blShowRPP = false;
                if ($product->getTPrice() && $product->getTPrice()->getPrice() > $product->getPrice()->getPrice()) {
                    $blShowRPP = true;
                }
                $this->assertEquals(
                    $expected['show_rrp'],
                    $blShowRPP,
                    "RRP price showing of product #{$productData['id']}"
                );
            }
        }
    }

    private function truncateDatabaseData(): void
    {
        $database = DatabaseProvider::getDb();
        $database->execute('DELETE FROM oxarticles WHERE 1');
        $database->execute('DELETE FROM oxdiscount WHERE 1');
        $database->execute('DELETE FROM oxprice2article WHERE 1');
        $database->execute('DELETE FROM oxobject2discount WHERE 1');
        $database->execute('DELETE FROM oxuser WHERE 1');
        $database->execute('DELETE FROM oxobject2group WHERE 1');
        $database->execute('DELETE FROM oxgroups WHERE 1');
    }
}
