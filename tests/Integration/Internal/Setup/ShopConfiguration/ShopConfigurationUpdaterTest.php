<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\ShopConfiguration;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\ShopConfiguration\ShopConfigurationUpdaterInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ShopConfigurationUpdaterTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function testSaveShopSetupTime(): void
    {
        $oldTime = $this
            ->get(ShopConfigurationSettingDaoInterface::class)
            ->get('sTagList', 1)
            ->getValue();

        $this->get(ShopConfigurationUpdaterInterface::class)->saveShopSetupTime();

        $newTime = $this
            ->get(ShopConfigurationSettingDaoInterface::class)
            ->get('sTagList', 1)
            ->getValue();

        $this->assertGreaterThan($oldTime, $newTime);
    }
}
