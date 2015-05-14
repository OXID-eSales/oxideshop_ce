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
 * Class, responsible for retrieving correct vat for users and articles
 *
 */
class oxVatSelector extends oxSuperCfg
{

    /**
     * State is VAT calculation for category is set
     *
     * @var bool
     */
    protected $_blCatVatSet = null;

    /**
     * keeps loaded user Vats for later reusage
     *
     * @var array
     */
    protected static $_aUserVatCache = array();

    /**
     * get VAT for user, can NOT be null
     *
     * @param oxUser $oUser        given user object
     * @param bool   $blCacheReset reset cache
     *
     * @throws oxObjectException if wrong country
     * @return double | false
     */
    public function getUserVat(oxUser $oUser, $blCacheReset = false)
    {
        if (!$blCacheReset) {
            $sId = $oUser->getId();
            if (array_key_exists($sId, self::$_aUserVatCache) &&
                self::$_aUserVatCache[$sId] !== null
            ) {
                return self::$_aUserVatCache[$sId];
            }
        }

        $ret = false;

        $sCountryId = $this->_getVatCountry($oUser);

        if ($sCountryId) {
            $oCountry = oxNew('oxcountry');
            if (!$oCountry->load($sCountryId)) {
                throw oxNew("oxObjectException");
            }
            if ($oCountry->isForeignCountry()) {
                $ret = $this->_getForeignCountryUserVat($oUser, $oCountry);
            }
        }

        self::$_aUserVatCache[$oUser->getId()] = $ret;

        return $ret;
    }

    /**
     * get vat for user of a foreign country
     *
     * @param oxUser    $oUser    given user object
     * @param oxCountry $oCountry given country object
     *
     * @return mixed
     */
    protected function _getForeignCountryUserVat(oxUser $oUser, oxCountry $oCountry)
    {
        if ($oCountry->isInEU()) {
            if ($oUser->oxuser__oxustid->value) {
                return 0;
            }

            return false;
        }

        return 0;
    }

    /**
     * return Vat value for oxcategory type assignment only
     *
     * @param oxArticle $oArticle given article
     *
     * @return float | false
     */
    protected function _getVatForArticleCategory(oxArticle $oArticle)
    {
        $oDb = oxDb::getDb();
        $sCatT = getViewName('oxcategories');

        if ($this->_blCatVatSet === null) {
            $sSelect = "SELECT oxid FROM $sCatT WHERE oxvat IS NOT NULL LIMIT 1";

            //no category specific vats in shop?
            //then for performance reasons we just return false
            $this->_blCatVatSet = (bool) $oDb->getOne($sSelect);
        }

        if (!$this->_blCatVatSet) {
            return false;
        }

        $sO2C = getViewName('oxobject2category');
        $sSql = "SELECT c.oxvat
                 FROM $sCatT AS c, $sO2C AS o2c
                 WHERE c.oxid=o2c.oxcatnid AND
                       o2c.oxobjectid = " . $oDb->quote($oArticle->getId()) . " AND
                       c.oxvat IS NOT NULL
                 ORDER BY o2c.oxtime ";

        $fVat = $oDb->getOne($sSql);
        if ($fVat !== false && $fVat !== null) {
            return $fVat;
        }

        return false;
    }

    /**
     * get VAT for given article, can NOT be null
     *
     * @param oxArticle $oArticle given article
     *
     * @return double
     */
    public function getArticleVat(oxArticle $oArticle)
    {
        startProfile("_assignPriceInternal");
        // article has its own VAT ?

        if (($dArticleVat = $oArticle->getCustomVAT()) !== null) {
            stopProfile("_assignPriceInternal");

            return $dArticleVat;
        }
        if (($dArticleVat = $this->_getVatForArticleCategory($oArticle)) !== false) {
            stopProfile("_assignPriceInternal");

            return $dArticleVat;
        }

        stopProfile("_assignPriceInternal");

        return $this->getConfig()->getConfigParam('dDefaultVAT');
    }

    /**
     * Currently returns vats percent that can be applied for basket
     * item ( executes oxVatSelector::getArticleVat()). Can be used to override
     * basket price calculation behaviour (oxarticle::getBasketPrice())
     *
     * @param object $oArticle article object
     * @param object $oBasket  oxbasket object
     *
     * @return double
     */
    public function getBasketItemVat(oxArticle $oArticle, $oBasket)
    {
        return $this->getArticleVat($oArticle);
    }

    /**
     * get article user vat
     *
     * @param oxArticle $oArticle article object
     *
     * @return double | false
     */
    public function getArticleUserVat(oxArticle $oArticle)
    {
        if (($oUser = $oArticle->getArticleUser())) {
            return $this->getUserVat($oUser);
        }

        return false;
    }


    /**
     * Returns country id which VAT should be applied to.
     * Depending on configuration option either user billing country or shipping country (if available) is returned.
     *
     * @param oxUser $oUser user object
     *
     * @return string
     */
    protected function _getVatCountry(oxUser $oUser)
    {
        $blUseShippingCountry = $this->getConfig()->getConfigParam("blShippingCountryVat");

        if ($blUseShippingCountry) {
            $aAddresses = $oUser->getUserAddresses($oUser->getId());
            $sSelectedAddress = $oUser->getSelectedAddressId();

            if (isset($aAddresses[$sSelectedAddress])) {
                return $aAddresses[$sSelectedAddress]->oxaddress__oxcountryid->value;
            }
        }

        return $oUser->oxuser__oxcountryid->value;
    }
}
