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
 * Review of chosen article.
 * Collects article review data, saves new review to DB.
 */
class Review extends Details
{
    /**
     * Review user object
     * @var oxuser
     */
    protected $_oRevUser = null;

    /**
     * Active object ($_oProduct or $_oActiveRecommList)
     * @var object
     */
    protected $_oActObject = null;

    /**
     * Active recommendations list
     * @var object
     */
    protected $_oActiveRecommList = null;

    /**
     * Active recommlist's items
     * @var object
     */
    protected $_oActiveRecommItems = null;

    /**
     * Can user rate
     * @var bool
     */
    protected $_blRate = null;

    /**
     * Array of reviews
     * @var array
     */
    protected $_aReviews = null;

    /**
     * CrossSelling articlelist
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products articlelist
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Recommlist
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Review send status
     * @var bool
     */
    protected $_blReviewSendStatus = null;

    /**
     * Page navigation
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'page/review/review.tpl';

    /**
     * Current class login template name.
     * @var string
     */
    protected $_sThisLoginTemplate = 'page/review/review_login.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * Returns prefix ID used by template engine.
     *
     * @return  string  $this->_sViewID view id
     */
    public function getViewId()
    {
        return oxUBase::getViewId();
    }

    /**
     * Executes parent::init(), Loads user chosen product object (with all data).
     *
     * @return null
     */
    public function init()
    {
        if ( oxConfig::getParameter( 'recommid' ) && !$this->getActiveRecommList() ) {
            oxRegistry::getUtils()->redirect( $this->getConfig()->getShopHomeURL(), true, 302 );
        }

        oxUBase::init();
    }

    /**
     * Executes parent::render, loads article reviews and additional data
     * (oxarticle::getReviews(), oxarticle::getCrossSelling(),
     * oxarticle::GetSimilarProducts()). Returns name of template file to
     * render review::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        $oConfig = $this->getConfig();

        if ( !$oConfig->getConfigParam( "bl_perfLoadReviews" ) ) {
            oxRegistry::getUtils()->redirect( $oConfig->getShopHomeURL() );
        }

        oxUBase::render();
        if ( ! ( $this->getReviewUser() ) ) {
            $this->_sThisTemplate = $this->_sThisLoginTemplate;
        } else {

            $oActiveRecommList = $this->getActiveRecommList();
            $oList = $this->getActiveRecommItems();

            if ( $oActiveRecommList ) {
                if ( $oList && $oList->count()) {
                    $this->_iAllArtCnt = $oActiveRecommList->getArtCount();
                }
                // load only lists which we show on screen
                $iNrofCatArticles = $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;
                $this->_iCntPages  = round( $this->_iAllArtCnt / $iNrofCatArticles + 0.49 );
            }
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves user review text (oxreview object)
     *
     * @return null
     */
    public function saveReview()
    {
        if ( ( $oRevUser = $this->getReviewUser() ) && $this->canAcceptFormData() ) {

            if ( ( $oActObject = $this->_getActiveObject() ) && ( $sType = $this->_getActiveType() ) ) {

                if ( ( $dRating = oxConfig::getParameter( 'rating' ) ) === null ) {
                    $dRating = oxConfig::getParameter( 'artrating' );
                }

                if ( $dRating !== null ) {
                    $dRating = (int) $dRating;
                }

                //save rating
                if ( $dRating !== null && $dRating >= 1 && $dRating <= 5 ) {
                    $oRating = oxNew( 'oxrating' );
                    if ( $oRating->allowRating( $oRevUser->getId(), $sType, $oActObject->getId() ) ) {
                        $oRating->oxratings__oxuserid   = new oxField( $oRevUser->getId() );
                        $oRating->oxratings__oxtype     = new oxField( $sType );
                        $oRating->oxratings__oxobjectid = new oxField( $oActObject->getId() );
                        $oRating->oxratings__oxrating   = new oxField( $dRating );
                        $oRating->save();

                        $oActObject->addToRatingAverage( $dRating);

                        $this->_blReviewSendStatus = true;
                    }
                }

                if ( ( $sReviewText = trim( ( string ) oxConfig::getParameter( 'rvw_txt', true ) ) ) ) {
                    $oReview = oxNew( 'oxreview' );
                    $oReview->oxreviews__oxobjectid = new oxField( $oActObject->getId() );
                    $oReview->oxreviews__oxtype     = new oxField( $sType );
                    $oReview->oxreviews__oxtext     = new oxField( $sReviewText, oxField::T_RAW );
                    $oReview->oxreviews__oxlang     = new oxField( oxRegistry::getLang()->getBaseLanguage() );
                    $oReview->oxreviews__oxuserid   = new oxField( $oRevUser->getId() );
                    $oReview->oxreviews__oxrating   = new oxField( ( $dRating !== null ) ? $dRating : null );
                    $oReview->save();

                    $this->_blReviewSendStatus = true;
                }
            }
        }
    }

    /**
     * Returns review user object
     *
     * @return oxuser
     */
    public function getReviewUser()
    {
        if ( $this->_oRevUser === null ) {
            $this->_oRevUser = false;
            $oUser = oxNew( "oxuser" );

            if ( $sUserId = $oUser->getReviewUserId( $this->getReviewUserHash() ) ) {
                // review user, by link or other source?
                if ( $oUser->load( $sUserId ) ) {
                    $this->_oRevUser = $oUser;
                }
            } elseif ( $oUser = $this->getUser() ) {
                // session user?
                $this->_oRevUser = $oUser;
            }

        }
        return $this->_oRevUser;
    }

    /**
     * Template variable getter. Returns review user id
     *
     * @return string
     */
    public function getReviewUserHash()
    {
        return oxConfig::getParameter( 'reviewuserhash' );
    }

    /**
     * Template variable getter. Returns active object (oxarticle or oxrecommlist)
     *
     * @return object
     */
    protected function _getActiveObject()
    {
        if ( $this->_oActObject === null ) {
            $this->_oActObject = false;

            if ( ( $oProduct = $this->getProduct() ) ) {
                $this->_oActObject = $oProduct;
            } elseif ( ( $oRecommList = $this->getActiveRecommList() ) ) {
                $this->_oActObject = $oRecommList;
            }
        }
        return $this->_oActObject;
    }

    /**
     * Template variable getter. Returns active type (oxarticle or oxrecommlist)
     *
     * @return string
     */
    protected function _getActiveType()
    {
        $sType = null;
        if ( $this->getProduct() ) {
            $sType = 'oxarticle';
        } elseif ( $this->getActiveRecommList() ) {
            $sType = 'oxrecommlist';
        }
        return $sType;
    }

    /**
     * Template variable getter. Returns active recommlist
     *
     * @return oxRecommList
     */
    public function getActiveRecommList()
    {
        if (!$this->getViewConfig()->getShowListmania()) {
            return false;
        }

        if ( $this->_oActiveRecommList === null ) {
            $this->_oActiveRecommList = false;

            if ( $sRecommId = oxConfig::getParameter( 'recommid' ) ) {
                $oActiveRecommList = oxNew('oxrecommlist');
                if ( $oActiveRecommList->load( $sRecommId ) ) {
                    $this->_oActiveRecommList = $oActiveRecommList;
                }
            }
        }
        return $this->_oActiveRecommList;
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
            if ( ( $oActObject = $this->_getActiveObject() ) && ( $oRevUser = $this->getReviewUser() ) ) {
                $oRating = oxNew( 'oxrating' );
                $this->_blRate = $oRating->allowRating( $oRevUser->getId(), $this->_getActiveType(), $oActObject->getId() );
            }
        }
        return $this->_blRate;
    }

    /**
     * Template variable getter. Returns active object's reviews
     *
     * @return array
     */
    public function getReviews()
    {
        if ( $this->_aReviews === null ) {
            $this->_aReviews = false;
            if ( $oObject = $this->_getActiveObject() ) {
                $this->_aReviews = $oObject->getReviews();
            }
        }
        return $this->_aReviews;
    }

    /**
     * Template variable getter. Returns recommlists
     *
     * @return object
     */
    public function getRecommList()
    {
        if ( $this->_oRecommList === null ) {
            $this->_oRecommList = false;
            if ( $oProduct = $this->getProduct() ) {
                $oRecommList = oxNew('oxrecommlist');
                $this->_oRecommList = $oRecommList->getRecommListsByIds( array( $oProduct->getId() ) );
            }
        }
        return $this->_oRecommList;
    }

    /**
     * Template variable getter. Returns active recommlist's items
     *
     * @return object
     */
    public function getActiveRecommItems()
    {
        if ( $this->_oActiveRecommItems === null ) {
            $this->_oActiveRecommItems = false;
            if ( $oActiveRecommList = $this->getActiveRecommList()) {
                // sets active page
                $iActPage = (int) oxConfig::getParameter( 'pgNr' );
                $iActPage = ($iActPage < 0) ? 0 : $iActPage;

                // load only lists which we show on screen
                $iNrofCatArticles = $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

                $oList = $oActiveRecommList->getArticles($iNrofCatArticles * $iActPage, $iNrofCatArticles);

                if ( $oList && $oList->count() ) {
                    foreach ( $oList as $oItem) {
                        $oItem->text = $oActiveRecommList->getArtDescription( $oItem->getId() );
                    }
                    $this->_oActiveRecommItems = $oList;
                }
            }
        }
        return $this->_oActiveRecommItems;
    }

    /**
     * Template variable getter. Returns review send status
     *
     * @return bool
     */
    public function getReviewSendStatus()
    {
        return $this->_blReviewSendStatus;
    }

    /**
     * Template variable getter. Returns page navigation
     *
     * @return object
     */
    public function getPageNavigation()
    {
        if ( $this->_oPageNavigation === null ) {
            $this->_oPageNavigation = false;
            if ( $this->getActiveRecommList() ) {
                $this->_oPageNavigation = $this->generatePageNavigation();
            }
        }
        return $this->_oPageNavigation;
    }

    /**
     * Template variable getter. Returns additional params for url
     *
     * @return string
     */
    public function getAdditionalParams()
    {
        $sAddParams = oxUBase::getAdditionalParams();
        if ( $oActRecommList = $this->getActiveRecommList() ) {
            $sAddParams .= '&amp;recommid='.$oActRecommList->getId();
        }
        return $sAddParams;
    }

    /**
     * returns additional url params for dynamic url building
     *
     * @return string
     */
    public function getDynUrlParams()
    {
        $sParams = parent::getDynUrlParams();

        if ( $sCnId = oxConfig::getParameter( 'cnid' ) ) {
            $sParams .= "&amp;cnid={$sCnId}";
        }
        if ( $sAnId = oxConfig::getParameter( 'anid' ) ) {
            $sParams .= "&amp;anid={$sAnId}";
        }
        if ( $sListType = oxConfig::getParameter( 'listtype' ) ) {
            $sParams .= "&amp;listtype={$sListType}";
        }
        if ( $sRecommId = oxConfig::getParameter( 'recommid' ) ) {
            $sParams .= "&amp;recommid={$sRecommId}";
        }

        return $sParams;
    }
}
