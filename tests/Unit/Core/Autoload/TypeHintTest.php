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
use oxBasket;
use OxidEsales\Eshop\Application\Model\User;

/**
 * Class typeHintTestBasket.
 *
 * Type hint in method finalizeOrder is for the backwards compatibility class, the type hint in parent is for the Unified
 * Namespaced class.
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload
 */
class TypeHintTestOrder extends \OxidEsales\Eshop\Application\Model\Order
{
    /**
     * @param oxBasket $basket
     * @param User     $user
     * @param bool     $recalculatingOrder
     *
     * @return integer
     */
    public function finalizeOrder(oxBasket $basket, $user, $recalculatingOrder = false)
    {
        return parent::finalizeOrder($basket, $user, $recalculatingOrder);
    }
}

/**
 * Class TypeHintTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload
 */
class TypeHintTest extends UnitTestCase
{
    /**
     * Test type hints with backwards compatibility aliases.
     */
    public function testTypeHintAliasingUnifiedNamespace()
    {
        $basket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);

        $this->createOrder()->finalizeOrder($basket, $this->loadDefaultAdminUser());
    }

    /**
     * Test type hints with backwards compatibility aliases.
     */
    public function testTypeHintAliasingBackwardsCompatibilityWithOxNew()
    {
        $basket = oxNew('oxBasket');

        $this->createOrder()->finalizeOrder($basket, $this->loadDefaultAdminUser());
    }

    /**
     * Test type hints with backwards compatibility aliases.
     */
    public function testTypeHintAliasingBackwardsCompatibilityWithNew()
    {
        $basket = new \oxBasket;

        $this->createOrder()->finalizeOrder($basket, $this->loadDefaultAdminUser());
    }

    /**
     * Load the default admin user.
     *
     * @return \OxidEsales\Eshop\Application\Model\User The default admin user.
     */
    protected function loadDefaultAdminUser()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load('oxdefaultadmin');

        return $user;
    }

    /**
     * Create the example module order object.
     *
     * @return \OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload\TypeHintTestOrder
     */
    protected function createOrder()
    {
        return oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload\TypeHintTestOrder::class);
    }
}
