<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Review\Bridge;

use OxidEsales\EshopCommunity\Internal\Domain\Review\Bridge\ProductRatingBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Domain\Review\Dao\ProductRatingDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Webmozart\PathUtil\Path;

class ProductRatingBridgeTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures([Path::join(__DIR__, 'Fixtures', 'rating_bridge_fixtures.yaml')]);
    }

    public function testUpdateProductRating()
    {

        $productRatingBridge = $this->getProductRatingBridge();
        $productRatingBridge->updateProductRating('testProduct');

        $productRatingDao = $this->getProductRatingDao();
        $productRating = $productRatingDao->getProductRatingById('testProduct');

        $this->assertEquals(
            4,
            $productRating->getRatingAverage()
        );

        $this->assertEquals(
            3,
            $productRating->getRatingCount()
        );
    }

    private function getProductRatingBridge()
    {
        return $this->get(ProductRatingBridgeInterface::class);
    }

    private function getProductRatingDao()
    {
        return $this->get(ProductRatingDaoInterface::class);
    }
}
