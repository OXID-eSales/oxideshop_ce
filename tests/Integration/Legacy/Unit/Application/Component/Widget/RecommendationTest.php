<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Component\Widget;

use \oxTestModules;
use RecommList;

/**
 * Tests for oxwRecomm class
 */
class RecommendationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing oxwRecomm::getSimilarRecommLists()
     */
    public function testGetSimilarRecommLists_empty()
    {
        $aParams["aArticleIds"] = [];
        $oRecomm = oxNew('oxwRecommendation');
        $oRecomm->setViewParameters($aParams);

        $oRecommList = $oRecomm->getSimilarRecommLists();
        $this->assertFalse(isset($oRecommList), "Should be empty if no articles id given");
    }

    /**
     * Testing oxwRecomm::getSimilarRecommLists()
     */
    public function testGetSimilarRecommLists()
    {
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, ["getRecommListsByIds"]);
        $oRecommList->expects($this->once())->method("getRecommListsByIds")->with(["articleId"])->willReturn("oxRecommListMock");
        oxTestModules::addModuleObject('oxrecommlist', $oRecommList);

        $aParams["aArticleIds"] = ["articleId"];

        $oRecomm = oxNew('oxwRecommendation');
        $oRecomm->setViewParameters($aParams);

        $this->assertSame("oxRecommListMock", $oRecomm->getSimilarRecommLists(), "Should try to create RecommList object.");
    }

    /**
     * Testing oxwRecommendation::getRecommList()
     */
    public function testGetRecommList()
    {
        $oRecommList = oxNew('oxwRecommendation');
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Application\Controller\RecommListController::class, $oRecommList->getRecommList());
    }
}
