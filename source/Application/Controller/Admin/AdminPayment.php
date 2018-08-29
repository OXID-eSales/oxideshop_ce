<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin payment manager.
 * Returns template, that arranges two other templates ("payment_list.tpl"
 * and "payment_main.tpl") to frame.
 * Admin Menu: Shop Settings -> Payment Methods.
 */
class AdminPayment extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_payment.tpl';
}
