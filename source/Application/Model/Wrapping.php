<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Wrapping manager.
 * Performs Wrapping data/objects loading, deleting.
 */
class Wrapping extends \OxidEsales\Eshop\Core\Model\MultiLanguageModel
{
    /**
     * Class name.
     *
     * @var string name of current class
     */
    protected $_sClassName = 'oxwrapping';

    /**
     * Wrapping oxprice object.
     *
     * @var \OxidEsales\Eshop\Core\Price
     */
    protected $_oPrice = null;

    /**
     * Wrapping Vat.
     *
     * @var float
     */
    protected $_dVat = 0;

    /**
     * Wrapping VAT config.
     *
     * @var bool
     */
    protected $_blWrappingVatOnTop = false;

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()), loads
     * base shop objects.
     */
    public function __construct()
    {
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $this->setWrappingVat($oConfig->getConfigParam('dDefaultVAT'));
        $this->setWrappingVatOnTop($oConfig->getConfigParam('blWrappingVatOnTop'));
        parent::__construct();
        $this->init('oxwrapping');
    }

    /**
     * Wrapping Vat setter.
     *
     * @param float $dVat vat
     */
    public function setWrappingVat($dVat): void
    {
        $this->_dVat = $dVat;
    }

    /**
     * Wrapping VAT config setter.
     *
     * @param bool $blOnTop wrapping vat config
     */
    public function setWrappingVatOnTop($blOnTop): void
    {
        $this->_blWrappingVatOnTop = $blOnTop;
    }

    /**
     * Returns oxprice object for wrapping.
     *
     * @param int $dAmount article amount
     *
     * @return \OxidEsales\Eshop\Core\Price
     */
    public function getWrappingPrice($dAmount = 1)
    {
        if (null === $this->_oPrice) {
            $this->_oPrice = oxNew(\OxidEsales\Eshop\Core\Price::class);

            if (!$this->_blWrappingVatOnTop) {
                $this->_oPrice->setBruttoPriceMode();
            } else {
                $this->_oPrice->setNettoPriceMode();
            }

            $oCur = \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject();
            $this->_oPrice->setPrice($this->oxwrapping__oxprice->value * $oCur->rate, $this->_dVat);
            $this->_oPrice->multiply($dAmount);
        }

        return $this->_oPrice;
    }

    /**
     * Loads wrapping list for specific wrap type.
     *
     * @param string $sWrapType wrap type
     *
     * @return array wrapping list
     */
    public function getWrappingList($sWrapType)
    {
        // load wrapping
        $oEntries = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
        $oEntries->init('oxwrapping');
        $sWrappingViewName = getViewName('oxwrapping');
        $sSelect = "select * from $sWrappingViewName 
            where $sWrappingViewName.oxactive = :oxactive
              and $sWrappingViewName.oxtype = :oxtype";
        $oEntries->selectString($sSelect, [
            ':oxactive' => '1',
            ':oxtype' => $sWrapType,
        ]);

        return $oEntries;
    }

    /**
     * Counts amount of wrapping/card options.
     *
     * @param string $sWrapType type - wrapping paper (WRAP) or card (CARD)
     *
     * @return int
     */
    public function getWrappingCount($sWrapType)
    {
        $sWrappingViewName = getViewName('oxwrapping');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQ = "select count(*) from $sWrappingViewName 
            where $sWrappingViewName.oxactive = :oxactive 
              and $sWrappingViewName.oxtype = :oxtype";

        return (int)$oDb->getOne($sQ, [
            ':oxactive' => '1',
            ':oxtype' => $sWrapType,
        ]);
    }

    /**
     * Checks and return true if price view mode is netto.
     *
     * @return bool
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "isPriceViewModeNetto" in next major
     */
    protected function _isPriceViewModeNetto() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $blResult = (bool)\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blShowNetPrice');
        $oUser = $this->getUser();
        if ($oUser) {
            $blResult = $oUser->isPriceViewModeNetto();
        }

        return $blResult;
    }

    /**
     * Returns formatted wrapping price.
     *
     * @deprecated since v5.1 (2013-10-13); use oxPrice smarty plugin for formatting in templates
     *
     * @return string
     */
    public function getFPrice()
    {
        $dPrice = $this->getPrice();

        return \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($dPrice, \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject());
    }

    /**
     * Gets price.
     *
     * @return float
     */
    public function getPrice()
    {
        if ($this->_isPriceViewModeNetto()) {
            $dPrice = $this->getWrappingPrice()->getNettoPrice();
        } else {
            $dPrice = $this->getWrappingPrice()->getBruttoPrice();
        }

        return $dPrice;
    }

    /**
     * Returns returns dyn image dir (not ssl).
     *
     * @return string
     */
    public function getNoSslDynImageDir()
    {
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getPictureUrl(null, false, false, null, $this->oxwrapping__oxshopid->value);
    }

    /**
     * Returns returns dyn image dir.
     *
     * @return string
     */
    public function getPictureUrl()
    {
        if ($this->oxwrapping__oxpic->value) {
            return \OxidEsales\Eshop\Core\Registry::getConfig()->getPictureUrl('master/wrapping/' . $this->oxwrapping__oxpic->value, false, \OxidEsales\Eshop\Core\Registry::getConfig()->isSsl(), null, $this->oxwrapping__oxshopid->value);
        }
    }
}
