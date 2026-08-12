<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\DeliverySet;
use OxidEsales\Eshop\Application\Model\DeliverySetList as DeliverySetListModel;

/**
 * Admin deliverysetset list manager.
 * Collects deliveryset base information (description), there is ability to
 * filter them by description, title or delete them.
 * Admin Menu: Shop Settings -> Shipping & Handling Sets.
 */
class DeliverySetList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = DeliverySet::class;

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = DeliverySetListModel::class;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'deliveryset_list';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxpos';
}
