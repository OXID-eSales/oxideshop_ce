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
 * @version   SVN: $Id$
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for oxwAction class
 */
class Unit_Components_Widgets_oxwActionTest extends OxidTestCase
{
    /**
     * Testing oxwAction::render()
     *
     * @return null
     */
    public function testRender()
    {
        $oAction = new oxwAction();
        $this->assertEquals( 'widget/product/action.tpl', $oAction->render() );
    }
    
    /**
     * Testing oxwAction::getAction()
     *
     * @return null
     */
    public function testGetAction()
    {
        $this->getConfig()->setConfigParam( 'bl_perfLoadAktion', 1 );
        
        $oAction = new oxwAction();
        $oAction->setViewParameters( array("action" => "oxtop5") );
        $aList = $oAction->getAction();
        $this->assertTrue($aList instanceof oxarticlelist);
        $this->assertEquals(1, $aList->count());
        $this->assertEquals("1849", $aList->current()->getId());
    }
    
    /**
     * Testing oxwAction::getActionName()
     *
     * @return null
     */
    public function testGetActionName()
    {
        $oAction = new oxwAction();
        $oAction->setViewParameters( array("action" => "Bestseller") );
        $this->assertTrue( $oAction->getActionName() );
        $this->assertEquals('Bestseller', $oAction->getActionName());
    }
    
    /**
     * Testing oxwAction::getListType()
     *
     * @return null
     */
    public function testGetListType()
    {
        $oAction = new oxwAction();
        $oAction->setViewParameters( array("listtype" => "grid") );
        $this->assertTrue( $oAction->getListType() );
        $this->assertEquals('grid', $oAction->getListType());
    }

}