<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article discount manager.
 * Returns template, that arranges two other templates ("discount_list"
 * and "discount_main") to frame.
 * Admin Menu: Shop Settings -> Discounts.
 */
class DiscountController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'discount';
}
