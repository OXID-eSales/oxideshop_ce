<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Application\Model\Rating;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\TestingLibrary\UnitTestCase;

class RatingTest extends UnitTestCase
{
    public function testUpdateProductRatingOnRatingDelete()
    {
        $this->createTestProduct();
        $this->createTestRatings();

        $rating = oxNew(Rating::class);
        $rating->load('id3');
        $rating->delete();

        $product = oxNew(Article::class);
        $product->load('testId');

        $this->assertEquals(
            2,
            $product->oxarticles__oxratingcnt->value
        );

        $this->assertEquals(
            1.5,
            $product->oxarticles__oxrating->value
        );
    }

    public function testUpdateProductRatingOnRatingDeleteWhenAllRatingsForProductAreDeleted()
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

        $this->assertEquals(
            0,
            $product->oxarticles__oxratingcnt->value
        );

        $this->assertEquals(
            0,
            $product->oxarticles__oxrating->value
        );
    }

    private function createTestProduct()
    {
        $product = oxNew(Article::class);
        $product->setId('testId');
        $product->oxarticles__oxrating = new Field(2);
        $product->oxarticles__oxratingcnt = new Field(3);
        $product->save();
    }

    private function createTestRatings()
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
