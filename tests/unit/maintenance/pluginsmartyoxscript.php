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
 * @version   SVN: $Id: pluginsmartyoxcontentTest.php 26841 2010-03-25 13:58:15Z arvydas $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';
require_once oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'core/smarty/plugins/function.oxscript.php';

class Unit_Maintenance_pluginSmartyOxScriptTest extends OxidTestCase
{
    public function testSmartyFunctionOxScript()
    {
        $oSmarty = new oxStdClass();
        $this->assertEquals('', smarty_function_oxmailto( array('include' => 'oxid'), $oSmarty ) );
        $this->assertEquals('', smarty_function_oxmailto( array('add' => 'oxid'), $oSmarty ) );

        $sOutput = '<script type="text/javascript" src="oxid"></script>'."\n";
        $sOutput.= '<script type="text/javascript">oxid</script>';

        $this->assertEquals($sOutput, smarty_function_oxmailto( array(), $oSmarty ) );
    }
}
