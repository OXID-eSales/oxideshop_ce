<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * State handler
 */
class State extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxstate';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init("oxstates");
    }

    /**
     * Returns country id by code
     *
     * @param string $sCode      country code
     * @param string $sCountryId country id
     *
     * @return string
     */
    public function getIdByCode($sCode, $sCountryId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $params = [
            ':oxisoalpha2' => $sCode,
            ':oxcountryid' => $sCountryId
        ];

        return $oDb->getOne("SELECT oxid FROM oxstates 
            WHERE oxisoalpha2 = :oxisoalpha2 
              AND oxcountryid = :oxcountryid", $params);
    }

    /**
     * Get state title by id
     *
     * @param integer|string $iStateId
     *
     * @return string
     */
    public function getTitleById($iStateId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "SELECT oxtitle FROM " . getViewName("oxstates") . " 
            WHERE oxid = :oxid";

        $sStateTitle = $oDb->getOne($sQ, [
            ':oxid' => $iStateId
        ]);

        return (string) $sStateTitle;
    }
}
