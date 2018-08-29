<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin order manager.
 * Returns template, that arranges two other templates ("order_list.tpl"
 * and "order_overview.tpl") to frame.
 * Admin Menu: Orders -> Display Orders.
 */
class AdminOrder extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'order.tpl';
}
