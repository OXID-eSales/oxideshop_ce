<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwReview class
 */
class ReviewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Testing oxwReview::getReviewType()
     */
    public function testGetReviewTypeLowerCase()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['type' => 'testreviewtype']);
        $this->assertSame('testreviewtype', $oReviewWidget->getReviewType());
    }

    /**
     * Testing oxwReview::getReviewType()
     */
    public function testGetReviewTypeUpperCase()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['type' => 'TESTREVIEWTYPE']);
        $this->assertSame('testreviewtype', $oReviewWidget->getReviewType());
    }

    /**
     * Testing oxwReview::getArticleId()
     */
    public function testGetArticleId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['aid' => 'testaid']);
        $this->assertSame('testaid', $oReviewWidget->getArticleId());
    }

    /**
     * Testing oxwReview::getArticleId()
     */
    public function testGetArticleNId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['anid' => 'testanid']);
        $this->assertSame('testanid', $oReviewWidget->getArticleNId());
    }

    /**
     * Testing oxwReview::getRecommListId()
     */
    public function testGetRecommListId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['recommid' => 'testrecommid']);
        $this->assertSame('testrecommid', $oReviewWidget->getRecommListId());
    }

    /**
     * Testing oxwReview::canRate()
     */
    public function testCanRate()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['canrate' => 'testcanrate']);
        $this->assertSame('testcanrate', $oReviewWidget->canRate());
    }

    /**
     * Testing oxwReview::getReviewUserHash()
     */
    public function testGetReviewUserHash()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(['reviewuserhash' => 'testreviewuserhash']);
        $this->assertSame('testreviewuserhash', $oReviewWidget->getReviewUserHash());
    }
}
