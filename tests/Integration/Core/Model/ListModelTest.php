<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Core\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ListModelTest extends IntegrationTestCase
{
    private int $productMaxCount = 100;
    private int $variantMaxCount = 10;

    public function testListModuleHighload(): void
    {
        $this->createProducts();

        $timeStart = microtime(true);
        $memoryStart = memory_get_usage();
        $oArticles = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArticles->getList();
        $memoryEnd = memory_get_usage();
        $timeEnd = microtime(true);

        $this->assertLessThan(1, $timeEnd-$timeStart);
        $this->assertLessThan(1000000, $memoryEnd-$memoryStart);
    }

    private function createProducts(): void
    {
        for ($i=0; $i < $this->productMaxCount; $i++)
        {
            /** @var \OxidEsales\Eshop\Application\Model\Article $product */
            $product = oxNew(Article::class);
            $product->save();
            for ($j=0; $j < $this->variantMaxCount; $j++)
            {
                $productVariant = oxNew(Article::class);
                $productVariant->oxarticles__oxparentid = new Field($product->getId());
                $productVariant->save();
            }
        }
    }
}
