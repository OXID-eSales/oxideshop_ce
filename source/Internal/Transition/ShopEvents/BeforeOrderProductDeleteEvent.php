<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\OrderArticle;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class BeforeOrderProductDeleteEvent extends Event
{
    public const NAME = self::class;

    /** @var OrderArticle */
    private $orderProduct;

    public function __construct(
        OrderArticle $orderProduct
    ) {
        $this->orderProduct = $orderProduct;
    }

    /** @return OrderArticle */
    public function getOrderProduct(): OrderArticle
    {
        return $this->orderProduct;
    }
}
