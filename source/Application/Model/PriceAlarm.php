<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * PriceAlarm manager.
 * Performs PriceAlarm data/objects loading, deleting.
 */
class PriceAlarm extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * Current class name.
     *
     * @var string
     */
    protected $_sClassName = 'oxpricealarm';

    /**
     * Article object.
     *
     * @var object
     */
    protected $_oArticle = null;

    /**
     * Formatted original article price.
     *
     * @var string
     */
    protected $_fPrice = null;

    /**
     * Original article price.
     *
     * @var float
     */
    protected $_dPrice = null;

    /**
     * Full article title.
     *
     * @var string
     */
    protected $_sTitle = null;

    /**
     * Currency object.
     *
     * @var object
     */
    protected $_oCurrency = null;

    /**
     * Customer proposed price.
     *
     * @var string
     */
    protected $_fProposedPrice = null;

    /**
     * PriceAlarm status.
     *
     * @var int
     */
    protected $_iStatus = null;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()), loads
     * base shop objects.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxpricealarm');
    }

    /**
     * Inserts object data into DB, returns true on success.
     *
     * @return bool
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "insert" in next major
     */
    protected function _insert() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        // set oxinsert value
        $this->oxpricealarm__oxinsert = new \OxidEsales\Eshop\Core\Field(date('Y-m-d', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime()));

        return parent::_insert();
    }

    /**
     * Loads pricealarm article.
     *
     * @return object
     */
    public function getArticle()
    {
        if (null === $this->_oArticle) {
            $this->_oArticle = false;
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            if ($oArticle->load($this->oxpricealarm__oxartid->value)) {
                $this->_oArticle = $oArticle;
            }
        }

        return $this->_oArticle;
    }

    /**
     * Returns formatted pricealarm article original price.
     *
     * @return string
     */
    public function getFPrice()
    {
        if (null === $this->_fPrice) {
            $this->_fPrice = false;
            if ($dArtPrice = $this->getPrice()) {
                $myLang = \OxidEsales\Eshop\Core\Registry::getLang();
                $oThisCurr = $this->getPriceAlarmCurrency();
                $this->_fPrice = $myLang->formatCurrency($dArtPrice, $oThisCurr);
            }
        }

        return $this->_fPrice;
    }

    /**
     * Returns pricealarm article original price.
     *
     * @return float
     */
    public function getPrice()
    {
        if (null === $this->_dPrice) {
            $this->_dPrice = false;
            if ($oArticle = $this->getArticle()) {
                $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
                $oThisCurr = $this->getPriceAlarmCurrency();

                // #889C - Netto prices in Admin
                // (we have to call $oArticle->getPrice() to get price with VAT)
                $dArtPrice = $oArticle->getPrice()->getBruttoPrice() * $oThisCurr->rate;
                $dArtPrice = $myUtils->fRound($dArtPrice);

                $this->_dPrice = $dArtPrice;
            }
        }

        return $this->_dPrice;
    }

    /**
     * Returns pricealarm article full title.
     *
     * @return string
     */
    public function getTitle()
    {
        if (null === $this->_sTitle) {
            $this->_sTitle = false;
            if ($oArticle = $this->getArticle()) {
                $this->_sTitle = $oArticle->oxarticles__oxtitle->value;
                if ($oArticle->oxarticles__oxparentid->value && !$oArticle->oxarticles__oxtitle->value) {
                    $oParent = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                    $oParent->load($oArticle->oxarticles__oxparentid->value);
                    $this->_sTitle = $oParent->oxarticles__oxtitle->value . ' ' . $oArticle->oxarticles__oxvarselect->value;
                }
            }
        }

        return $this->_sTitle;
    }

    /**
     * Returns pricealarm currency object.
     *
     * @return object
     */
    public function getPriceAlarmCurrency()
    {
        if (null === $this->_oCurrency) {
            $this->_oCurrency = false;
            $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
            $oThisCurr = $myConfig->getCurrencyObject($this->oxpricealarm__oxcurrency->value);

            // #869A we should perform currency conversion
            // (older versions doesn't have currency info - assume as it is default - first in currency array)
            if (!$oThisCurr) {
                $oDefCurr = $myConfig->getActShopCurrencyObject();
                $oThisCurr = $myConfig->getCurrencyObject($oDefCurr->name);
                $this->oxpricealarm__oxcurrency->setValue($oDefCurr->name);
            }
            $this->_oCurrency = $oThisCurr;
        }

        return $this->_oCurrency;
    }

    /**
     * Returns formatted proposed price.
     *
     * @return string
     */
    public function getFProposedPrice()
    {
        if (null === $this->_fProposedPrice) {
            $this->_fProposedPrice = false;
            if ($oThisCurr = $this->getPriceAlarmCurrency()) {
                $myLang = \OxidEsales\Eshop\Core\Registry::getLang();
                $this->_fProposedPrice = $myLang->formatCurrency($this->oxpricealarm__oxprice->value, $oThisCurr);
            }
        }

        return $this->_fProposedPrice;
    }

    /**
     * Returns pricealarm status.
     *
     * @return int
     */
    public function getPriceAlarmStatus()
    {
        if (null === $this->_iStatus) {
            // neutral status
            $this->_iStatus = 0;

            // shop price is less or equal
            $dArtPrice = $this->getPrice();
            if ($this->oxpricealarm__oxprice->value >= $dArtPrice) {
                $this->_iStatus = 1;
            }

            // suggestion to user is sent
            if ('0000-00-00 00:00:00' !== $this->oxpricealarm__oxsended->value) {
                $this->_iStatus = 2;
            }
        }

        return $this->_iStatus;
    }
}
