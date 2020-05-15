<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin news list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: Customer Info -> News.
 * @deprecated 6.5.6 "News" feature will be removed completely
 */
class NewsList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'news_list.tpl';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxnews';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxnewslist';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxdate";

    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        $aSorting = parent::getListSorting();
        if (isset($aSorting["oxnews"][$this->_sDefSortField])) {
            $this->_blDesc = true;
        }

        return $aSorting;
    }
}
