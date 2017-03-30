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
 * Type hint in method finalizeOrder is for BC class, the type hint in parent is for VNS class.
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload
 */
class TypeHintTestOrder extends \OxidEsales\Eshop\Application\Model\Order
{
    /**
     * @param oxBasket $basket
     * @param User     $user
     * @param bool     $recalculatingOrder
     */
    public function finalizeOrder(oxBasket $basket, $user, $recalculatingOrder = false)
    {
        parent::finalizeOrder($basket, $user, $recalculatingOrder);
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
     * Test type hints with BC aliases.
     */
    public function testTypeHintAliasingVNS()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load('oxdefaultadmin');
        $basket = oxNew(\OxidEsales\Eshop\Application\Model\Basket::class);
        $order = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload\TypeHintTestOrder::class);
        $order->finalizeOrder($basket, $user);
    }

    /**
     * Test type hints with BC aliases.
     */
    public function testTypeHintAliasingBC()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load('oxdefaultadmin');
        $basket = oxNew('oxBasket');
        $order = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload\TypeHintTestOrder::class);
        $order->finalizeOrder($basket, $user);
    }

    /**
     * Test type hints with BC aliases.
     */
    public function testTypeHintAliasingBCnew()
    {
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $user->load('oxdefaultadmin');
        $basket = new \oxBasket;
        $order = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Core\Autoload\TypeHintTestOrder::class);
        $order->finalizeOrder($basket, $user);
    }
}
