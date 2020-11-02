<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Interface for country getter and setter.
 */
interface ICountryAware
{
    /**
     * Country setter.
     */
    public function setCountry(\OxidEsales\Eshop\Application\Model\Country $oCountry);

    /**
     * Country getter.
     *
     * @return \OxidEsales\Eshop\Application\Model\Country
     */
    public function getCountry();
}
