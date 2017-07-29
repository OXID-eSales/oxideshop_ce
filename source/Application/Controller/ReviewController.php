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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxField;
use oxRegistry;
use oxUBase;
use oxUser;

/**
 * Review of chosen article.
 * Collects article review data, saves new review to DB.
 */
class ReviewController extends \OxidEsales\Eshop\Application\Controller\ArticleDetailsController
{

    /**
     * Review user object
     *
     * @var oxuser
     */
    protected $_oRevUser = null;

    /**
     * Active object ($_oProduct or $_oActiveRecommList)
     *
     * @var object
     */
    protected $_oActObject = null;

    /**
     * Active recommendations list
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oActiveRecommList = null;

    /**
     * Active recommlist's items
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @var object
     */
    protected $_oActiveRecommItems = null;

    /**
     * Can user rate
     *
     * @var bool
     */
    protected $_blRate = null;

    /**
     * Array of reviews
     *
     * @var array
     */
    protected $_aReviews = null;

    /**
     * CrossSelling articlelist
     *
     * @var object
     */
    protected $_oCrossSelling = null;

    /**
     * Similar products articlelist
     *
     * @var object
     */
    protected $_oSimilarProducts = null;

    /**
     * Recommlist
     *
     * @var object
     */
    protected $_oRecommList = null;

    /**
     * Review send status
     *
     * @var bool
     */
    protected $_blReviewSendStatus = null;

    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/review/review.tpl';

    /**
     * Current class login template name.
     *
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
    public function generateViewId()
    {
        return \OxidEsales\Eshop\Application\Controller\FrontendController::generateViewId();
    }

    /**
     * Executes parent::init(), Loads user chosen product object (with all data).
     */
    public function init()
    {
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('recommid') && !$this->getActiveRecommList()) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($this->getConfig()->getShopHomeUrl(), true, 302);
        }
        // END deprecated

        \OxidEsales\Eshop\Application\Controller\FrontendController::init();
    }

    /**
     * Executes parent::render, loads article reviews and additional data
     * (\OxidEsales\Eshop\Application\Model\Article::getReviews(), \OxidEsales\Eshop\Application\Model\Article::getCrossSelling(),
     * \OxidEsales\Eshop\Application\Model\Article::GetSimilarProducts()). Returns name of template file to
     * render review::_sThisTemplate.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        $oConfig = $this->getConfig();

        if (!$oConfig->getConfigParam("bl_perfLoadReviews")) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($oConfig->getShopHomeUrl());
        }

        \OxidEsales\Eshop\Application\Controller\FrontendController::render();
        if (!($this->getReviewUser())) {
            $this->_sThisTemplate = $this->_sThisLoginTemplate;
        } else {
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            $oActiveRecommList = $this->getActiveRecommList();
            $oList = $this->getActiveRecommItems();

            if ($oActiveRecommList) {
                if ($oList && $oList->count()) {
                    $this->_iAllArtCnt = $oActiveRecommList->getArtCount();
                }
                // load only lists which we show on screen
                $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;
                $this->_iCntPages = ceil($this->_iAllArtCnt / $iNrofCatArticles);
            }
            // END deprecated
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
        if (!\OxidEsales\Eshop\Core\Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        if (($oRevUser = $this->getReviewUser()) && $this->canAcceptFormData()) {
            if (($oActObject = $this->_getActiveObject()) && ($sType = $this->_getActiveType())) {
                if (($dRating = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('rating')) === null) {
                    $dRating = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('artrating');
                }

                if ($dRating !== null) {
                    $dRating = (int) $dRating;
                }

                //save rating
                if ($dRating !== null && $dRating >= 1 && $dRating <= 5) {
                    $oRating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);
                    if ($oRating->allowRating($oRevUser->getId(), $sType, $oActObject->getId())) {
                        $oRating->oxratings__oxuserid = new \OxidEsales\Eshop\Core\Field($oRevUser->getId());
                        $oRating->oxratings__oxtype = new \OxidEsales\Eshop\Core\Field($sType);
                        $oRating->oxratings__oxobjectid = new \OxidEsales\Eshop\Core\Field($oActObject->getId());
                        $oRating->oxratings__oxrating = new \OxidEsales\Eshop\Core\Field($dRating);
                        $oRating->save();

                        $oActObject->addToRatingAverage($dRating);

                        $this->_blReviewSendStatus = true;
                    }
                }

                if (($sReviewText = trim(( string ) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('rvw_txt', true)))) {
                    $oReview = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
                    $oReview->oxreviews__oxobjectid = new \OxidEsales\Eshop\Core\Field($oActObject->getId());
                    $oReview->oxreviews__oxtype = new \OxidEsales\Eshop\Core\Field($sType);
                    $oReview->oxreviews__oxtext = new \OxidEsales\Eshop\Core\Field($sReviewText, \OxidEsales\Eshop\Core\Field::T_RAW);
                    $oReview->oxreviews__oxlang = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage());
                    $oReview->oxreviews__oxuserid = new \OxidEsales\Eshop\Core\Field($oRevUser->getId());
                    $oReview->oxreviews__oxrating = new \OxidEsales\Eshop\Core\Field(($dRating !== null) ? $dRating : null);
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
        if ($this->_oRevUser === null) {
            $this->_oRevUser = false;
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);

            if ($sUserId = $oUser->getReviewUserId($this->getReviewUserHash())) {
                // review user, by link or other source?
                if ($oUser->load($sUserId)) {
                    $this->_oRevUser = $oUser;
                }
            } elseif ($oUser = $this->getUser()) {
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
        return \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('reviewuserhash');
    }

    /**
     * Template variable getter. Returns active object (oxarticle or oxrecommlist)
     *
     * @return object
     */
    protected function _getActiveObject()
    {
        if ($this->_oActObject === null) {
            $this->_oActObject = false;

            if (($oProduct = $this->getProduct())) {
                $this->_oActObject = $oProduct;
                // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            } elseif (($oRecommList = $this->getActiveRecommList())) {
                $this->_oActObject = $oRecommList;
                // END deprecated
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
        if ($this->getProduct()) {
            return 'oxarticle';
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        } elseif ($this->getActiveRecommList()) {
            return 'oxrecommlist';
            // END deprecated
        }
    }

    /**
     * Template variable getter. Returns active recommlist
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return oxRecommList
     */
    public function getActiveRecommList()
    {
        if (!$this->getViewConfig()->getShowListmania()) {
            return false;
        }

        if ($this->_oActiveRecommList === null) {
            $this->_oActiveRecommList = false;

            if ($sRecommId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('recommid')) {
                $oActiveRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);
                if ($oActiveRecommList->load($sRecommId)) {
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
        if ($this->_blRate === null) {
            $this->_blRate = false;
            if (($oActObject = $this->_getActiveObject()) && ($oRevUser = $this->getReviewUser())) {
                $oRating = oxNew(\OxidEsales\Eshop\Application\Model\Rating::class);
                $this->_blRate = $oRating->allowRating(
                    $oRevUser->getId(),
                    $this->_getActiveType(),
                    $oActObject->getId()
                );
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
        if ($this->_aReviews === null) {
            $this->_aReviews = false;
            if ($oObject = $this->_getActiveObject()) {
                $this->_aReviews = $oObject->getReviews();
            }
        }

        return $this->_aReviews;
    }

    /**
     * Template variable getter. Returns recommlists
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return object
     */
    public function getRecommList()
    {
        if ($this->_oRecommList === null) {
            $this->_oRecommList = false;
            if ($oProduct = $this->getProduct()) {
                $oRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);
                $this->_oRecommList = $oRecommList->getRecommListsByIds(array($oProduct->getId()));
            }
        }

        return $this->_oRecommList;
    }

    /**
     * Template variable getter. Returns active recommlist's items
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return object
     */
    public function getActiveRecommItems()
    {
        if ($this->_oActiveRecommItems === null) {
            $this->_oActiveRecommItems = false;
            if ($oActiveRecommList = $this->getActiveRecommList()) {
                // sets active page
                $iActPage = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgNr');
                $iActPage = ($iActPage < 0) ? 0 : $iActPage;

                // load only lists which we show on screen
                $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
                $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

                $oList = $oActiveRecommList->getArticles($iNrofCatArticles * $iActPage, $iNrofCatArticles);

                if ($oList && $oList->count()) {
                    foreach ($oList as $oItem) {
                        $oItem->text = $oActiveRecommList->getArtDescription($oItem->getId());
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
        if ($this->_oPageNavigation === null) {
            $this->_oPageNavigation = false;
            // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
            if ($this->getActiveRecommList()) {
                $this->_oPageNavigation = $this->generatePageNavigation();
            }
            // END deprecated
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
        $sAddParams = \OxidEsales\Eshop\Application\Controller\FrontendController::getAdditionalParams();
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if ($oActRecommList = $this->getActiveRecommList()) {
            $sAddParams .= '&amp;recommid=' . $oActRecommList->getId();
        }
        // END deprecated

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

        if ($sCnId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cnid')) {
            $sParams .= "&amp;cnid={$sCnId}";
        }
        if ($sAnId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('anid')) {
            $sParams .= "&amp;anid={$sAnId}";
        }
        if ($sListType = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('listtype')) {
            $sParams .= "&amp;listtype={$sListType}";
        }
        // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
        if ($sRecommId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('recommid')) {
            $sParams .= "&amp;recommid={$sRecommId}";
        }
        // END deprecated

        return $sParams;
    }
}
