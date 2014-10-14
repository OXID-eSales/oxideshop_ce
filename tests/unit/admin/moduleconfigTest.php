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

/**
 * Tests for Shop_Config class
 */
class Unit_Admin_ModuleConfigTest extends OxidTestCase
{
    /**
     * Shop_Config::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        // testing..
        $oView = new Module_Config();
        $this->assertEquals( 'module_config.tpl', $oView->render() );
    }


    public function testGetModuleForConfigVars()
    {
        $oView = $this->getProxyClass( 'Module_Config' );
        $oView->setNonPublicVar( "_sModuleId", "testModuleId" );
        $this->assertEquals( 'module:testModuleId', $oView->UNITgetModuleForConfigVars() );
    }

}
