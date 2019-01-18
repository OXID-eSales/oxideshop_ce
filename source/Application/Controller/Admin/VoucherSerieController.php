<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin voucherserie manager.
 * Returns template, that arranges two other templates ("voucherserie_list.tpl"
 * and "voucherserie_main.tpl") to frame.
 * Admin Menu: Shop Settings -> Vouchers.
 */
class VoucherSerieController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'voucherserie.tpl';
}
