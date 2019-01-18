<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
     * Executed after test is down.
    **/
    protected function tearDown()
    {
        if (\OxidEsales\Eshop\Core\DatabaseProvider::getDb()->isTransactionActive()) {
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->rollbackTransaction();
        }

        parent::tearDown();
    }

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
