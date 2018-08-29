<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

/**
 * Tests for oxwReview class
 */
class ReviewTest extends \OxidTestCase
{
    /**
     * Testing oxwReview::getReviewType()
     *
     * @return null
     */
    public function testGetReviewTypeLowerCase()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('type' => 'testreviewtype'));
        $this->assertEquals('testreviewtype', $oReviewWidget->getReviewType());
    }

    /**
     * Testing oxwReview::getReviewType()
     *
     * @return null
     */
    public function testGetReviewTypeUpperCase()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('type' => 'TESTREVIEWTYPE'));
        $this->assertEquals('testreviewtype', $oReviewWidget->getReviewType());
    }

    /**
     * Testing oxwReview::getArticleId()
     *
     * @return null
     */
    public function testGetArticleId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('aid' => 'testaid'));
        $this->assertEquals('testaid', $oReviewWidget->getArticleId());
    }

    /**
     * Testing oxwReview::getArticleId()
     *
     * @return null
     */
    public function testGetArticleNId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('anid' => 'testanid'));
        $this->assertEquals('testanid', $oReviewWidget->getArticleNId());
    }

    /**
     * Testing oxwReview::getRecommListId()
     *
     * @return null
     */
    public function testGetRecommListId()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('recommid' => 'testrecommid'));
        $this->assertEquals('testrecommid', $oReviewWidget->getRecommListId());
    }

    /**
     * Testing oxwReview::canRate()
     *
     * @return null
     */
    public function testCanRate()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('canrate' => 'testcanrate'));
        $this->assertEquals('testcanrate', $oReviewWidget->canRate());
    }

    /**
     * Testing oxwReview::getReviewUserHash()
     *
     * @return null
     */
    public function testGetReviewUserHash()
    {
        $oReviewWidget = oxNew('oxwReview');
        $oReviewWidget->setViewParameters(array('reviewuserhash' => 'testreviewuserhash'));
        $this->assertEquals('testreviewuserhash', $oReviewWidget->getReviewUserHash());
    }
}
