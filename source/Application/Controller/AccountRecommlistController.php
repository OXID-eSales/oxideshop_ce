<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxField;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use oxObjectException;
use oxRegistry;

/**
 * Current user recommlist manager.
 * When user is logged in in this manager window he can modify his
 * own recommlists status - remove articles from list or store
 * them to shopping basket, view detail information.
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 */
class AccountRecommlistController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/recommendationlist.tpl';

    /**
     * Is recomendation list entry was saved this marker gets value TRUE. Default is FALSE
     *
     * @var bool
     */
    protected $_blSavedEntry = false;

    /**
     * returns the recomm list articles
     *
     * @var object
     */
    protected $_oActRecommListArticles = null;

    /**
     * returns the recomm list article. Whether the variable is empty, it list nothing
     *
     * @var array
     */
    protected $_aUserRecommLists = null;

    /**
     * returns the recomm list articles
     *
     * @var object
     */
    protected $_oActRecommList = null;

    /**
     * List items count
     *
     * @var int
     */
    protected $_iAllArtCnt = 0;

    /**
     * Page navigation
     *
     * @var object
     */
    protected $_oPageNavigation = null;

    /**
     * If user is logged in loads his wishlist articles (articles may be accessed by
     * \OxidEsales\Eshop\Application\Model\User::GetBasket()), loads similar articles (is available) for the last
     * article in list loaded by \OxidEsales\Eshop\Application\Model\Article::GetSimilarProducts() and returns name of
     * template to render \OxidEsales\Eshop\Application\Controller\AccountWishlistController::_sThisTemplate
     *
     * @return  string  $_sThisTemplate current template file name
     */
    public function render()
    {
        parent::render();

        // is logged in ?
        if (!($oUser = $this->getUser())) {
            return $this->_sThisTemplate = $this->_sThisLoginTemplate;
        }

        $oLists = $this->getRecommLists();
        $oActList = $this->getActiveRecommList();

        // list of found oxrecommlists
        if (!$oActList && $oLists->count()) {
            $this->_iAllArtCnt = $oUser->getRecommListsCount();
            $iNrofCatArticles = (int) $this->getConfig()->getConfigParam('iNrofCatArticles');
            $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;
            $this->_iCntPages = ceil($this->_iAllArtCnt / $iNrofCatArticles);
        }

        return $this->_sThisTemplate;
    }

    /**
     * Returns array of params => values which are used in hidden forms and as additional url params
     *
     * @return array
     */
    public function getNavigationParams()
    {
        $aParams = parent::getNavigationParams();

        // adding recommendation list id to list product urls
        if (($oList = $this->getActiveRecommList())) {
            $aParams['recommid'] = $oList->getId();
        }

        return $aParams;
    }

    /**
     * return recomm list from the user
     *
     * @return array
     */
    public function getRecommLists()
    {
        if ($this->_aUserRecommLists === null) {
            $this->_aUserRecommLists = false;
            if (($oUser = $this->getUser())) {
                // recommendation list
                $this->_aUserRecommLists = $oUser->getUserRecommLists();
            }
        }

        return $this->_aUserRecommLists;
    }

    /**
     * return all articles in the recomm list
     *
     * @return null
     */
    public function getArticleList()
    {
        if ($this->_oActRecommListArticles === null) {
            $this->_oActRecommListArticles = false;

            if (($oRecommList = $this->getActiveRecommList())) {
                $oItemList = $oRecommList->getArticles();

                if ($oItemList->count()) {
                    foreach ($oItemList as $key => $oItem) {
                        if (!$oItem->isVisible()) {
                            $oRecommList->removeArticle($oItem->getId());
                            $oItemList->offsetUnset($key);
                            continue;
                        }

                        $oItem->text = $oRecommList->getArtDescription($oItem->getId());
                    }
                    $this->_oActRecommListArticles = $oItemList;
                }
            }
        }

        return $this->_oActRecommListArticles;
    }

    /**
     * return the active entrys
     *
     * @return null
     */
    public function getActiveRecommList()
    {
        if (!$this->getViewConfig()->getShowListmania()) {
            return false;
        }

        if ($this->_oActRecommList === null) {
            $this->_oActRecommList = false;

            if (($oUser = $this->getUser()) &&
                ($sRecommId = Registry::getConfig()->getRequestParameter('recommid'))
            ) {
                $oRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);
                $sUserIdField = 'oxrecommlists__oxuserid';
                if (($oRecommList->load($sRecommId)) && $oUser->getId() === $oRecommList->$sUserIdField->value) {
                    $this->_oActRecommList = $oRecommList;
                }
            }
        }

        return $this->_oActRecommList;
    }

    /**
     * Set active recommlist
     *
     * @param object $oRecommList Recommendation list
     */
    public function setActiveRecommList($oRecommList)
    {
        $this->_oActRecommList = $oRecommList;
    }

    /**
     * add new recommlist
     *
     * @return null
     */
    public function saveRecommList()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        if (!$this->getViewConfig()->getShowListmania()) {
            return;
        }

        if (($oUser = $this->getUser())) {
            if (!($oRecommList = $this->getActiveRecommList())) {
                $oRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);
                $oRecommList->oxrecommlists__oxuserid = new Field($oUser->getId());
                $oRecommList->oxrecommlists__oxshopid = new Field($this->getConfig()->getShopId());
            } else {
                $this->_sThisTemplate = 'page/account/recommendationedit.tpl';
            }

            $sTitle = trim(( string ) Registry::getConfig()->getRequestParameter('recomm_title', true));
            $sAuthor = trim(( string ) Registry::getConfig()->getRequestParameter('recomm_author', true));
            $sText = trim(( string ) Registry::getConfig()->getRequestParameter('recomm_desc', true));

            $oRecommList->oxrecommlists__oxtitle = new Field($sTitle);
            $oRecommList->oxrecommlists__oxauthor = new Field($sAuthor);
            $oRecommList->oxrecommlists__oxdesc = new Field($sText);

            try {
                // marking entry as saved
                $this->_blSavedEntry = (bool) $oRecommList->save();
                $this->setActiveRecommList($this->_blSavedEntry ? $oRecommList : false);
            } catch (\OxidEsales\Eshop\Core\Exception\ObjectException $oEx) {
                //add to display at specific position
                Registry::getUtilsView()->addErrorToDisplay($oEx, false, true, 'user');
            }
        }
    }

    /**
     * List entry saving status getter. Saving status is
     *
     * @return bool
     */
    public function isSavedList()
    {
        return $this->_blSavedEntry;
    }

    /**
     * Delete recommlist
     *
     * @return null
     */
    public function editList()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        if (!$this->getViewConfig()->getShowListmania()) {
            return;
        }

        // deleting on demand
        if (($sAction = Registry::getConfig()->getRequestParameter('deleteList')) &&
            ($oRecommList = $this->getActiveRecommList())
        ) {
            $oRecommList->delete();
            $this->setActiveRecommList(false);
        } else {
            $this->_sThisTemplate = 'page/account/recommendationedit.tpl';
        }
    }

    /**
     * Delete recommlist
     *
     * @return null
     */
    public function removeArticle()
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        if (!$this->getViewConfig()->getShowListmania()) {
            return;
        }

        if (($sArtId = Registry::getConfig()->getRequestParameter('aid')) &&
            ($oRecommList = $this->getActiveRecommList())
        ) {
            $oRecommList->removeArticle($sArtId);
        }
        $this->_sThisTemplate = 'page/account/recommendationedit.tpl';
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
            if (!$this->getActiveRecommlist()) {
                $this->_oPageNavigation = $this->generatePageNavigation();
            }
        }

        return $this->_oPageNavigation;
    }

    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        $sSelfLink = $this->getViewConfig()->getSelfLink();
        $aPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = Registry::getSeoEncoder()->getStaticUrl($sSelfLink . 'cl=account');
        $aPaths[] = $aPath;

        $aPath['title'] = Registry::getLang()->translateString('LISTMANIA', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Article count getter
     *
     * @return int
     */
    public function getArticleCount()
    {
        return $this->_iAllArtCnt;
    }
}
