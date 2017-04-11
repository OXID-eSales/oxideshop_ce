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

require_once oxRegistry::getConfig()->getConfigParam('sShopDir') . 'core/smarty/plugins/modifier.colon.php';

class Unit_Maintenance_smartyModifierColonTest extends OxidTestCase
{

    /**
     * provides data to testColons
     *
     * @return array
     */
    public function provider()
    {
        return array(
            array(':', 'Name:'), // normal colon
            array(' :', 'Name :') // french, for example, has space before colon
        );
    }

    /**
     * Test colon smarty modifier
     *
     * @dataProvider provider
     */
    public function testColons($sTranslation, $sResult)
    {
        $oLang = $this->getMock("oxLang", array("translateString"));
        $oLang->expects($this->any())->method("translateString")->with($this->equalTo('COLON'))->will($this->returnValue($sTranslation));

        oxRegistry::set('oxLang', $oLang);

        $this->assertEquals($sResult, smarty_modifier_colon('Name'));
    }
}
