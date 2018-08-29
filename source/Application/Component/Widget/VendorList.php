<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Vendor list widget.
 * Forms vendor list.
 */
class VendorList extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/footer/vendorlist.tpl';

    /**
     * Template variable getter. Returns vendorlist for search
     *
     * @return array
     */
    public function getVendorlist()
    {
        if ($this->_aVendorlist === null) {
            $oVendorTree = oxNew(\OxidEsales\Eshop\Application\Model\VendorList::class);
            $oVendorTree->buildVendorTree('vendorlist', null, $this->getConfig()->getShopHomeUrl());
            $this->_aVendorlist = $oVendorTree;
        }

        return $this->_aVendorlist;
    }
}
