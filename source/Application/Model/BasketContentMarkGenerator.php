<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Class oxBasketContentMarkGenerator which forms explanation marks.
 */
class BasketContentMarkGenerator
{
    /**
     * Default value for explanation mark.
     */
    const DEFAULT_EXPLANATION_MARK = '**';

    /**
     * Marks added to array by article type.
     *
     * @var array
     */
    private $_aMarks;

    /**
     * Basket that is used to get article type(downloadable, intangible etc..).
     *
     * @var \OxidEsales\Eshop\Application\Model\Basket
     */
    private $_oBasket;

    /**
     * Sets basket that is used to get article type(downloadable, intangible etc..).
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket
     */
    public function __construct(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        $this->_oBasket = $oBasket;
    }

    /**
     * Returns explanation mark by given mark identification (skippedDiscount, downloadable, intangible).
     *
     * @param string $sMarkIdentification Mark identification.
     *
     * @return string
     */
    public function getMark($sMarkIdentification)
    {
        if (is_null($this->_aMarks)) {
            $sCurrentMark = self::DEFAULT_EXPLANATION_MARK;
            $aMarks = $this->_formMarks($sCurrentMark);
            $this->_aMarks = $aMarks;
        }

        return $this->_aMarks[$sMarkIdentification];
    }

    /**
     * Basket that is used to get article type(downloadable, intangible etc..).
     *
     * @return \OxidEsales\Eshop\Application\Model\Basket
     * @deprecated underscore prefix violates PSR12, will be renamed to "getBasket" in next major
     */
    private function _getBasket() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->_oBasket;
    }

    /**
     * Forms marks for articles.
     *
     * @param string $sCurrentMark Current mark.
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "formMarks" in next major
     */
    private function _formMarks($sCurrentMark) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oBasket = $this->_getBasket();
        $aMarks = [];
        if ($oBasket->hasSkipedDiscount()) {
            $aMarks['skippedDiscount'] = $sCurrentMark;
            $sCurrentMark .= '*';
        }
        if ($oBasket->hasArticlesWithDownloadableAgreement()) {
            $aMarks['downloadable'] = $sCurrentMark;
            $sCurrentMark .= '*';
        }
        if ($oBasket->hasArticlesWithIntangibleAgreement()) {
            $aMarks['intangible'] = $sCurrentMark;
        }

        return $aMarks;
    }
}
