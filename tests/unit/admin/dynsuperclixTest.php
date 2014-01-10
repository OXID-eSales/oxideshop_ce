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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

/**
 * Tests for dyn_superclix class
 */
class Unit_Admin_dynsuperclixTest extends OxidTestCase
{
    /**
     * dyn_superclix::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_superclix();
        $this->assertEquals( 'dyn_superclix.tpl', $oView->render() );
    }

    /**
     * dyn_superclix::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction( 'oxshop', 'save', '{ throw new Exception( "save" ); }');
        oxTestModules::addFunction( 'oxshop', 'load', '{ return true; }');
        oxTestModules::addFunction( 'oxshop', 'assign', '{ return true; }');

        // testing..
        try {
            $oView = new dyn_superclix();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in dyn_superclix::save()" );
            return;
        }
        $this->fail( "error in dyn_superclix::save()" );
    }

}
