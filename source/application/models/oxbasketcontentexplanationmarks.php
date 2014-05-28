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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Class oxBasketContentExplanationMarks which forms explanation marks.
 */
class oxBasketContentExplanationMarks
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
     * @var oxBasket
     */
    private $_oBasket;

    /**
     * Sets basket that is used to get article type(downloadable, intangible etc..).
     *
     * @param oxBasket $oBasket
     */
    public function __construct(oxBasket $oBasket)
    {
        $this->_oBasket = $oBasket;
    }

    /**
     * Returns explanation mark for articles with skipped discount.
     *
     * @return string
     */
    public function getForArticlesWithSkippedDiscount()
    {
        $aMarks = $this->_getMarks();

        return $aMarks['skippedDiscount'];
    }

    /**
     * Returns explanation mark for downloadable articles.
     *
     * @return string
     */
    public function getForDownloadableArticles()
    {
        $aMarks = $this->_getMarks();

        return $aMarks['downloadable'];
    }

    /**
     * Returns explanation mark for intangible articles.
     *
     * @return string
     */
    public function getForIntangibleArticles()
    {
        $aMarks = $this->_getMarks();

        return $aMarks['intangible'];
    }

    /**
     * Returns array with marks.
     *
     * @return array
     */
    private function _getMarks()
    {
        if (is_null($this->_aMarks)) {
            $sCurrentMark = self::DEFAULT_EXPLANATION_MARK;
            $aMarks = $this->_formMarks($sCurrentMark);
            $this->_aMarks = $aMarks;
        }

        return $this->_aMarks;
    }

    /**
     * Basket that is used to get article type(downloadable, intangible etc..).
     *
     * @return oxBasket
     */
    private function _getBasket()
    {
        return $this->_oBasket;
    }

    /**
     * Forms marks for articles.
     *
     * @param $sCurrentMark
     *
     * @return array
     */
    private function _formMarks($sCurrentMark)
    {
        $oBasket = $this->_getBasket();
        $aMarks = array();
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