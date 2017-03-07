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
namespace OxidEsales\EshopCommunity\Tests\Integration\Modules;

/**
 * Test for the module class chain.
 *
 * We found some bugs, so this test class is a regression test, if you have a better place for some of the tests or all,
 * feel free to move things.
 *
 * @group module
 */
class ModuleChainTest extends BaseModuleTestCase
{
    /**
     * Test, that a deactivated module is not used in the module chain.
     */
    public function testModuleChainIsntUsedForDeactivatedModules()
    {
        // @todo: implement test case
    }

    /**
     * Test, that the module activation removes not existing classes from the module chain.
     *
     * Note: same problem might exist with files - maybe we should write a test for the files too.
     */
    public function testModuleActivationRemovesNotExistingChainClasses()
    {
        // @todo: implement test case
    }
}
