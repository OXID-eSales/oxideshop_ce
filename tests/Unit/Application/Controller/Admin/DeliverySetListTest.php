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
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \oxTestModules;

/**
 * Tests for DeliverySet_List class
 */
class DeliverySetListTest extends \OxidTestCase
{

    /**
     * DeliverySet_List::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction("oxUtilsServer", "getOxCookie", "{return array(1);}");
        oxTestModules::addFunction("oxUtils", "checkAccessRights", "{return true;}");

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->any())->method('checkSessionChallenge')->will($this->returnValue(true));

        $oView = $this->getMock($this->getProxyClassName('DeliverySet_List'), array('getSession'));
        $oView->expects($this->any())->method('getSession')->will($this->returnValue($oSess));

        $oView->init();

        $this->assertEquals('oxdeliveryset', $oView->getNonPublicVar("_sListClass"));
        $this->assertEquals('oxdeliverysetlist', $oView->getNonPublicVar("_sListType"));
        $this->assertEquals(array("oxdeliveryset" => array("oxpos" => "asc")), $oView->getListSorting());
        $this->assertEquals('deliveryset_list.tpl', $oView->render());
    }

}
