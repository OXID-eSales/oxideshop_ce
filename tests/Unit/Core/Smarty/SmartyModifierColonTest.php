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

use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sShopDir') . 'Core/Smarty/Plugin/modifier.colon.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/modifier.colon.php';
}

class SmartyModifierColonTest extends \OxidTestCase
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
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("translateString"));
        $oLang->expects($this->any())->method("translateString")->with($this->equalTo('COLON'))->will($this->returnValue($sTranslation));

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Language::class, $oLang);

        $this->assertEquals($sResult, smarty_modifier_colon('Name'));
    }
}
