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

/**
 * Class Unit_Core_oxOnlineRequestTest
 *
 * @covers oxOnlineRequest
 */
class OnlineRequestTest extends \OxidTestCase
{
    public function testClusterIdGenerationWhenNotSet()
    {
        $this->getConfig()->setConfigParam('sClusterId', '');
        $request = oxNew('oxOnlineRequest');
        $this->assertNotEquals('', $request->clusterId);
    }

    public function testClusterIdIsNotRegenerationWhenAlreadySet()
    {
        $this->getConfig()->setConfigParam('sClusterId', 'generated_unique_cluster_id');
        $request = oxNew('oxOnlineRequest');
        $this->assertSame('generated_unique_cluster_id', $request->clusterId);
    }

    public function testDefaultParametersSetOnConstruct()
    {
        $config = $this->getConfig();

        $config->setConfigParam('sClusterId', 'generated_unique_cluster_id');
        $request = oxNew('oxOnlineRequest');

        $this->assertSame('generated_unique_cluster_id', $request->clusterId);
        $this->assertSame($config->getEdition(), $request->edition);
        $this->assertSame($config->getVersion(), $request->version);
        $this->assertSame($config->getShopUrl(), $request->shopUrl);
    }
}
