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

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
* Tests for oxwArticleBox class
*/
class Unit_Components_Widgets_oxwArticleDetailsTest extends OxidTestCase
{
    /**
     * Test get active zoom picture.
     *
     * @return null
     */
    public function testGetActZoomPic()
    {
        $oDetails = new oxwArticleDetails();
        $this->assertEquals(1, $oDetails->getActZoomPic());
    }

    /**
     * Test getDefaultSorting when default sorting is not set
     *
     * @return null
     */
    public function testGetDefaultSortingUndefinedSorting()
    {
        $oController = new oxwArticleDetails();

        $oCategory = $this->getMock('oxCategory', array( 'getDefaultSorting' ));
        $oCategory->expects( $this->any() )->method( 'getDefaultSorting' )->will( $this->returnValue( '' ) );
        $oController->setActiveCategory( $oCategory );

        $this->assertEquals( null, $oController->getDefaultSorting() );
    }

    /**
     * Test getDefaultSorting when default sorting is set
     *
     * @return null
     */
    public function testGetDefaultSortingDefinedSorting()
    {
        $oController = new oxwArticleDetails();

        $oCategory = $this->getMock('oxCategory', array( 'getDefaultSorting' ));
        $oCategory->expects( $this->any() )->method( 'getDefaultSorting' )->will( $this->returnValue( 'testsort' ) );
        $oController->setActiveCategory( $oCategory );

        $sArticleTable = getViewName( 'oxarticles' );
        $this->assertEquals( array( 'sortby' => $sArticleTable.'.'.'testsort', 'sortdir' => "asc" ), $oController->getDefaultSorting() );
    }

    /**
     * Test getDefaultSorting when sorting mode is undefined
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsUndefined()
    {
        $oController = new oxwArticleDetails();

        $oCategory = $this->getMock('oxCategory', array( 'getDefaultSorting', 'getDefaultSortingMode' ));
        $oCategory->expects( $this->any() )->method( 'getDefaultSorting' )->will( $this->returnValue( 'testsort' ) );
        $oCategory->expects( $this->any() )->method( 'getDefaultSortingMode' )->will( $this->returnValue( null ) );
        $oController->setActiveCategory( $oCategory );

        $sArticleTable = getViewName( 'oxarticles' );
        $this->assertEquals( array( 'sortby' => $sArticleTable.'.'.'testsort', 'sortdir' => "asc" ), $oController->getDefaultSorting() );
    }

    /**
     * Test getDefaultSorting when sorting mode is set to 'asc'
     * This might be a little too much, but it's a case
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsAsc()
    {
        $oController = new oxwArticleDetails();

        $oCategory = $this->getMock('oxCategory', array( 'getDefaultSorting', 'getDefaultSortingMode' ));
        $oCategory->expects( $this->any() )->method( 'getDefaultSorting' )->will( $this->returnValue( 'testsort' ) );
        $oCategory->expects( $this->any() )->method( 'getDefaultSortingMode' )->will( $this->returnValue( false ) );

        $oController->setActiveCategory( $oCategory );

        $sArticleTable = getViewName( 'oxarticles' );
        $this->assertEquals( array( 'sortby' => $sArticleTable.'.'.'testsort', 'sortdir' => "asc" ), $oController->getDefaultSorting() );
    }
    /**
     * Test getDefaultSorting when sorting mode is set to 'desc'
     *
     * @return null
     */
    public function testDefaultSortingWhenSortingModeIsDesc()
    {
        $oController = new oxwArticleDetails();

        $oCategory = $this->getMock('oxCategory', array( 'getDefaultSorting', 'getDefaultSortingMode' ));
        $oCategory->expects( $this->any() )->method( 'getDefaultSorting' )->will( $this->returnValue( 'testsort' ) );
        $oCategory->expects( $this->any() )->method( 'getDefaultSortingMode' )->will( $this->returnValue( true ) );

        $oController->setActiveCategory( $oCategory );

        $sArticleTable = getViewName( 'oxarticles' );
        $this->assertEquals( array( 'sortby' => $sArticleTable.'.'.'testsort', 'sortdir' => "desc" ), $oController->getDefaultSorting() );
    }

}