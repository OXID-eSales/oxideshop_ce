<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \oxField;

class RecommAddTest extends \OxidTestCase
{

    /**
     * In case product uses alternative template, adding to list mania is impossible (#0001444)
     */
    public function testForUseCase()
    {
        $oProduct = oxNew('oxArticle');
        $oProduct->load("1126");
        $oProduct->oxarticles__oxtemplate->value = 'details_persparam.tpl';

        $oRecomm = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommendationAddController::class, array("getProduct"));
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
        $oUser = $this->getMock(\OxidEsales\Eshop\Application\Model\User::class, array('getUserRecommLists'));
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

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\RecommendationAddController::class, array('getProduct'));
        $oView->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals('title select', $oView->getTitle());
    }
}
