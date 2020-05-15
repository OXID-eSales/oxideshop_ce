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
 * @deprecated 6.5.6 "News" feature will be removed completely
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
