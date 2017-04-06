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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
