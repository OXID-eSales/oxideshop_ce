<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Core\Controller\BaseController;
use OxidEsales\Eshop\Core\ShopControl;
use Symfony\Component\EventDispatcher\Event;

class BeforeHeadersSendEvent extends Event
{
    const NAME = self::class;

    /**
     * @var BaseController
     */
    private $controller;

    /**
     * @var ShopControl
     */
    private $shopControl;

    /**
     * BeforeHeadersSendEvent constructor.
     *
     * @param ShopControl               $shopControl ShopControl object
     * @param BaseController $controller  Controller
     */
    public function __construct(
        ShopControl $shopControl,
        BaseController $controller
    ) {
        $this->shopControl = $shopControl;
        $this->controller = $controller;
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
    public function getController(): BaseController
    {
        return $this->controller;
    }
}
