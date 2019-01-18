<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin selectlist manager.
 * Returns template, that arranges two other templates ("selectlist_list.tpl"
 * and "selectlist_main.tpl") to frame.
 */
class SelectListController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'selectlist.tpl';
}
