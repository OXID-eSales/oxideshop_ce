<?php

/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * Article box widget
 */
class oxwArticleBox extends oxWidget
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     * @var array
     */
    protected $_aComponentNames = array( 'oxcmp_user' => 1, 'oxcmp_basket' => 1 );

    /**
     * Current class template name.
     * @var string
     */
    protected $_sTemplate = 'widget/product/boxproduct.tpl';


    /**
     * Current article
     * @var null
     */
    protected $_oArticle = null;

    /**
     * Renders template based on widget type or just use directly passed path of template
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $sWidgetType = $this->getViewParameter('sWidgetType');
        $sListType   = $this->getViewParameter('sListType');

        if ($sWidgetType && $sListType) {
            $this->_sTemplate = "widget/" . $sWidgetType . "/" . $sListType . ".tpl";
        }

        $sForceTemplate = $this->getViewParameter('oxwtemplate');
        if ($sForceTemplate) {
            $this->_sTemplate = $sForceTemplate;
        }

        return $this->_sTemplate;
    }

    /**
     * Get product article
     *
     * @return oxArticle
     */
    public function getBoxProduct()
    {
        if ( is_null( $this->_oArticle ) ) {
        $sId = $this->getViewParameter('anid');
        $iLinkType = $this->getViewParameter('iLinkType');
        $sAddDynParams = $this->getConfig()->getTopActiveView()->getAddUrlParams();


            /** @var oxArticle $oArticle */
        $oArticle = oxNew( 'oxArticle' );
        $oArticle->load($sId);
        if ( $sAddDynParams ) {
            $blSeo = oxRegistry::getUtils()->seoIsActive();
            if ( !$blSeo ) {
                // only if seo is off..
                $oArticle->appendStdLink( $sAddDynParams );
            }
            $oArticle->appendLink( $sAddDynParams );
        }

        if ( $iLinkType ) {
            $oArticle->setLinkType( $iLinkType );
        }

        if ( $oRecommList = $this->getActiveRecommList() ) {
            $oArticle->text = $oRecommList->getArtDescription( $oArticle->getId() );
        }
            $this->_oArticle = $oArticle;
        }

        return $this->_oArticle;
    }

    /**
     * get link of current top view
     *
     * @param int $iLang requested language
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        return $this->getConfig()->getTopActiveView()->getLink( $iLang );
    }

    /**
     * Returns if VAT is included in price
     *
     * @return bool
     */
    public function isVatIncluded()
    {
        return (bool) $this->getViewParameter( "isVatIncluded" );
    }

    /**
     * Returns active currency sign
     *
     * @return string
     */
    public function getActCurrencySign()
    {
        return $this->getViewParameter( "currencySign" );
    }

    /**
     * Returns wishlist id
     *
     * @return string
     */
    public function getWishId()
    {
        return $this->getViewParameter('owishid');
    }

    /**
     * Returns remove function
     *
     * @return string
     */
    public function getRemoveFunction()
    {
        return $this->getViewParameter('removeFunction');
    }

    /**
     * Returns toBasket function
     *
     * @return string
     */
    public function getToBasketFunction()
    {
        return $this->getViewParameter('toBasketFunction');
    }

    /**
     * Returns if toCart must be disabled
     *
     * @return bool
     */
    public function getDisableToCart()
    {
        return (bool) $this->getViewParameter('blDisableToCart');
    }

    /**
     * Returns list item id with identifier
     *
     * @return string
     */
    public function getTestId()
    {
        return $this->getViewParameter('testid');
    }

    /**
     * Returns recommendation id
     *
     * @return string
     */
    public function getRecommId()
    {
        return $this->getViewParameter('recommid');
    }

    /**
     * Returns iteration number
     *
     * @return string
     */
    public function getIteration()
    {
        return $this->getViewParameter('iIteration');
    }

    /**
     * Returns RSS links
     *
     * @return array|null
     */
    public function getRSSLinks()
    {
        $aRSS = $this->getViewParameter('rsslinks');
        if ( !is_array($aRSS) ) {
            $aRSS = null;
        }
        return $aRSS;
    }

    /**
     * Returns the answer if main link must be showed
     *
     * @return bool
     */
    public function getShowMainLink()
    {
        return (bool) $this->getViewParameter('showMainLink');
    }

    /**
     * Returns if alternate product exists
     *
     * @return bool
     */
    public function getAltProduct()
    {
        return (bool) $this->getViewParameter('altproduct');
    }

}