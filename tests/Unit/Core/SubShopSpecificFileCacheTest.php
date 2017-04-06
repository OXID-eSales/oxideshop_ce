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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class SubShopSpecificFileCacheTest extends \OxidTestCase
{

    public function testSetGetCacheSubShopSpecific()
    {
        $sTest = "test val";

        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue(1));

        $moduleCache = oxNew('oxSubShopSpecificFileCache', $shopIdCalculator);

        $moduleCache->setToCache("testKey", $sTest);
        $this->assertEquals($sTest, $moduleCache->getFromCache("testKey"));
    }

    public function testGetCacheFileName()
    {
        $shopIdCalculator = $this->getMock(\OxidEsales\Eshop\Core\ShopIdCalculator::class, array('getShopId'), array(), '', false);
        $shopIdCalculator->expects($this->any())->method('getShopId')->will($this->returnValue(2));

        $moduleCache = $this->getProxyClass('oxSubShopSpecificFileCache', array($shopIdCalculator));

        $sExpt = "config.2.testval.txt";
        $this->assertEquals($sExpt, basename($moduleCache->getCacheFilePath("testVal")));
    }
}
