<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article deliveryset manager.
 * Returns template, that arranges two other templates ("deliveryset_list.tpl"
 * and "deliveryset_main.tpl") to frame.
 * Admin Menu: Shop settings -> Shipping & Handling Sets.
 */
class DeliverySetController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'deliveryset.tpl';
}
