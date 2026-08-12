<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Mini basket widget
 */
class MiniBasket extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = [
        \OxidEsales\Eshop\Application\Component\CurrencyComponent::class => 1,
        \OxidEsales\Eshop\Application\Component\BasketComponent::class  => 1,
        \OxidEsales\Eshop\Application\Component\UserComponent::class    => 1,
    ];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/header/minibasket';
}
