<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Core\Controller\BaseController;
use OxidEsales\Eshop\Core\ShopControl;
use OxidEsales\EshopCommunity\Internal\Framework\Controller\ControllerInterface;
use Symfony\Contracts\EventDispatcher\Event;

class BeforeHeadersSendEvent extends Event
{
    public function __construct(
        private ShopControl $shopControl,
        private BaseController|ControllerInterface $controller
    ) {
    }

    /**
     * Getter for ShopControl object.
     *
     * @return ShopControl
     */
    public function getShopControl(): ShopControl
    {
        return $this->shopControl;
    }

    /**
     * Getter for controller object.
     *
     * @return BaseController
     */
    public function getController(): BaseController|ControllerInterface
    {
        return $this->controller;
    }
}
