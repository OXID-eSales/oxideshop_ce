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
        $oRecomm->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
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
        $oUser->expects($this->once())->method('getUserRecommLists')->will($this->returnValue('testRecommList'));

        $oRecomm = oxNew('RecommAdd');
        $oRecomm->setUser($oUser);
        $this->assertEquals('testRecommList', $oRecomm->getRecommLists('test'));
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
        $oView->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals('title select', $oView->getTitle());
    }
}
