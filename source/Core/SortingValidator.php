<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * @internal Please do not use or extend this class.
 */
class SortingValidator
{
    /**
     * @param string $sortBy
     * @param string $sortOrder
     * @return bool
     */
    public function isValid($sortBy, $sortOrder)
    {
        $isValid = false;
        if ($sortBy
            && $sortOrder
            && in_array(strtolower($sortOrder), $this->getSortingOrders())
            && in_array($sortBy, \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aSortCols'))
        ) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * @return array
     */
    public function getSortingOrders()
    {
        return ['desc', 'asc'];
    }
}
