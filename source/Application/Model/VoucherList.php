<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Voucher list manager.
 *
 */
class VoucherList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Calls parent constructor
     */
    public function __construct()
    {
        parent::__construct('oxvoucher');
    }
}
