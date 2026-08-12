<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Discount;
use OxidEsales\Eshop\Application\Model\DiscountList as DiscountListModel;

/**
 * Admin discount list manager.
 * Collects delivery base information (description), there is ability to
 * filter them by description, title or delete them.
 * Admin Menu: Shop Settings -> Discounts.
 */
class DiscountList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'discount_list';

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = Discount::class;

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = DiscountListModel::class;

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxsort';
}
