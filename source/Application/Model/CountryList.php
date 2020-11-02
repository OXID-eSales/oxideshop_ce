<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Country list manager class.
 * Collects a list of countries according to collection rules (active).
 */
class CountryList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Call parent class constructor.
     */
    public function __construct()
    {
        parent::__construct('oxcountry');
    }

    /**
     * Selects and loads all active countries.
     *
     * @param int $iLang language
     */
    public function loadActiveCountries($iLang = null): void
    {
        $sViewName = getViewName('oxcountry', $iLang);
        $sSelect = "SELECT oxid, oxtitle, oxisoalpha2 FROM {$sViewName} WHERE oxactive = '1' ORDER BY oxorder, oxtitle ";
        $this->selectString($sSelect);
    }
}
