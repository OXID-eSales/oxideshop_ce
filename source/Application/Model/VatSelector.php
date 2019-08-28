<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxObjectException;

/**
 * Class, responsible for retrieving correct vat for users and articles
 *
 */
class VatSelector extends \OxidEsales\Eshop\Core\Base
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
    protected static $_aUserVatCache = [];

    /**
     * get VAT for user, can NOT be null
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser        given  user object
     * @param bool                                     $blCacheReset reset cache
     *
     * @throws oxObjectException if wrong country
     * @return double | false
     */
    public function getUserVat(\OxidEsales\Eshop\Application\Model\User $oUser, $blCacheReset = false)
    {
        $cacheId = $oUser->getId() . '_' . $oUser->oxuser__oxcountryid->value;

        if (!$blCacheReset) {
            if (array_key_exists($cacheId, self::$_aUserVatCache) &&
                self::$_aUserVatCache[$cacheId] !== null
            ) {
                return self::$_aUserVatCache[$cacheId];
            }
        }

        $ret = false;

        $sCountryId = $this->_getVatCountry($oUser);

        if ($sCountryId) {
            $oCountry = oxNew(\OxidEsales\Eshop\Application\Model\Country::class);
            if (!$oCountry->load($sCountryId)) {
                throw oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
            }
            if ($oCountry->isForeignCountry()) {
                $ret = $this->_getForeignCountryUserVat($oUser, $oCountry);
            }
        }

        self::$_aUserVatCache[$cacheId] = $ret;

        return $ret;
    }

    /**
     * get vat for user of a foreign country
     *
     * @param \OxidEsales\Eshop\Application\Model\User    $oUser    given user object
     * @param \OxidEsales\Eshop\Application\Model\Country $oCountry given country object
     *
     * @return mixed
     */
    protected function _getForeignCountryUserVat(\OxidEsales\Eshop\Application\Model\User $oUser, \OxidEsales\Eshop\Application\Model\Country $oCountry)
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
     * return Vat value for category type assignment only
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle given article
     *
     * @return float | false
     */
    protected function _getVatForArticleCategory(\OxidEsales\Eshop\Application\Model\Article $oArticle)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
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
                       o2c.oxobjectid = :oxobjectid AND
                       c.oxvat IS NOT NULL
                 ORDER BY o2c.oxtime ";

        $fVat = $oDb->getOne($sSql, [
            ':oxobjectid' => $oArticle->getId()
        ]);
        if ($fVat !== false && $fVat !== null) {
            return $fVat;
        }

        return false;
    }

    /**
     * get VAT for given article, can NOT be null
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle given article
     *
     * @return double
     */
    public function getArticleVat(\OxidEsales\Eshop\Application\Model\Article $oArticle)
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
     * item ( executes \OxidEsales\Eshop\Application\Model\VatSelector::getArticleVat()). Can be used to override
     * basket price calculation behaviour (\OxidEsales\Eshop\Application\Model\Article::getBasketPrice())
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     * @param \OxidEsales\Eshop\Application\Model\Basket  $oBasket  oxbasket object
     *
     * @return double
     */
    public function getBasketItemVat(\OxidEsales\Eshop\Application\Model\Article $oArticle, $oBasket)
    {
        return $this->getArticleVat($oArticle);
    }

    /**
     * get article user vat
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $oArticle article object
     *
     * @return double | false
     */
    public function getArticleUserVat(\OxidEsales\Eshop\Application\Model\Article $oArticle)
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
     * @param \OxidEsales\Eshop\Application\Model\User $oUser user object
     *
     * @return string
     */
    protected function _getVatCountry(\OxidEsales\Eshop\Application\Model\User $oUser)
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
