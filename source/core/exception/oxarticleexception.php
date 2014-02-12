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
 * Exception base class for an article
 */
class oxArticleException extends oxException
{
    /**
     * Article number who caused this exception
     *
     * @var string
     */
    protected $_sArticleNr = null;

    /**
     * Id of product which caused this exception
     *
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Sets the article number of the article which caused the exception
     *
     * @param string $sArticleNr Article who causes the exception
     *
     * @return null
     */
    public function setArticleNr($sArticleNr)
    {
        $this->_sArticleNr = $sArticleNr;
    }

    /**
     * The article number of the faulty article
     *
     * @return string
     */
    public function getArticleNr()
    {
        return $this->_sArticleNr;
    }

    /**
     * Sets the product id of the article which caused the exception
     *
     * @param string $sProductId id of product who causes the exception
     *
     * @return null
     */
    public function setProductId( $sProductId )
    {
        $this->_sProductId = $sProductId;
    }

    /**
     * Faulty product id
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->_sProductId;
    }

    /**
     * Get string dump
     * Overrides oxException::getString()
     *
     * @return string
     */
    public function getString()
    {
        return __CLASS__ .'-'.parent::getString()." Faulty Article --> ".$this->_sArticleNr . "\n";
    }


    /**
     * Override of oxException::getValues()
     *
     * @return array
     */
    public function getValues()
    {
        $aRes = parent::getValues();
        $aRes['articleNr'] = $this->getArticleNr();
        $aRes['productId'] = $this->getProductId();
        return $aRes;
    }
}
