<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Utils;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Utils;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

final class UtilsSearchEngineTest extends IntegrationTestCase
{
    use ContainerTrait;

    public static function providerSearchEngineNoneAdminMode(): array
    {
        return [
            [true, [], 'googlebot', false],
            [false, [], 'googlebot', false],
            [true, ['googlebot', 'xxx'], 'googlebot', true],
            [false, ['googlebot', 'xxx'], 'googlebot', true],
        ];
    }

    #[DataProvider('providerSearchEngineNoneAdminMode')]
    public function testIsSearchEngineNonAdmin(bool $debug, array $robots, string $searchEngine, bool $expected): void
    {
        $this->setParameter('oxid_debug_mode', $debug);
        $this->setParameter('oxid_search_engine_list', $robots);
        $this->attachContainerToContainerFactory();

        $this->assertSame($expected, (new Utils())->isSearchEngine($searchEngine));
    }

    public function testIsSearchEngineAdminAndDebugOn(): void
    {
        $this->setParameter('oxid_debug_mode', true);
        $this->setParameter('oxid_search_engine_list', ['googlebot', 'xxx']);
        $this->attachContainerToContainerFactory();

        $utils = $this->getUtils();

        $this->assertFalse($utils->isSearchEngine('googlebot'));
        $this->assertFalse($utils->isSearchEngine('xxx'));
    }

    private function getUtils(): UtilsSpy
    {
        $utils = new UtilsSpy();
        Registry::set(Utils::class, $utils);

        return $utils;
    }
}
