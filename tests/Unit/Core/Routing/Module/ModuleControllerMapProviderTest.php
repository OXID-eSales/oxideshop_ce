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
namespace Unit\Core\Routing\Module;

use OxidEsales\EshopCommunity\Core\Routing\Module\ClassProviderStorage;
use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Routing\ModuleControllerMapProvider;

/**
 * Test the module ControllerProvider.
 *
 * @package Unit\Core\Routing\Module
 */
class ModuleControllerMapProviderTest extends UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $storage = oxNew(ClassProviderStorage::class);
        $storage->set(null);
    }

    /**
     * Test, that the creation works properly.
     *
     * @return ModuleControllerMapProvider A plain module controller provider.
     */
    public function testCreation()
    {
        $moduleControllerProvider = oxNew(ModuleControllerMapProvider::class);

        $this->assertTrue(method_exists($moduleControllerProvider, 'getControllerMap'));

        return $moduleControllerProvider;
    }

    /**
     * Test, that the controller provider gives back null, if no module was activated ever before.
     */
    public function testWithoutModules()
    {
        $moduleControllerProvider = $this->testCreation();

        $result = $moduleControllerProvider->getControllerMap();

        $this->assertNotNull($result);
        $this->assertTrue(is_array($result));
        $this->assertEmpty($result);
    }
}
