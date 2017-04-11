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

/**
 * Tests for oxwCategoryTree class
 */
class Unit_Components_Widgets_oxwratingTest extends OxidTestCase
{

    /**
     * Testing oxwRating::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oRating = new oxwRating();
        $this->assertEquals('widget/reviews/rating.tpl', $oRating->render());
    }

    /**
     * Testing oxwRating::getRatingValue()
     *
     * @return null
     */
    public function testGetRatingValue()
    {
        $oRating = new oxwRating();
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
        $oRating = new oxwRating();
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
        $oRating = new oxwRating();
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
        $oRating = new oxwRating();
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
        $oRating = new oxwRating();
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
        $oRating = new oxwRating();
        $this->assertEquals(null, $oRating->getRateUrl());
    }

}