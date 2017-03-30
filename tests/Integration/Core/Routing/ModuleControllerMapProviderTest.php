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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version       OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Routing;

use OxidEsales\EshopCommunity\Core\Routing\Module\ClassProviderStorage;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider;
use OxidEsales\Eshop\Core\Module\ModuleVariablesLocator;

/**
 * Test the module ControllerProvider.
 *
 * @package Unit\Core\Routing\Module
 */
class ModuleControllerMapProviderTest extends UnitTestCase
{

    /**
     * Set up fixture
     */
    protected function setUp()
    {
        parent::setUp();

        ModuleVariablesLocator::resetModuleVariables();
    }

    /**
     * The data provider for the method testGetControllerMapWithModules.
     *
     * @return array
     */
    public function dataProviderTestPossibleCombinationsOfActiveModules()
    {
        return [
            // no module active
            [
                [],
                []
            ],

            // 2 modules active
            [
                [
                    'module1' => [
                        'module1controller1' => 'a',
                        'module1controller2' => 'b'
                    ],
                    'module2' => [
                        'module2controller1' => 'c',
                        'module2controller2' => 'd'
                    ]
                ],
                [
                    'module1controller1' => 'a',
                    'module1controller2' => 'b',
                    'module2controller1' => 'c',
                    'module2controller2' => 'd'
                ]
            ]
        ];
    }

    /**
     * @dataProvider dataProviderTestPossibleCombinationsOfActiveModules
     *
     * @param array $controllerKeysFromStorage The controller key mapping we get by the storage
     * @param array $expectedControllerKeys    The controller key mapping we expect to be returned
     *
     */
    public function testGetControllerMapWithModules($controllerKeysFromStorage, $expectedControllerKeys)
    {
        $this->getConfig()->saveShopConfVar('aarr', ClassProviderStorage::STORAGE_KEY, $controllerKeysFromStorage);
        $this->assertModuleControllersNotCached();

        /** @var \OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider|\PHPUnit_Framework_MockObject_MockObject $moduleControllerMapProviderMock */
        $moduleControllerMapProviderMock = oxNew(ModuleControllerMapProvider::class);

        $this->assertSame($expectedControllerKeys, $moduleControllerMapProviderMock->getControllerMap());
        $this->assertModuleControllersCached($controllerKeysFromStorage);
    }

    /**
     * Assert that module controller data is cached in filesystem.
     *
     * @param array $expectedControllerKeys
     */
    protected function assertModuleControllersCached($expectedControllerKeys)
    {
        $subShopSpecificCache = $this->getFileCache();
        $this->assertEquals($expectedControllerKeys, $subShopSpecificCache->getFromCache(ClassProviderStorage::STORAGE_KEY));
    }

    /**
     * Assert that module controller data is not cached in filesystem.
     */
    protected function assertModuleControllersNotCached()
    {
        $subShopSpecificCache = $this->getFileCache();
        $this->assertNull($subShopSpecificCache->getFromCache(ClassProviderStorage::STORAGE_KEY));
    }

    /**
     * Get a file cache object
     */
    private function getFileCache()
    {
        $shopId = $this->getTestConfig()->getShopId();

        $shopIdCalculatorMock = $this->getMock('\OxidEsales\EshopCommunity\Core\ShopIdCalculator', array('getShopId'), array(), '', false);
        $shopIdCalculatorMock->expects($this->any())->method('getShopId')->will($this->returnValue($shopId));

        $subShopSpecificCache = oxNew('\OxidEsales\EshopCommunity\Core\SubShopSpecificFileCache', $shopIdCalculatorMock);

        return $subShopSpecificCache;
    }
}
