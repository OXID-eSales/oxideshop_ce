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

/**
 * oxvatselector test
 */
class Unit_Core_oxtsprotectionTest extends OxidTestCase
{

    /**
     * Tests oxtsprotection::getTsProduct()
     *
     */
    public function testGetTsProduct()
    {
        $oTsProtection = oxNew('oxtsprotection');
        $oProduct = $oTsProtection->getTsProduct('TS080501_500_30_EUR');

        $this->assertEquals('TS080501_500_30_EUR', $oProduct->getTsId());
        $this->assertEquals(500, $oProduct->getAmount());
        $this->assertEquals('0,82', $oProduct->getFPrice());
    }

    /**
     * Tests oxtsprotection::getTsProducts()
     *
     */
    public function testGetTsProducts()
    {
        $oTsProtection = oxNew('oxtsprotection');
        $oProducts = $oTsProtection->getTsProducts(50);
        $oProduct = current($oProducts);

        $this->assertEquals('TS080501_500_30_EUR', $oProduct->getTsId());
        $this->assertEquals(500, $oProduct->getAmount());
        $this->assertEquals('0,82', $oProduct->getFPrice());
    }

    /**
     * Tests oxtsprotection::getTsProducts()
     *
     */
    public function testGetTsProductsWithBiggerPrice()
    {
        $oTsProtection = oxNew('oxtsprotection');
        $oProducts = $oTsProtection->getTsProducts(2000);

        $this->assertEquals(3, count($oProducts));
    }

    /**
     * Tests whether oxtsprotection::checkCertificate() is correctly envoked
     *
     */
    public function testCheckCertificate()
    {
        $sSoapUrl = 'https://www.trustedshops.de/ts/services/TsProtection?wsdl';
        $sFunction = 'checkCertificate';
        $iTrustedShopId = 'AAAA';
        $oTsProtection = $this->getMock("oxtsprotection", array("executeSoap"));
        $oTsProtection->expects($this->any())->method('executeSoap')->with($this->equalTo($sSoapUrl), $this->equalTo($sFunction), $this->equalTo($iTrustedShopId))->will($this->returnValue(true));

        $this->assertTrue($oTsProtection->checkCertificate($iTrustedShopId, false));
    }

    /**
     * Tests oxtsprotection::_getTsProductCurrId()
     *
     */
    public function testGetTsProductCurrId()
    {
        $oTsProtection = oxNew('oxtsprotection');
        $sId = $oTsProtection->UNITgetTsProductCurrId('TS080501_500_30_EUR', 'GBP');

        $this->assertEquals('TS100629_500_30_GBP', $sId);
    }

}
