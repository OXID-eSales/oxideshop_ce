<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

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
    protected $_sListClass = 'oxdeliveryset';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxdeliverysetlist';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'deliveryset_list.tpl';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxpos';
}
