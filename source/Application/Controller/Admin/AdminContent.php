<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Sets template, that arranges two other templates ("content_list.tpl"
 * and "content_main.tpl") to frame.
 * Admin Menu: Customerinformations -> Content.
 */
class AdminContent extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'content.tpl';
}
