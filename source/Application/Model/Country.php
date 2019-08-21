<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Country manager
 *
 */
class Country extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxcountry';

    /**
     * State list
     *
     * @var oxStateList
     */
    protected $_aStates = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxcountry');
    }

    /**
     * returns true if this country is a foreign country
     *
     * @return bool
     */
    public function isForeignCountry()
    {
        return !in_array($this->getId(), $this->getConfig()->getConfigParam('aHomeCountry'));
    }

    /**
     * returns true if this country is marked as EU
     *
     * @return bool
     */
    public function isInEU()
    {
        return (bool) ($this->oxcountry__oxvatstatus->value == 1);
    }

    /**
     * Returns current state list
     *
     * @return array
     */
    public function getStates()
    {
        if (!is_null($this->_aStates)) {
            return $this->_aStates;
        }

        $sCountryId = $this->getId();
        $sViewName = getViewName("oxstates", $this->getLanguage());
        $sQ = "select * from {$sViewName} where `oxcountryid` = :oxcountryid order by `oxtitle`  ";
        $this->_aStates = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $this->_aStates->init("oxstate");
        $this->_aStates->selectString($sQ, [
            ':oxcountryid' => $sCountryId
        ]);

        return $this->_aStates;
    }

    /**
     * Returns country id by code
     *
     * @param string $sCode country code
     *
     * @return string
     */
    public function getIdByCode($sCode)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        return $oDb->getOne("select oxid from oxcountry where oxisoalpha2 = :oxisoalpha2", [
            ':oxisoalpha2' => $sCode
        ]);
    }

    /**
     * Method returns VAT identification number prefix.
     *
     * @return string
     */
    public function getVATIdentificationNumberPrefix()
    {
        return $this->oxcountry__oxvatinprefix->value;
    }
}
