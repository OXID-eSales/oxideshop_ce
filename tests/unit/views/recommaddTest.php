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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Views_recommaddTest extends OxidTestCase
{
    /**
     * In case product uses alternative template, adding to listmania is impossible (#0001444)
     *
     * @return null
     */
    public function testForUseCase()
    {
        oxTestModules::addFunction('oxUtilsServer', 'getServerVar', '{ if ( $aA[0] == "HTTP_HOST") { return "shop.com/"; } else { return "test.php";} }');

        $oProduct = new oxArticle();
        $oProduct->load( "1126" );
        $oProduct->oxarticles__oxtemplate->value = 'details_persparam.tpl';

        $oRecomm = $this->getMock( "recommadd", array( "getProduct" ) );
        $oRecomm->expects( $this->any() )->method( 'getProduct')->will( $this->returnValue( $oProduct ) );
        $oRecomm->init();

        $oBlankRecomm = new RecommAdd();
        $this->assertEquals( $oBlankRecomm->getTemplateName(), $oRecomm->render() );
    }

    /**
     * Getting view values
     */
    public function testGetRecommLists()
    {
        $oUser = $this->getMock( 'oxuser', array( 'getUserRecommLists' ) );
        $oUser->expects( $this->once() )->method( 'getUserRecommLists')->will( $this->returnValue( 'testRecommList' ) );

        $oRecomm = new recommadd();
        $oRecomm->setUser( $oUser );
        $aLists = $oRecomm->getRecommLists( 'test');
        $this->assertEquals( 'testRecommList', $oRecomm->getRecommLists( 'test') );
    }

}
