<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */


/**
 * Country manager
 *
 */
class oxCountry extends oxI18n
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
        $sQ = "select * from {$sViewName} where `oxcountryid` = '$sCountryId' order by `oxtitle`  ";
        $this->_aStates = oxNew("oxlist");
        $this->_aStates->init("oxstate");
        $this->_aStates->selectString($sQ);

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
        $oDb = oxDb::getDb();

        return $oDb->getOne("select oxid from oxcountry where oxisoalpha2 = " . $oDb->quote($sCode));
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
