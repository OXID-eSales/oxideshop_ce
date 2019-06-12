<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwCategoryTree class
 */
class RatingTest extends \OxidTestCase
{
    /**
     * Testing oxwRating::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oRating = oxNew('oxwRating');
        $this->assertEquals('widget/reviews/rating.tpl', $oRating->render());
    }

    /**
     * Testing oxwRating::getRatingValue()
     *
     * @return null
     */
    public function testGetRatingValue()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(array("dRatingValue" => 2.59));
        $this->assertEquals(2.6, $oRating->getRatingValue());
    }

    /**
     * Testing oxwRating::getRatingCount()
     *
     * @return null
     */
    public function testGetRatingCount()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(array("dRatingCount" => 6));
        $this->assertEquals(6, $oRating->getRatingCount());
    }

    /**
     * Testing oxwRating::canRate()
     *
     * @return null
     */
    public function testCanRate()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(array("blCanRate" => true));
        $this->assertTrue($oRating->canRate());
    }

    /**
     * Testing oxwRating::getArticleId()
     *
     * @return null
     */
    public function testGetArticleNId()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(array('anid' => 'testanid'));
        $this->assertEquals('testanid', $oRating->getArticleNId());
    }

    /**
     * Testing oxwRating::getRateUrl()
     *
     * @return null
     */
    public function testGetRateUrl_RateUrlParamSet_RateUrlValue()
    {
        $oRating = oxNew('oxwRating');
        $oRating->setViewParameters(array("sRateUrl" => "testUrl"));
        $this->assertEquals('testUrl', $oRating->getRateUrl());
    }

    /**
     * Testing oxwRating::getRateUrl()
     *
     * @return null
     */
    public function testGetRateUrl_NoRateUrlParam_Null()
    {
        $oRating = oxNew('oxwRating');
        $this->assertEquals(null, $oRating->getRateUrl());
    }
}
