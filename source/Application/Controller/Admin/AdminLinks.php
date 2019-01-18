<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admins links manager.
 * Sets template, that arranges two other templates ("adminlinks_lis.tpl"
 * and "adminlinks_main.tpl") to frame.
 * Admin Menu: Customer Info -> Links.
 */
class AdminLinks extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_links.tpl';
}
