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
 * Article suggestion page.
 * Collects some article base information, sets default recomendation text,
 * sends suggestion mail to user.
 */
class RecommList extends aList
{
    /**
     * List type
     * @var string
     */
    protected $_sListType = 'recommlist';

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/recommendations/recommlist.tpl';

    /**
     * Other recommendations list
     * @var oxrecommlist
     */
    protected $_oOtherRecommList = null;

    /**
     * Recommlist reviews
     * @var array
     */
    protected $_aReviews = null;

    /**
     * Can user rate
     * @var bool
     */
    protected $_blRate = null;

    /**
     * Rating value
     * @var double
     */
    protected $_dRatingValue = null;

    /**
     * Ratng count
     * @var integer
     */
    protected $_iRatingCnt = null;

    /**
     * Searched recommendations list
     * @var object
     */
    protected $_oSearchRecommLists = null;

    /**
     * Search string
     * @var string
     */
    protected $_sSearch = null;

    /**
     * Template location
     *
     * @var string
     */
    protected $_sTplLocation = null;

    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;


    /**
     * Collects current view data, return current template file name
     *
     * @return string
     */
    public function render()
    {
        oxUBase::render();
        $myConfig = $this->getConfig();

        $this->_iAllArtCnt = 0;

        if ( $oActiveRecommList = $this->getActiveRecommList() ) {
            if ( ( $oList = $this->getArticleList() ) && $oList->count()) {
                $this->_iAllArtCnt = $oActiveRecommList->getArtCount();
            }

            if ( $myConfig->getConfigParam( 'bl_rssRecommListArts' ) ) {
                $oRss = oxNew('oxrssfeed');
                $this->addRssFeed($oRss->getRecommListArticlesTitle($oActiveRecommList), $oRss->getRecommListArticlesUrl($this->_oActiveRecommList), 'recommlistarts');
            }

        } else {
            if ( ( $oList = $this->getRecommLists() ) && $oList->count() ) {
                $oRecommList = oxNew( 'oxrecommlist' );
                $this->_iAllArtCnt = $oRecommList->getSearchRecommListCount( $this->getRecommSearch() );
            }
        }

        if ( !( $oList = $this->getArticleList() ) ) {
            $oList = $this->getRecommLists();
        }

        if ( $oList && $oList->count() ) {
            $iNrofCatArticles = (int) $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
            $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;
            $this->_iCntPages  = round( $this->_iAllArtCnt / $iNrofCatArticles + 0.49 );
        }
        // processing list articles
        $this->_processListArticles();

        return $this->_sThisTemplate;
    }

    /**
     * Returns product link type (OXARTICLE_LINKTYPE_RECOMM)
     *
     * @return int
     */
    protected function _getProductLinkType()
    {
        return OXARTICLE_LINKTYPE_RECOMM;
    }

    /**
     * Returns additional URL parameters which must be added to list products dynamic urls
     *
     * @return string
     */
    public function getAddUrlParams()
    {
        $sAddParams  = parent::getAddUrlParams();
        $sAddParams .= ($sAddParams?'&amp;':'') . "listtype={$this->_sListType}";

        if ( $oRecommList = $this->getActiveRecommList() ) {
            $sAddParams .= "&amp;recommid=".$oRecommList->getId();
        }

        return $sAddParams;
    }

    /**
     * Returns additional URL parameters which must be added to list products seo urls
     *
     * @return string
     */
    public function getAddSeoUrlParams()
    {
        $sAddParams = parent::getAddSeoUrlParams();
        if ( $sParam = oxConfig::getParameter( "searchrecomm", true) ) {
            $sAddParams .= "&amp;searchrecomm=" . rawurlencode( $sParam );
        }
        return $sAddParams;
    }

    /**
     * Saves user ratings and review text (oxreview object)
     *
     * @return null
     */
    public function saveReview()
    {
        if ( $this->canAcceptFormData() &&
             ( $oRecommList = $this->getActiveRecommList() ) && ( $oUser = $this->getUser() ) ) {

            //save rating
            $dRating = oxConfig::getParameter( 'recommlistrating' );
            if ( $dRating !== null ) {
                $dRating = (int) $dRating;
            }

            if ( $dRating !== null && $dRating >= 1 && $dRating <= 5 ) {
                $oRating = oxNew( 'oxrating' );
                if ( $oRating->allowRating( $oUser->getId(), 'oxrecommlist', $oRecommList->getId() ) ) {
                    $oRating->oxratings__oxuserid   = new oxField( $oUser->getId() );
                    $oRating->oxratings__oxtype     = new oxField( 'oxrecommlist' );
                    $oRating->oxratings__oxobjectid = new oxField( $oRecommList->getId() );
                    $oRating->oxratings__oxrating   = new oxField( $dRating );
                    $oRating->save();
                    $oRecommList->addToRatingAverage( $dRating );
                }
            }

            if ( ( $sReviewText = trim( ( string ) oxConfig::getParameter( 'rvw_txt', true ) ) ) ) {
                $oReview = oxNew( 'oxreview' );
                $oReview->oxreviews__oxobjectid = new oxField( $oRecommList->getId() );
                $oReview->oxreviews__oxtype     = new oxField( 'oxrecommlist' );
                $oReview->oxreviews__oxtext     = new oxField( $sReviewText, oxField::T_RAW );
                $oReview->oxreviews__oxlang     = new oxField( oxRegistry::getLang()->getBaseLanguage() );
                $oReview->oxreviews__oxuserid   = new oxField( $oUser->getId() );
                $oReview->oxreviews__oxrating   = new oxField( ( $dRating !== null ) ? $dRating : null );
                $oReview->save();
            }
        }
    }

    /**
     * Returns array of params => values which are used in hidden forms and as additional url params
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $aParams = oxUBase::getNavigationParams();
        $aParams['recommid'] = oxConfig::getParameter( 'recommid' );

        return $aParams;
    }

    /**
     * Template variable getter. Returns category's article list
     *
     * @return array
     */
    public function getArticleList()
    {
        if ( $this->_aArticleList === null ) {
            $this->_aArticleList = false;
            if ( $oActiveRecommList = $this->getActiveRecommList()) {
                // sets active page
                $iActPage = (int) oxConfig::getParameter( 'pgNr' );
                $iActPage = ($iActPage < 0) ? 0 : $iActPage;

                // load only lists which we show on screen
                $iNrofCatArticles = $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

                $this->_aArticleList = $oActiveRecommList->getArticles($iNrofCatArticles * $iActPage, $iNrofCatArticles);

                if ( $this->_aArticleList && $this->_aArticleList->count() ) {
                    foreach ( $this->_aArticleList as $oItem ) {
                        $oItem->text = $oActiveRecommList->getArtDescription( $oItem->getId() );
                    }
                }
            }
        }
        return $this->_aArticleList;
    }

    /**
     * Template variable getter. Returns other recommlists
     *
     * @return object
     */
    public function getSimilarRecommLists()
    {
        if ( $this->_oOtherRecommList === null ) {
            $this->_oOtherRecommList = false;
            if ( ( $oActiveRecommList = $this->getActiveRecommList() ) && ( $oList = $this->getArticleList() ) ) {
                $oRecommLists = $oActiveRecommList->getRecommListsByIds( $oList->arrayKeys() );
                //do not show the same list
                unset( $oRecommLists[$oActiveRecommList->getId()] );
                $this->_oOtherRecommList = $oRecommLists;
            }
        }
        return $this->_oOtherRecommList;
    }

    /**
     * Template variable getter. Returns recommlist's reviews
     *
     * @return array
     */
    public function getReviews()
    {
        if ( $this->_aReviews === null ) {
            $this->_aReviews = false;
            if ( $this->isReviewActive() && ( $oActiveRecommList = $this->getActiveRecommList() ) ) {
                $this->_aReviews = $oActiveRecommList->getReviews();
            }
        }
        return $this->_aReviews;
    }

    /**
     * Template variable getter. Returns if review module is on
     *
     * @return bool
     */
    public function isReviewActive()
    {
        return $this->getConfig()->getConfigParam( 'bl_perfLoadReviews' );
    }

    /**
     * Template variable getter. Returns if user can rate
     *
     * @return bool
     */
    public function canRate()
    {
        if ( $this->_blRate === null ) {
            $this->_blRate = false;
            if ( $this->isReviewActive() && ( $oActiveRecommList = $this->getActiveRecommList() ) ) {
                $oRating = oxNew( 'oxrating' );
                $this->_blRate = $oRating->allowRating( oxSession::getVar( 'usr' ), 'oxrecommlist', $oActiveRecommList->getId());
            }
        }
        return $this->_blRate;
    }

    /**
     * Template variable getter. Returns rating value
     *
     * @return double
     */
    public function getRatingValue()
    {
        if ( $this->_dRatingValue === null ) {
            $this->_dRatingValue = (double)0;
            if ( $this->isReviewActive() && ( $oActiveRecommList = $this->getActiveRecommList() ) ) {
                $this->_dRatingValue = round( $oActiveRecommList->oxrecommlists__oxrating->value, 1);
            }
        }
        return (double) $this->_dRatingValue;
    }

    /**
     * Template variable getter. Returns rating count
     *
     * @return integer
     */
    public function getRatingCount()
    {
        if ( $this->_iRatingCnt === null ) {
            $this->_iRatingCnt = false;
            if ( $this->isReviewActive() && ( $oActiveRecommList = $this->getActiveRecommList() ) ) {
                $this->_iRatingCnt = $oActiveRecommList->oxrecommlists__oxratingcnt->value;
            }
        }
        return $this->_iRatingCnt;
    }

    /**
     * Template variable getter. Returns searched recommlist
     *
     * @return object
     */
    public function getRecommLists()
    {
        if ( $this->_oSearchRecommLists === null ) {
            $this->_oSearchRecommLists = array();
            if ( !$this->getActiveRecommList() ) {
                // list of found oxrecommlists
                $oRecommList = oxNew( 'oxrecommlist' );
                $oList = $oRecommList->getSearchRecommLists( $this->getRecommSearch() );
                if ( $oList && $oList->count() ) {
                    $this->_oSearchRecommLists = $oList;
                }
            }
        }
        return $this->_oSearchRecommLists;
    }

    /**
     * Template variable getter. Returns search string
     *
     * @return string
     */
    public function getRecommSearch()
    {
        if ( $this->_sSearch === null ) {
            $this->_sSearch = false;
            if ( $sSearch = oxConfig::getParameter( 'searchrecomm', false ) ) {
                $this->_sSearch = $sSearch;
            }
        }
        return $this->_sSearch;
    }

    /**
     * Template variable getter. Returns category path array
     *
     * @return array
     */
    public function getTreePath()
    {
        $oLang = oxRegistry::getLang();

        $aPath[0] = oxNew( "oxcategory" );
        $aPath[0]->setLink( false );
        $aPath[0]->oxcategories__oxtitle = new oxField( $oLang->translateString('RECOMMLIST') );

        if ( $sSearchparam = $this->getRecommSearch() ) {
            $sUrl   = $this->getConfig()->getShopHomeURL()."cl=recommlist&amp;searchrecomm=".rawurlencode( $sSearchparam );
            $sTitle = $oLang->translateString('RECOMMLIST_SEARCH').' "'.$sSearchparam.'"';

            $aPath[1] = oxNew( "oxcategory" );
            $aPath[1]->setLink( $sUrl );
            $aPath[1]->oxcategories__oxtitle = new oxField( $sTitle );
        }

        return $aPath;
    }

    /**
     * Template variable getter. Returns search string
     *
     * @return string
     */
    public function getSearchForHtml()
    {
        // #M1450 if active recommlist is loaded return it's title
        if ( $oActiveRecommList = $this->getActiveRecommList()) {
            return $oActiveRecommList->oxrecommlists__oxtitle->value;
        }
        return oxConfig::getParameter( 'searchrecomm' );
    }

    /**
     * Generates Url for page navigation
     *
     * @return string
     */
    public function generatePageNavigationUrl()
    {
        if ( ( oxRegistry::getUtils()->seoIsActive() && ( $oRecomm = $this->getActiveRecommList() ) ) ) {
            $sUrl = $oRecomm->getLink();
        } else {
            $sUrl = oxUBase::generatePageNavigationUrl();
        }
        return $sUrl;
    }

    /**
     * Adds page number parameter to current Url and returns formatted url
     *
     * @param string $sUrl  url to append page numbers
     * @param int    $iPage current page number
     * @param int    $iLang requested language
     *
     * @return string
     */
    protected function _addPageNrParam( $sUrl, $iPage, $iLang = null)
    {
        if ( oxRegistry::getUtils()->seoIsActive() && ( $oRecomm = $this->getActiveRecommList() ) ) {
            if ( $iPage ) {
                // only if page number > 0
                $sUrl = $oRecomm->getBaseSeoLink( $iLang, $iPage );
            }
        } else {
            $sUrl = oxUBase::_addPageNrParam( $sUrl, $iPage, $iLang );
        }
        return $sUrl;
    }

    /**
     * Template variable getter. Returns additional params for url
     *
     * @return string
     */
    public function getAdditionalParams()
    {
        $sAddParams = oxUBase::getAdditionalParams();

        if ( $oRecomm = $this->getActiveRecommList() ) {
            $sAddParams .= "&amp;recommid=".$oRecomm->getId();
        }

        if ( $sSearch = $this->getRecommSearch() ) {
            $sAddParams .= "&amp;searchrecomm=". rawurlencode( $sSearch );
        }

        return $sAddParams;
    }

    /**
     * get link of current view
     *
     * @param int $iLang requested language
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        if ( $oRecomm = $this->getActiveRecommList() ) {
            $sLink = $oRecomm->getLink( $iLang );
        } else {
            $sLink = oxUBase::getLink( $iLang );
        }
        $sSearch = oxConfig::getParameter( 'searchrecomm' );
        if ( $sSearch ) {
            $sLink .= ( ( strpos( $sLink, '?' ) === false ) ? '?' : '&amp;' ) . "searchrecomm={$sSearch}";
        }

        return $sLink;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = array();
        $aPath = array();

        $aPath['title'] = oxRegistry::getLang()->translateString( 'LISTMANIA', oxRegistry::getLang()->getBaseLanguage(), false );
        $aPath['link']  = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }
}
