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
 * Tests for dyn_adbutler class
 */
class Unit_Admin_dynadbutlerTest extends OxidTestCase
{
    /**
     * dyn_adbutler::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new dyn_adbutler();
        $this->assertEquals( 'dyn_adbutler.tpl', $oView->render() );
    }

    /**
     * dyn_adbutler::Save() test case
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
            $oView = new dyn_adbutler();
            $oView->save();
        } catch ( Exception $oExcp ) {
            $this->assertEquals( "save", $oExcp->getMessage(), "error in dyn_adbutler::save()" );
            return;
        }
        $this->fail( "error in dyn_adbutler::save()" );
    }

    /**
     * dyn_adbutler::CheckId() test case
     *
     * @return null
     */
    public function testCheckId()
    {
        $aTestData = array( "123456789" => "123456789", "12345678" => "012345678", "1234567" => "001234567" );
        $oView = new dyn_adbutler();
        foreach ( $aTestData as $iIn => $iTest ) {
            $this->assertEquals( $iTest, $oView->UNITcheckId( $iIn ) );
        }
    }
}
