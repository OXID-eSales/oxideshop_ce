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
namespace Unit\Application\Controller;

use \oxField;
use \oxException;
use OxidEsales\EshopCommunity\Core\Registry;
use \oxTestModules;

/**
 * oxcmp_shop tests
 */
class CmpShopTest extends \OxidTestCase
{

    /**
     * Testing oxcmp_shop::render()
     */
    public function testRenderNoActiveShop()
    {
        $oView = $this->getMock("oxView", array("getClassName"));
        $oView->expects($this->once())->method('getClassName')->will($this->returnValue("test"));

        $oShop = oxNew('oxShop');
        $oShop->oxshops__oxactive = new oxField(0);

        $oUtils = $this->getMock('oxUtils', array('showOfflinePage'));
        $oUtils->expects($this->once())->method('showOfflinePage');
        Registry::set('oxUtils', $oUtils);

        $oConfig = $this->getMock("oxConfig", array("getConfigParam", "getActiveView", "getActiveShop"));
        $oConfig->expects($this->once())->method('getActiveView')->will($this->returnValue($oView));
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValue(false));
        $oConfig->expects($this->once())->method('getActiveShop')->will($this->returnValue($oShop));

        $oCmp = $this->getMock("oxcmp_shop", array("getConfig", "isAdmin"), array(), '', false);
        $oCmp->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $oCmp->expects($this->once())->method('isAdmin')->will($this->returnValue(false));

        $oCmp->render();
    }
}
