<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;

/**
 * Defines an element of multidimentional variant name tree structure. Contains article id, variant name, URL, price, price text, and a subset of MD variants.
 *
 */
class MdVariant extends \OxidEsales\Eshop\Core\Base
{
    /**
     * MD variant identifier
     *
     * @var string
     */
    protected $_sId;

    /**
     * Parent ID
     *
     * @var string
     */
    protected $_sParentId;

    /**
     * Corresponding article id
     *
     * @var string
     */
    protected $_sArticleId;

    /**
     * Variant name
     *
     * @var string
     */
    protected $_sName;

    /**
     * Variant URL
     *
     * @var string
     */
    protected $_sUrl;

    /**
     * Variant price
     *
     * @var double
     */
    protected $_dPrice = null;

    /**
     * Variant Price text represenatation. Eg. "10,00 EUR" or "from 8,00 EUR"
     *
     * @var string
     */
    protected $_sFPrice;

    /**
     * Subvariant array
     *
     * @var array[string]oxMdVariant
     */
    protected $_aSubvariants = [];

    /**
     * Sets MD variant identifier
     *
     * @param string $sId New id
     */
    public function setId($sId)
    {
        $this->_sId = $sId;
    }

    /**
     * Returns MD variant identifier
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * Sets parent id
     *
     * @param string $sParentId Parent id
     */
    public function setParentId($sParentId)
    {
        $this->_sParentId = $sParentId;
    }

    /**
     * Returns parent id
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->_sParentId;
    }

    /**
     * Sets MD subvariants
     *
     * @param \OxidEsales\Eshop\Application\Model\MdVariant[] $aSubvariants Subvariants
     */
    public function setMdSubvariants($aSubvariants)
    {
        $this->_aSubvariants = $aSubvariants;
    }

    /**
     * Returns full array of subvariants
     *
     * @return array[string]OxMdSubvariants
     */
    public function getMdSubvariants()
    {
        return $this->_aSubvariants;
    }

    /**
     * Returns first MD subvariant from subvariant set or null in case variant has no subvariants.
     *
     * @return \OxidEsales\Eshop\Application\Model\MdVariant
     */
    public function getFirstMdSubvariant()
    {
        $aMdSubvariants = $this->getMdSubvariants();
        if (count($aMdSubvariants)) {
            return reset($aMdSubvariants);
        }

        return null;
    }

    /**
     * Checks for existing MD subvariant by name. Returns existing one or in case $sName has not been found creates an empty OxMdVariant instance.
     *
     * @param string $sName Subvariant name
     *
     * @return \OxidEsales\Eshop\Application\Model\MdVariant
     */
    public function getMdSubvariantByName($sName)
    {
        $aSubvariants = $this->getMdSubvariants();
        foreach ($aSubvariants as $oMdSubvariant) {
            if (strcasecmp($oMdSubvariant->getName(), $sName) == 0) {
                return $oMdSubvariant;
            }
        }

        $oNewSubvariant = oxNew(\OxidEsales\Eshop\Application\Model\MdVariant::class);
        $oNewSubvariant->setName($sName);
        $oNewSubvariant->setId(md5($sName . $this->getId()));
        $oNewSubvariant->setParentId($this->getId());
        $this->_addMdSubvariant($oNewSubvariant);

        return $oNewSubvariant;
    }

    /**
     * Returns corresponding article URL or recusively first variant URL from subvariant set
     *
     * @return string
     */
    public function getLink()
    {
        $oFirstSubvariant = $this->getFirstMdSubvariant();
        if ($oFirstSubvariant) {
            return $oFirstSubvariant->getLink();
        }

        return $this->_sUrl;
    }

    /**
     * Name setter
     *
     * @param string $sName New name
     */
    public function setName($sName)
    {
        $this->_sName = $sName;
    }

    /**
     * Returns MD variant name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * Returns price
     *
     * @return double
     */
    public function getDPrice()
    {
        return $this->_dPrice;
    }

    /**
     * Returns min price recursively selected from full subvariant tree.
     *
     * @return double
     */
    public function getMinDPrice()
    {
        $dMinPrice = $this->getDPrice();
        $aVariants = $this->getMdSubvariants();
        foreach ($aVariants as $oVariant) {
            $dMinVariantPrice = $oVariant->getMinDPrice();
            if (is_null($dMinPrice)) {
                $dMinPrice = $dMinVariantPrice;
            }
            if (!is_null($dMinVariantPrice) && $dMinVariantPrice < $dMinPrice) {
                $dMinPrice = $dMinVariantPrice;
            }
        }

        return $dMinPrice;
    }

    /**
     * Gets max subvariant depth. 0 means no deeper subvariants.
     *
     * @return int
     */
    public function getMaxDepth()
    {
        $aSubvariants = $this->getMdSubvariants();

        if (!count($aSubvariants)) {
            return 0;
        }

        $iMaxDepth = 0;
        foreach ($aSubvariants as $oSubvariant) {
            if ($oSubvariant->getMaxDepth() > $iMaxDepth) {
                $iMaxDepth = $oSubvariant->getMaxDepth();
            }
        }

        return $iMaxDepth + 1;
    }

    /**
     * Returns MD variant price as a text.
     *
     * @return string
     */
    public function getFPrice()
    {
        $myConfig = $this->getConfig();
        // 0002030 No need to return price if it disabled for better performance.
        if (!$myConfig->getConfigParam('bl_perfLoadPrice')) {
            return;
        }

        if ($this->_sFPrice) {
            return $this->_sFPrice;
        }

        $sFromPrefix = '';

        if (!$this->_isFixedPrice()) {
            $sFromPrefix = \OxidEsales\Eshop\Core\Registry::getLang()->translateString('PRICE_FROM') . ' ';
        }

        $dMinPrice = $this->getMinDPrice();
        $sFMinPrice = \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($dMinPrice);
        $sCurrency = ' ' . $this->getConfig()->getActShopCurrencyObject()->sign;
        $this->_sFPrice = $sFromPrefix . $sFMinPrice . $sCurrency;

        return $this->_sFPrice;
    }

    /**
     * Inits MD variant by name. In case $aNames parameter has more than one element addNames recursively adds names for subvariants.
     *
     * @param string $sArtId Article ID
     * @param array  $aNames Expected array of $sKey=>$sName pairs.
     * @param double $dPrice Price as double
     * @param string $sUrl   Article URL
     */
    public function addNames($sArtId, $aNames, $dPrice, $sUrl)
    {
        $iCount = count($aNames);
        $sName = array_shift($aNames);

        if ($iCount) {
            //get required subvariant
            $oVariant = $this->getMdSubvariantByName($sName);
            //add remaining names
            $oVariant->addNames($sArtId, $aNames, $dPrice, $sUrl);
        } else {
            //means we have the deepest element and assign other attributes
            $this->_sArticleId = $sArtId;
            $this->_dPrice = $dPrice;
            $this->_sUrl = $sUrl;
        }
    }

    /**
     * Returns corresponding article id or recusively first variant id from subvariant set
     *
     * @return string
     */
    public function getArticleId()
    {
        $oFirstSubvariant = $this->getFirstMdSubvariant();

        if ($oFirstSubvariant) {
            return $oFirstSubvariant->getArticleId();
        }

        return $this->_sArticleId;
    }

    /**
     * Checks whether $sArtId is one of subtree article ids.
     *
     * @param string $sArtId Article ID
     *
     * @return bool
     */
    public function hasArticleId($sArtId)
    {
        if ($this->getArticleId() == $sArtId) {
            return true;
        }

        $aSubvariants = $this->getMdSubvariants();
        foreach ($aSubvariants as $oSubvariant) {
            if ($oSubvariant->hasArticleId($sArtId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds one subvariant to subvariant set
     *
     * @param \OxidEsales\Eshop\Application\Model\MdVariant $oSubvariant Subvariant
     */
    protected function _addMdSubvariant($oSubvariant)
    {
        $this->_aSubvariants[$oSubvariant->getId()] = $oSubvariant;
    }

    /**
     * Checks if variant price is fixed or not ("from" price)
     *
     * @return bool
     */
    protected function _isFixedPrice()
    {
        $dPrice = $this->getDPrice();
        $aVariants = $this->getMdSubvariants();
        foreach ($aVariants as $oVariant) {
            $dVariantPrice = $oVariant->getDPrice();
            if (is_null($dPrice)) {
                $dPrice = $dVariantPrice;
            }
            if (!is_null($dVariantPrice) && $dVariantPrice != $dPrice) {
                return false;
            }
            if (!$oVariant->_isFixedPrice()) {
                return false;
            }
        }

        return true;
    }
}
