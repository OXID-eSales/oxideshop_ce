<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class BeforeHeadersSendEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class BeforeHeadersSendEvent extends Event
{
    const NAME = self::class;

    /**
     * @var \OxidEsales\Eshop\Core\Controller\BaseController
     */
    private $controller;

    /**
     * @var \OxidEsales\Eshop\Core\ShopControl
     */
    private $shopControl;

    /**
     * BeforeHeadersSendEvent constructor.
     *
     * @param \OxidEsales\Eshop\Core\ShopControl               $shopControl ShopControl object
     * @param \OxidEsales\Eshop\Core\Controller\BaseController $controller  Controller
     */
    public function __construct(
        \OxidEsales\Eshop\Core\ShopControl $shopControl,
        \OxidEsales\Eshop\Core\Controller\BaseController $controller
    ) {
        $this->shopControl = $shopControl;
        $this->controller = $controller;
    }

    /**
     * Getter for ShopControl object.
     *
     * @return \OxidEsales\Eshop\Core\ShopControl
     */
    public function getShopControl(): \OxidEsales\Eshop\Core\ShopControl
    {
        return $this->shopControl;
    }

    /**
     * Getter for controller object.
     *
     * @return \OxidEsales\Eshop\Core\Controller\BaseController
     */
    public function getController(): \OxidEsales\Eshop\Core\Controller\BaseController
    {
        return $this->controller;
    }
}
