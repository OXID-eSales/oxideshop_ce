<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

require_once realpath( "." ) . '/unit/OxidTestCase.php';
require_once realpath( "." ) . '/unit/test_config.inc.php';

/**
 * Tests for oxwReview class
 */
class Unit_Components_Widgets_oxwreviewTest extends OxidTestCase
{
    /**
     * Testing oxwRating::getReviewType()
     *
     * @return null
     */
    public function testGetReviewTypeLowerCase()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'type' => 'testreviewtype' ) );
        $this->assertEquals( 'testreviewtype', $oRating->getReviewType() );
    }

    /**
     * Testing oxwRating::getReviewType()
     *
     * @return null
     */
    public function testGetReviewTypeUpperCase()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'type' => 'TESTREVIEWTYPE' ) );
        $this->assertEquals( 'testreviewtype', $oRating->getReviewType() );
    }

    /**
     * Testing oxwRating::getArticleId()
     *
     * @return null
     */
    public function testGetArticleId()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'aid' => 'testaid' ) );
        $this->assertEquals( 'testaid', $oRating->getArticleId() );
    }

    /**
     * Testing oxwRating::getArticleId()
     *
     * @return null
     */
    public function testGetArticleNId()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'anid' => 'testanid' ) );
        $this->assertEquals( 'testanid', $oRating->getArticleNId() );
    }

    /**
     * Testing oxwRating::getRecommListId()
     *
     * @return null
     */
    public function testGetRecommListId()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'recommid' => 'testrecommid' ) );
        $this->assertEquals( 'testrecommid', $oRating->getRecommListId() );
    }

    /**
     * Testing oxwRating::canRate()
     *
     * @return null
     */
    public function testCanRate()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'canrate' => 'testcanrate' ) );
        $this->assertEquals( 'testcanrate', $oRating->canRate() );
    }

    /**
     * Testing oxwRating::getReviewUserHash()
     *
     * @return null
     */
    public function testGetReviewUserHash()
    {
        $oRating = new oxwReview();
        $oRating->setViewParameters( array( 'reviewuserhash' => 'testreviewuserhash' ) );
        $this->assertEquals( 'testreviewuserhash', $oRating->getReviewUserHash() );
    }
}