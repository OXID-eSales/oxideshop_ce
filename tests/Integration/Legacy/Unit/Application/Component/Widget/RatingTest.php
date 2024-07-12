<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class RatingTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing oxwRating::render()
     */
    public function testRender()
    {
        $oRating = oxNew('oxwRating');
        $this->assertSame('widget/reviews/rating', $oRating->render());
    }

    /**
     * Testing oxwRating::getRatingValue()
     */
    public function testGetRatingValue()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(["dRatingValue" => 2.59]);
        $this->assertEqualsWithDelta(2.6, $oRating->getRatingValue(), PHP_FLOAT_EPSILON);
    }

    /**
     * Testing oxwRating::getRatingCount()
     */
    public function testGetRatingCount()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(["dRatingCount" => 6]);
        $this->assertSame(6, $oRating->getRatingCount());
    }

    /**
     * Testing oxwRating::canRate()
     */
    public function testCanRate()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(["blCanRate" => true]);
        $this->assertTrue($oRating->canRate());
    }

    /**
     * Testing oxwRating::getArticleId()
     */
    public function testGetArticleNId()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(['anid' => 'testanid']);
        $this->assertSame('testanid', $oRating->getArticleNId());
    }

    /**
     * Testing oxwRating::getRateUrl()
     */
    public function testGetRateUrl_RateUrlParamSet_RateUrlValue()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(["sRateUrl" => "testUrl"]);
        $this->assertSame('testUrl', $oRating->getRateUrl());
    }

    /**
     * Testing oxwRating::getRateUrl()
     */
    public function testGetRateUrl_NoRateUrlParam_Null()
    {
        $oRating = oxNew('oxwRating');
        $this->assertEquals(null, $oRating->getRateUrl());
    }
}
