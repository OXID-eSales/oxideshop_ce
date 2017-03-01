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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Test the module controller provider cache.
 *
 * @package Unit\Core\Routing\Module
 */
class VirtualNamespaceClassMapProviderTest extends UnitTestCase
{
    /**
     * Test class map getter.
     */
    public function testGetClassMap()
    {
        $shopEdition = $this->getTestConfig()->getShopEdition();
        $Map = array('CE' => 'Community',
                     'PE' => 'Professional',
                     'EE' => 'Enterprise');
        $expectedMatch = 'OxidEsales\Eshop' . $Map[$shopEdition] . '\Application\Model\User';
        $virtualClassName = 'OxidEsales\Eshop\Application\Model\User';

        $test = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMapProvider;
        $virtualClassMap = $test->getClassMap();
        $this->assertEquals($expectedMatch, $virtualClassMap[$virtualClassName]);
    }

    /**
     * Test edition getter.
     */
    public function testGetEdition()
    {
        $shopEdition = $this->getTestConfig()->getShopEdition();
        $test = new \OxidEsales\EshopCommunity\Core\Autoload\VirtualNameSpaceClassMapProvider;
        $this->assertEquals($shopEdition, $test->getEdition());
    }
}
