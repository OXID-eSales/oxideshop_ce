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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Views_RecommAddTest extends OxidTestCase
{

    /**
     * In case product uses alternative template, adding to list mania is impossible (#0001444)
     */
    public function testForUseCase()
    {
        $oProduct = new oxArticle();
        $oProduct->load("1126");
        $oProduct->oxarticles__oxtemplate->value = 'details_persparam.tpl';

        $oRecomm = $this->getMock("recommadd", array("getProduct"));
        $oRecomm->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));
        $oRecomm->init();

        $oBlankRecomm = new RecommAdd();
        $this->assertEquals($oBlankRecomm->getTemplateName(), $oRecomm->render());
    }

    /**
     * Getting view values
     */
    public function testGetRecommLists()
    {
        $oUser = $this->getMock('oxUser', array('getUserRecommLists'));
        $oUser->expects($this->once())->method('getUserRecommLists')->will($this->returnValue('testRecommList'));

        $oRecomm = new RecommAdd();
        $oRecomm->setUser($oUser);
        $this->assertEquals('testRecommList', $oRecomm->getRecommLists('test'));
    }

    /**
     * Test get title.
     */
    public function testGetTitle()
    {
        $oProduct = new oxArticle();
        $oProduct->oxarticles__oxtitle = new oxField('title');
        $oProduct->oxarticles__oxvarselect = new oxField('select');

        $oView = $this->getMock("RecommAdd", array('getProduct'));
        $oView->expects($this->any())->method('getProduct')->will($this->returnValue($oProduct));

        $this->assertEquals('title select', $oView->getTitle());
    }
}
