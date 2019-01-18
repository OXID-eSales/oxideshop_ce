<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article categories text manager.
 * Returns template, that arranges two other templates ("category_list.tpl"
 * and "category_main.tpl") to frame.
 * Admin Menu: Manage Products -> Categories.
 */
class CategoryController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'category.tpl';
}
