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
class Unit_Components_Widgets_oxwArticleBoxTest extends OxidTestCase
{
    /**
     * Template view parameters data provider
     */
    public function _dpTemplateViewParams()
    {
        return array(
            array( "product", "listitem_grid"    , "widget/product/listitem_grid.tpl"     ),
            array( "product", "listitem_infogrid", "widget/product/listitem_infogrid.tpl" ),
            array( "product", "listitem_line"    , "widget/product/listitem_line.tpl"     ),
        );
    }

   /**
    * Test for rendering default template
    */
    public function testRender()
    {
        $oArticleBox = new oxwArticleBox();
        $this->assertEquals( "widget/product/box.tpl", $oArticleBox->render(), "Default template should be loaded" );
    }

    /**
     * Test for getting different templates
     *
     * @dataProvider _dpTemplateViewParams
     */
    public function testRenderDifferentTemplates($sWidgetType, $sListType, $sExpected)
    {
        $oArticleBox = new oxwArticleBox();

        $aViewParams = array(
            "sWidgetType" => $sWidgetType,
            "sListType"   => $sListType,
        );
        $oArticleBox->setViewParameters($aViewParams) ;

        $this->assertEquals( $sExpected, $oArticleBox->render(), "Correct template should be loaded" );
    }
}
