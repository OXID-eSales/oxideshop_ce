<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * @internal please do not use or extend this class
 */
class SortingValidator
{
    /**
     * @param string $sortBy
     * @param string $sortOrder
     *
     * @return bool
     */
    public function isValid($sortBy, $sortOrder)
    {
        $isValid = false;
        if (
            $sortBy
            && $sortOrder
            && \in_array(strtolower($sortOrder), $this->getSortingOrders(), true)
            && \in_array($sortBy, \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aSortCols'), true)
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
