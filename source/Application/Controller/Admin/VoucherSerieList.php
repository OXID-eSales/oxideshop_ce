<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin voucherserie list manager.
 * Collects voucherserie base information (serie no., discount, valid from, etc.),
 * there is ability to filter them by deiscount, serie no. or delete them.
 * Admin Menu: Shop Settings -> Vouchers.
 */
class VoucherSerieList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxvoucherserie';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'voucherserie_list.tpl';

    /**
     * Deletes selected Voucherserie.
     */
    public function deleteEntry()
    {
        // first we remove vouchers
        $oVoucherSerie = oxNew(\OxidEsales\Eshop\Application\Model\VoucherSerie::class);
        $oVoucherSerie->load($this->getEditObjectId());
        $oVoucherSerie->deleteVoucherList();

        parent::deleteEntry();
    }
}
