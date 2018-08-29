<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin admin_pricealarm manager.
 * Returns template, that arranges two other templates ("apricealarm_list.tpl"
 * and "pricealarm_main.tpl") to frame.
 * Admin Menu: Customer Info -> admin_pricealarm.
 */
class AdminPriceAlarm extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Default active tab number
     *
     * @var int
     */
    protected $_iDefEdit = 1;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_pricealarm.tpl';
}
