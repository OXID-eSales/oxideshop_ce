<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin usergroup manager.
 * Returns template, that arranges two other templates ("usergroup_list.tpl"
 * and "usergroup_main.tpl") to frame.
 * Admin Menu: User Administration -> User Groups.
 */
class UserGroupController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'usergroup.tpl';
}
