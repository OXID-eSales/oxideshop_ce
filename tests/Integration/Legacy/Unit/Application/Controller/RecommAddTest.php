<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;

class RecommAddTest extends \PHPUnit\Framework\TestCase
{

    /**
     * In case product uses alternative template, adding to list mania is impossible (#0001444)
     */
    public function testForUseCase()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");

        $oProduct->oxarticles__oxtemplate->value = 'details_persparam';

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommendationAddController::class, ["getProduct"]);
        $oRecomm->method('getProduct')->willReturn($oProduct);
        $oRecomm->init();

        $oBlankRecomm = oxNew('RecommAdd');
        $this->assertEquals($oBlankRecomm->getTemplateName(), $oRecomm->render());
    }

    /**
     * Getting view values
     */
    public function testGetRecommLists()
    {
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, ['getUserRecommLists']);
        $oUser->expects($this->once())->method('getUserRecommLists')->willReturn('testRecommList');

        $oRecomm = oxNew('RecommAdd');
        $oRecomm->setUser($oUser);
        $this->assertSame('testRecommList', $oRecomm->getRecommLists('test'));
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->oxarticles__oxtitle = new oxField('title');
        $oProduct->oxarticles__oxvarselect = new oxField('select');

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommendationAddController::class, ['getProduct']);
        $oView->method('getProduct')->willReturn($oProduct);

        $this->assertSame('title select', $oView->getTitle());
    }
}
