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
class RecommendationTest extends \OxidTestCase
{

    /**
     * Testing oxwRecomm::getSimilarRecommLists()
     *
     * @return null
     */
    public function testGetSimilarRecommLists_empty()
    {
        $aParams["aArticleIds"] = array();
        $oRecomm = oxNew('oxwRecommendation');
        $oRecomm->setViewParameters($aParams);
        $oRecommList = $oRecomm->getSimilarRecommLists();
        $this->assertTrue(!isset($oRecommList), "Should be empty if no articles id given");
    }

    /**
     * Testing oxwRecomm::getSimilarRecommLists()
     *
     * @return null
     */
    public function testGetSimilarRecommLists()
    {
        $oRecommList = $this->getMock(\OxidEsales\Eshop\Application\Model\RecommendationList::class, array("getRecommListsByIds"));
        $oRecommList->expects($this->once())->method("getRecommListsByIds")->with($this->equalTo(array("articleId")))->will($this->returnValue("oxRecommListMock"));
        oxTestModules::addModuleObject('oxrecommlist', $oRecommList);

        $aParams["aArticleIds"] = array("articleId");

        $oRecomm = oxNew('oxwRecommendation');
        $oRecomm->setViewParameters($aParams);

        $this->assertEquals("oxRecommListMock", $oRecomm->getSimilarRecommLists(), "Should try to create RecommList object.");
    }

    /**
     * Testing oxwRecommendation::getRecommList()
     *
     * @return null
     */
    public function testGetRecommList()
    {
        $oRecommList = oxNew('oxwRecommendation');
        $this->assertTrue($oRecommList->getRecommList() instanceof \OxidEsales\EshopCommunity\Application\Controller\RecommListController);
    }
}
