<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Groups;

/**
 * Admin usergroup list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: User Administration -> User Groups.
 */
class UserGroupList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = Groups::class;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxtitle";

    /**
     * Executes parent method parent::render() and returns name of template
     * file "usergroup_list".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "usergroup_list";
    }
}
