<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin news manager.
 * Returns template, that arranges two other templates ("news_list.tpl"
 * and "news_main.tpl") to frame.
 * Admin Menu: Customer Info -> News.
 *
 * @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a module in v6.0.0
 *
 */
class AdminNews extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_news.tpl';
}
