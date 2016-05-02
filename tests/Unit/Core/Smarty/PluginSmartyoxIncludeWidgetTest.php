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
namespace Unit\Core\Smarty;

use \Smarty;
use \oxRegistry;
use \oxTestModules;

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/smarty/plugins/function.oxid_include_widget.php';

class PluginSmartyoxIncludeWidgetTest extends \OxidTestCase
{
    public function testIncludeWidget()
    {
        $oReverseProxyBackend = $this->getMock("oxReverseProxyBackend", array("isActive"), array(), '', false);
        $oReverseProxyBackend->expects($this->any())->method("isActive")->will($this->returnValue(false));
        oxRegistry::set("oxReverseProxyBackend", $oReverseProxyBackend);

        $oShopControl = $this->getMock("oxWidgetControl", array("start"), array(), '', false);
        $oShopControl->expects($this->any())->method("start")->will($this->returnValue('html'));
        oxTestModules::addModuleObject('oxWidgetControl', $oShopControl);

        $oSmarty = new Smarty();
        $result = smarty_function_oxid_include_widget(array('cl' => 'oxwTagCloud', 'blShowTags' => 1), $oSmarty);

        $this->assertEquals('html', $result);
    }
}
