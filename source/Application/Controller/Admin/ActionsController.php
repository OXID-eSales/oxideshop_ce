<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Sets view template, that arranges two other templates ("actions_list.tpl"
 * and "actions_main.tpl") to frame.
 * Admin Menu: Manage Products -> Actions.
 */
class ActionsController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'actions.tpl';
}
