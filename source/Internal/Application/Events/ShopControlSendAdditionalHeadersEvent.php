<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Application\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ShopControlSendAdditionalHeadersEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Application\Events
 */
class ShopControlSendAdditionalHeadersEvent extends Event
{
    /**
     * Result
     *
     * @var bool
     */
    protected $result = false;

    /**
     * @var \OxidEsales\Eshop\Core\Controller\BaseController
     */
    protected $controller = null;

    /**
     * @var \OxidEsales\Eshop\Core\ShopControl
     */
    protected $shopControl = null;

    /**
     * Handle event.
     *
     * @return null
     */
    public function handleEvent()
    {
    }

    /**
     * Setter for ShopControl object.
     *
     * @param \OxidEsales\Eshop\Core\ShopControl $shopControl ShopControl object
     */
    public function setShopControl(\OxidEsales\Eshop\Core\ShopControl $shopControl)
    {
        $this->shopControl = $shopControl;
    }

    /**
     * Setter for result.
     *
     * @param string $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }

    /**
     * Setter for controller object.
     *
     * @param \OxidEsales\Eshop\Core\Controller\BaseController controller object
     */
    public function setController(\OxidEsales\Eshop\Core\Controller\BaseController $controller)
    {
        $this->controller = $controller;
    }

    /**
     * Getter for ShopControl object.
     *
     * @return \OxidEsales\Eshop\Core\ShopControl
     */
    public function getShopControl()
    {
        return $this->shopControl;
    }

    /**
     * Getter for result
     *
     * @return bool
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Getter for controller object.
     *
     * @return \OxidEsales\Eshop\Core\Controller\BaseController
     */
    public function getController()
    {
        return $this->controller;
    }
}
