<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Routing;

use OxidEsales\Eshop\Core\FileCache;
use OxidEsales\Eshop\Core\Routing\Module\ClassProviderStorage;
use OxidEsales\Eshop\Core\Routing\ModuleControllerMapProvider;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use OxidEsales\Eshop\Core\SubShopSpecificFileCache;
use OxidEsales\Eshop\Core\UtilsObject;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class ModuleControllerMapProviderTest extends UnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->resetUtilsObjectInstance();
        $this->mockShopIdRequest();
    }

    public function testGetControllerMapWithEmptyConfiguration(): void
    {
        $this->saveControllerMapToShopConfiguration([]);

        $controllerMap = oxNew(ModuleControllerMapProvider::class)->getControllerMap();

        $this->assertEmpty($controllerMap);
    }

    public function testGetControllerMapWillReturnFlatArray(): void
    {
        $originalControllerMap = [
            'module1' => [
                'module1controller1' => 'a',
                'module1controller2' => 'b',
            ],
            'module2' => [
                'module2controller1' => 'c',
                'module2controller2' => 'd',
            ],
        ];
        $controllerMapFlattened = [
            'module1controller1' => 'a',
            'module1controller2' => 'b',
            'module2controller1' => 'c',
            'module2controller2' => 'd',
        ];
        $this->saveControllerMapToShopConfiguration($originalControllerMap);

        $controllerMap = oxNew(ModuleControllerMapProvider::class)->getControllerMap();

        $this->assertEquals($controllerMapFlattened, $controllerMap);
    }

    public function testGetControllerMapWillUpdateFileCache(): void
    {
        $originalControllerMap = [
            'module1' => [
                'module1controller1' => 'a',
            ],
            'module2' => [
                'module2controller1' => 'b',
            ],
        ];
        $this->saveControllerMapToShopConfiguration($originalControllerMap);
        $this->assertEmpty($this->getControllerMapFromFileCache());

        oxNew(ModuleControllerMapProvider::class)->getControllerMap();

        $this->assertEquals($originalControllerMap, $this->getControllerMapFromFileCache());
    }

    private function resetUtilsObjectInstance(): void
    {
        Registry::set(UtilsObject::class, null);
    }

    private function mockShopIdRequest(): void
    {
        $_GET['shp'] = $this->getTestConfig()->getShopId();
    }

    private function saveControllerMapToShopConfiguration(array $controllerMap): void
    {
        $this->getConfig()->saveShopConfVar(
            'aarr', ClassProviderStorage::STORAGE_KEY,
            $controllerMap,
            $this->getTestConfig()->getShopId()
        );
    }

    private function getControllerMapFromFileCache(): ?array
    {
        return oxNew(
            SubShopSpecificFileCache::class,
            new ShopIdCalculator(new FileCache())
        )
            ->getFromCache(ClassProviderStorage::STORAGE_KEY);
    }

}
