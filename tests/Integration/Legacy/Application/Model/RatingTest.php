<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class RatingTest extends IntegrationTestCase
{
    public function testUpdateProductRatingOnRatingDelete(): void
    {
        $this->createTestProduct();
        $this->createTestRatings();

        $rating = oxNew(Rating::class);
        $rating->load('id3');
        $rating->delete();

        $product = oxNew(Article::class);
        $product->load('testId');

        $this->assertEquals(2, $product->oxarticles__oxratingcnt->value);

        $this->assertEquals(1.5, $product->oxarticles__oxrating->value);
    }

    public function testUpdateProductRatingOnRatingDeleteWhenAllRatingsForProductAreDeleted(): void
    {
        $this->createTestProduct();
        $this->createTestRatings();

        $rating = oxNew(Rating::class);

        $rating->load('id1');
        $rating->delete();

        $rating->load('id2');
        $rating->delete();

        $rating->load('id3');
        $rating->delete();

        $product = oxNew(Article::class);
        $product->load('testId');

        $this->assertEquals(0, $product->oxarticles__oxratingcnt->value);

        $this->assertEquals(0, $product->oxarticles__oxrating->value);
    }

    private function createTestProduct(): void
    {
        $product = oxNew(Article::class);
        $product->setId('testId');
        $product->oxarticles__oxrating = new Field(2);
        $product->oxarticles__oxratingcnt = new Field(3);
        $product->save();
    }

    private function createTestRatings(): void
    {
        $rating = oxNew(Rating::class);
        $rating->setId('id1');
        $rating->oxratings__oxobjectid = new Field('testId');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(1);
        $rating->save();

        $rating = oxNew(Rating::class);
        $rating->setId('id2');
        $rating->oxratings__oxobjectid = new Field('testId');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(2);
        $rating->save();

        $rating = oxNew(Rating::class);
        $rating->setId('id3');
        $rating->oxratings__oxobjectid = new Field('testId');
        $rating->oxratings__oxtype = new Field('oxarticle');
        $rating->oxratings__oxrating = new Field(3);
        $rating->save();
    }
}
