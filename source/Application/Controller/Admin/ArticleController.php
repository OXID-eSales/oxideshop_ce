<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Sets template, that arranges two other templates ("article_list.tpl"
 * and "article_main.tpl") to frame.
 * Admin Menu: Manage Products -> Articles.
 */
class ArticleController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'article.tpl';
}
