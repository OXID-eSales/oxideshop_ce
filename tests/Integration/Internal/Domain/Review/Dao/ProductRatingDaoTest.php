<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Dao;

use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridge;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDao;
use OxidEsales\EshopCommunity\Internal\Domain\Review\DataObject\ProductRating;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Service\ProductRatingService;

final class ProductRatingDaoTest extends TestCase
{
    public function testUpdateProductRating(): void
    {
        $this->createTestProduct();

        $productRating = new ProductRating();
        $productRating
            ->setProductId('testProduct')
            ->setRatingCount(66)
            ->setRatingAverage(3.7);

        $productRatingDao = $this->getProductRatingDao();
        $productRatingDao->update($productRating);

        $this->assertEquals(
            $productRating,
            $productRatingDao->getProductRatingById('testProduct')
        );
    }

    private function createTestProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId('testProduct');
        $product->save();
    }

    /**
     * Accessing the dao is difficult, because it is a private service.
     * In newer versions of the Symfony Container (since 4.1) this may be
     * done more elegant.
     *
     * @return ProductRatingDao
     */
    private function getProductRatingDao()
    {
        $bridge = ContainerFacade::get(ProductRatingBridgeInterface::class);
        $serviceProperty = new ReflectionProperty(ProductRatingBridge::class, 'productRatingService');
        $serviceProperty->setAccessible(true);
        $service = $serviceProperty->getValue($bridge);
        $daoProperty = new ReflectionProperty(ProductRatingService::class, 'productRatingDao');
        $daoProperty->setAccessible(true);

        return $daoProperty->getValue($service);
    }
}
