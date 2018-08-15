<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \Smarty;
use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxscript.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxscript.php';
}

class PluginSmartyOxScriptTest extends \OxidTestCase
{

    /**
     * Check for error if not existing file for include given.
     *
     * @expectedException PHPUnit\Framework\Error\Warning
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

        $sOutput = '<script type="text/javascript" src="http://someurl/src/js/libs/jquery.min.js"></script>';

        $this->assertEquals($sOutput, smarty_function_oxscript(array('inWidget' => false), $oSmarty));
    }

    /**
     * Check oxscript inclusion in widget
     */
    public function testSmartyFunctionOxScript_widget_include()
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('include' => 'http://someurl/src/js/libs/jquery.min.js'), $oSmarty));

        $sOutput = <<<JS
<script type='text/javascript'>
    window.addEventListener('load', function() {
        WidgetsHandler.registerFile('http://someurl/src/js/libs/jquery.min.js', 'somewidget');
    }, false)
</script>
JS;

        $this->assertEquals($sOutput, smarty_function_oxscript(array('widget' => 'somewidget', 'inWidget' => true), $oSmarty));
    }

    /**
     * Check for oxscript add method
     */
    public function testSmartyFunctionOxScript_add()
    {
        $oSmarty = new Smarty();
        $this->assertEquals('', smarty_function_oxscript(array('add' => 'oxidadd'), $oSmarty));

        $sOutput = "<script type='text/javascript'>oxidadd</script>";

        $this->assertEquals($sOutput, smarty_function_oxscript(array(), $oSmarty));
    }

    /**
     * Provides data for oxscript add function
     */
    public function addProvider()
    {
        return array(
            array('oxidadd', 'oxidadd'),
            array('"oxidadd"', '"oxidadd"'),
            array("'oxidadd'", "\\'oxidadd\\'"),
            array("oxid\r\nadd", 'oxid\nadd'),
            array("oxid\nadd", 'oxid\nadd'),
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

        $sOutput = "<script type='text/javascript'>window.addEventListener('load', function() { WidgetsHandler.registerFunction('$sScriptOutput', 'somewidget'); }, false )</script>";
        $this->assertEquals($sOutput, smarty_function_oxscript(array('widget' => 'somewidget', 'inWidget' => true), $oSmarty));
    }
}
