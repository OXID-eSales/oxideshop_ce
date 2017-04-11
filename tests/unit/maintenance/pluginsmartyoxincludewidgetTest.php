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

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'core/smarty/plugins/function.oxid_include_widget.php';

class Unit_Maintenance_pluginSmartyoxIncludeWidgetTest extends OxidTestCase
{

    public function testIncludeIfHTMLCachingIsOn()
    {
        return;
        $oReverseProxyBackend = $this->getMock("oxReverseProxyBackend", array("isActive"));
        $oReverseProxyBackend->expects($this->any())->method("isActive")->will($this->returnValue(true));

        oxRegistry::set("oxReverseProxyBackend", $oReverseProxyBackend);

        $oSmarty = new smarty();
        $sOutput = "<esi:include src='" . $this->getConfig()->getWidgetUrl() . "blShowTags=1&amp;cl=oxwtagcloud'/>";
        $this->assertEquals($sOutput, smarty_function_oxid_include_widget(array('cl' => 'oxwTagCloud', 'blShowTags' => 1), $oSmarty));

        oxRegistry::getLang()->setBaseLanguage(1);
        $sOutput = "<esi:include src='" . $this->getConfig()->getWidgetUrl() . "blShowTags=1&amp;cl=oxwtagcloud'/>";
        $this->assertEquals($sOutput, smarty_function_oxid_include_widget(array('cl' => 'oxwTagCloud', 'blShowTags' => 1), $oSmarty));

    }

    public function testIncludeWidget()
    {
        $oReverseProxyBackend = $this->getMock("oxReverseProxyBackend", array("isActive"), array(), '', false);
        $oReverseProxyBackend->expects($this->any())->method("isActive")->will($this->returnValue(false));
        oxRegistry::set("oxReverseProxyBackend", $oReverseProxyBackend);

        $oShopControl = $this->getMock("oxWidgetControl", array("start"), array(), '', false);
        $oShopControl->expects($this->any())->method("start")->will($this->returnValue('html'));
        oxTestModules::addModuleObject('oxWidgetControl', $oShopControl);


        $oSmarty = new Smarty();
        $this->assertEquals(
            smarty_function_oxid_include_widget(array('cl' => 'oxwTagCloud', 'blShowTags' => 1), $oSmarty)
            , 'html'
        );
    }
}
