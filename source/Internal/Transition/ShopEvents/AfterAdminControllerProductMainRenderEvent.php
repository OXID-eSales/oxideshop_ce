<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\ShopEvents;

use OxidEsales\Eshop\Application\Controller\Admin\ArticleMain;
use OxidEsales\Eshop\Application\Model\Article;
use Symfony\Contracts\EventDispatcher\Event;;

/**
 * @stable
 * @see OxidEsales/EshopCommunity/Internal/README.md
 */
class AfterAdminControllerProductMainRenderEvent extends Event
{
    public const NAME = self::class;

    /** @var ArticleMain */
    private $controller;
    /** @var Article */
    private $product;

    public function __construct(
        ArticleMain $productController,
        Article $product
    ) {
        $this->controller = $productController;
        $this->product = $product;
    }

    /** @return ArticleMain */
    public function getController(): ArticleMain
    {
        return $this->controller;
    }

    /** @return Article */
    public function getProduct(): Article
    {
        return $this->product;
    }
}
