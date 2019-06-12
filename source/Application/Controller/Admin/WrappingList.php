<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin wrapping list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: User Administration -> Users.
 */
class WrappingList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxwrapping';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxname';

    /**
     * Executes parent method parent::render() and returns name of template
     * file "user_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "wrapping_list.tpl";
    }
}
