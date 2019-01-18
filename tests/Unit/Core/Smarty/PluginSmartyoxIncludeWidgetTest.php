<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \Smarty;
use \oxRegistry;
use \oxTestModules;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/function.oxid_include_widget.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/function.oxid_include_widget.php';
}

class PluginSmartyoxIncludeWidgetTest extends \OxidTestCase
{
    public function testIncludeWidget()
    {
        $oReverseProxyBackend = $this->getMock(\OxidEsales\Eshop\Core\Cache\ReverseProxy\ReverseProxyBackend::class, array("isActive"), array(), '', false);
        $oReverseProxyBackend->expects($this->any())->method("isActive")->will($this->returnValue(false));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Cache\ReverseProxy\ReverseProxyBackend::class, $oReverseProxyBackend);

        $oShopControl = $this->getMock(\OxidEsales\Eshop\Core\WidgetControl::class, array("start"), array(), '', false);
        $oShopControl->expects($this->any())->method("start")->will($this->returnValue('html'));
        oxTestModules::addModuleObject('oxWidgetControl', $oShopControl);

        $oSmarty = new Smarty();
        $result = smarty_function_oxid_include_widget(array('cl' => 'oxwTagCloud', 'blShowTags' => 1), $oSmarty);

        $this->assertEquals('html', $result);
    }
}
