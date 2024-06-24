<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Model\Article;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterProductSaveEvent extends Event
{
    use ModelChangeEventTrait;

    const NAME = self::class;

    /** @var Article */
    private $product;

    public function __construct(Article $product)
    {
        $this->product = $product;
    }

    /** @return Article */
    public function getProduct(): Article
    {
        return $this->product;
    }
}
