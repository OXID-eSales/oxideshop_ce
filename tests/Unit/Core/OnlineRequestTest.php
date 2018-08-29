<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
