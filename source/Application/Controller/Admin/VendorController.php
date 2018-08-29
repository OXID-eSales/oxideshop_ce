<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Returns template, that arranges two other templates ("vendor_list.tpl"
 * and "vendor_main.tpl") to frame.
 * Admin Menu: Settings -> Vendors
 */
class VendorController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'vendor.tpl';
}
