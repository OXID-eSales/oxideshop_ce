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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'core/smarty/plugins/function.oxscript.php';

class Unit_Maintenance_pluginSmartyOxScriptTest extends OxidTestCase
{

    /**
     * Check for error if not existing file for include given.
     *
     * @expectedException PHPUnit_Framework_Error
     */
    public function testSmartyFunctionOxScript_includeNotExist()
    {
        $this->getConfig()->setConfigParam("iDebug", -1);

        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('include' => 'somescript.js'), $oSmarty));
    }

    /**
     * Check oxscript include
     */
    public function testSmartyFunctionOxScript_includeExist()
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('include' => 'http://someurl/src/js/libs/jquery.min.js'), $oSmarty));

        $sOutput = '<script type="text/javascript" src="http://someurl/src/js/libs/jquery.min.js"></script>' . "\n";

        $this->assertEquals($sOutput, smarty_function_oxscript(array('inWidget' => false), $oSmarty));
    }

    /**
     * Check oxscript inclusion in widget
     */
    public function testSmartyFunctionOxScript_widget_include()
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('include' => 'http://someurl/src/js/libs/jquery.min.js'), $oSmarty));

        $sOutput = '<script type="text/javascript">' . PHP_EOL
                   . 'window.addEventListener("load", function() {' . PHP_EOL
                   . 'WidgetsHandler.registerFile( "http://someurl/src/js/libs/jquery.min.js", "somewidget" );' . PHP_EOL
                   . '}, false )' . PHP_EOL
                   . '</script>' . PHP_EOL;

        $this->assertEquals($sOutput, smarty_function_oxscript(array('widget' => 'somewidget', 'inWidget' => true), $oSmarty));
    }

    /**
     * Check for oxscript add method
     */
    public function testSmartyFunctionOxScript_add()
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('add' => 'oxidadd'), $oSmarty));

        $sOutput = '<script type="text/javascript">' . PHP_EOL . 'oxidadd' . PHP_EOL . '</script>' . PHP_EOL;

        $this->assertEquals($sOutput, smarty_function_oxscript(array(), $oSmarty));
    }

    /**
     * Provides data for oxscript add function
     */
    public function addProvider()
    {
        return array(
            array('oxidadd', 'oxidadd'),
            array('"oxidadd"', '\"oxidadd\"'),
            array("'oxidadd'", "'oxidadd'"),
        );
    }

    /**
     * Check for oxscript add method in widget
     *
     * @dataProvider addProvider
     */
    public function testSmartyFunctionOxScript_widget_add($sScript, $sScriptOutput)
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('add' => $sScript), $oSmarty));

        $sOutput = '<script type="text/javascript">' . PHP_EOL
                   . 'window.addEventListener("load", function() {' . PHP_EOL
                   . 'WidgetsHandler.registerFunction( "' . $sScriptOutput . '", "somewidget");' . PHP_EOL
                   . '}, false )' . PHP_EOL
                   . '</script>' . PHP_EOL;

        $this->assertEquals($sOutput, smarty_function_oxscript(array('widget' => 'somewidget', 'inWidget' => true), $oSmarty));
    }
}
