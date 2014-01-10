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
 * Tests for dyn_affili24 class
 */
class Unit_Admin_dynaffili24Test extends OxidTestCase
{
    /**
     * dyn_affili24::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_affili24();
        $this->assertEquals( 'dyn_affili24.tpl', $oView->render() );
    }

    /**
     * dyn_affili24::Save() test case
     *
     * @return null
     */
    public function testSave()
    {
        // testing..
        oxTestModules::addFunction( 'oxshop', 'save', '{ throw new Exception( "save" ); }');
        modConfig::getInstance()->setConfigParam( "blAllowSharedEdit", true );

        // testing..
        try {
            $oView = new dyn_affili24();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in dyn_affili24::save()" );
            return;
        }
        $this->fail( "error in dyn_affili24::save()" );
    }
}
