<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Cache\ShopCacheCleanerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('cache')]
final class ResolvedThemeConfigurationCacheTest extends TestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;

    public function testGetReturnsPutConfiguration(): void
    {
        $cache = $this->get('oxid_esales.theme.configuration.resolver_cache');
        $cache->put(self::SHOP_ID, (new ThemeConfiguration())->setId('theme'));

        $this->assertSame('theme', $cache->get('theme', self::SHOP_ID)->getId());
    }

    public function testShopCacheClearRemovesConfiguration(): void
    {
        $cache = $this->get('oxid_esales.theme.configuration.resolver_cache');
        $cache->put(self::SHOP_ID, (new ThemeConfiguration())->setId('theme'));

        $this->get(ShopCacheCleanerInterface::class)->clear(self::SHOP_ID);

        $this->assertFalse($cache->exists('theme', self::SHOP_ID));
    }
}
