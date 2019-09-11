<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use Exception;
use oxDb;
use oxRegistry;
use oxList;
use oxField;

/**
 * Recommendation list manager class.
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 *
 */
class RecommendationList extends \OxidEsales\Eshop\Core\Model\BaseModel implements \OxidEsales\Eshop\Core\Contract\IUrl
{
    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxRecommList';

    /**
     * Article list
     *
     * @var string
     */
    protected $_oArticles = null;

    /**
     * Article list loading filter (appended where statement)
     *
     * @var string
     */
    protected $_sArticlesFilter = '';

    /**
     * Seo article urls for languages
     *
     * @var array
     */
    protected $_aSeoUrls = [];

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxrecommlists');
    }

    /**
     * Returns list of recommendation list items
     *
     * @param integer $iStart        start for sql limit
     * @param integer $iNrofArticles nr of items per page
     * @param bool    $blReload      if TRUE forces to reload list
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function getArticles($iStart = null, $iNrofArticles = null, $blReload = false)
    {
        // cached ?
        if ($this->_oArticles !== null && !$blReload) {
            return $this->_oArticles;
        }

        $this->_oArticles = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);

        if ($iStart !== null && $iNrofArticles !== null) {
            $this->_oArticles->setSqlLimit($iStart, $iNrofArticles);
        }

        // loading basket items
        $this->_oArticles->loadRecommArticles($this->getId(), $this->_sArticlesFilter);

        return $this->_oArticles;
    }

    /**
     * Returns count of recommendation list items
     *
     * @return integer
     */
    public function getArtCount()
    {
        $iCnt = 0;
        $sSelect = $this->_getArticleSelect();
        if ($sSelect) {
            $iCnt = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSelect);
        }

        return $iCnt;
    }

    /**
     * Returns the appropriate SQL select
     *
     * @return string
     */
    protected function _getArticleSelect()
    {
        $sArtView = getViewName('oxarticles');
        $sSelect = "select count(distinct $sArtView.oxid) from oxobject2list ";
        $sSelect .= "left join $sArtView on oxobject2list.oxobjectid = $sArtView.oxid ";
        $sSelect .= "where (oxobject2list.oxlistid = '" . $this->getId() . "') ";

        return $sSelect;
    }

    /**
     * returns first article from this list's article list
     *
     * @return oxArticle
     */
    public function getFirstArticle()
    {
        $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $oArtList->setSqlLimit(0, 1);
        $oArtList->loadRecommArticles($this->getId(), $this->_sArticlesFilter);
        $oArtList->rewind();

        return $oArtList->current();
    }

    /**
     * Removes articles from the recommlist and deletes list
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOXID = null)
    {
        if (!$sOXID) {
            $sOXID = $this->getId();
        }
        if (!$sOXID) {
            return false;
        }

        if (($blDelete = parent::delete($sOXID))) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            // cleaning up related data
            $oDb->execute("delete from oxobject2list where oxlistid = :oxlistid", [
                ':oxlistid' => $sOXID
            ]);
            $this->onDelete();
        }

        return $blDelete;
    }

    /**
     * Returns article description for recommendation list
     *
     * @param string $sOXID Object ID
     *
     * @return string
     */
    public function getArtDescription($sOXID)
    {
        if (!$sOXID) {
            return false;
        }

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sSelect = 'select oxdesc from oxobject2list 
            where oxlistid = :oxlistid and oxobjectid = :oxobjectid';

        return $oDb->getOne($sSelect, [
            ':oxlistid' => $this->getId(),
            ':oxobjectid' => $sOXID
        ]);
    }

    /**
     * Remove article from recommendation list
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function removeArticle($sOXID)
    {
        if ($sOXID) {
            $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $sQ = "delete from oxobject2list where oxobjectid = :oxobjectid and oxlistid = :oxlistid";

            return $oDb->execute($sQ, [
                ':oxobjectid' => $sOXID,
                ':oxlistid' => $this->getId()
            ]);
        }
    }

    /**
     * Add article to recommendation list
     *
     * @param string $sOXID Object ID
     * @param string $sDesc recommended article description
     *
     * @throws Exception
     *
     * @return bool
     */
    public function addArticle($sOXID, $sDesc)
    {
        $blAdd = false;
        if ($sOXID) {
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);

            $sql = "select oxid from oxobject2list 
                where oxobjectid = :oxobjectid 
                    and oxlistid = :oxlistid";
            $params = [
                ':oxobjectid' => $sOXID,
                ':oxlistid' => $this->getId()
            ];

            if (!$database->getOne($sql, $params)) {
                $sUid = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUID();
                $sQ = "insert into oxobject2list (oxid, oxobjectid, oxlistid, oxdesc) values (:oxid, :oxobjectid, :oxlistid, :oxdesc)";
                $blAdd = $database->execute($sQ, [
                    ':oxid' => $sUid,
                    ':oxobjectid' => $sOXID,
                    ':oxlistid' => $this->getId(),
                    ':oxdesc' => $sDesc
                ]);
            }
        }

        return $blAdd;
    }

    /**
     * get recommendation lists which include given article ids
     * also sort these lists by these criteria:
     *     1. show lists, that has more requested articles first
     *     2. show lists, that have more any articles
     *
     * @param array $aArticleIds Object IDs
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function getRecommListsByIds($aArticleIds)
    {
        if (is_array($aArticleIds) && count($aArticleIds)) {
            startProfile(__FUNCTION__);

            $sIds = implode(",", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aArticleIds));

            $oRecommList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oRecommList->init('oxrecommlist');

            $iCnt = $this->getConfig()->getConfigParam('iNrofCrossellArticles');

            $oRecommList->setSqlLimit(0, $iCnt);

            $sSelect = "SELECT distinct lists.* FROM oxobject2list AS o2l_lists";
            $sSelect .= " LEFT JOIN oxobject2list AS o2l_count ON o2l_lists.oxlistid = o2l_count.oxlistid";
            $sSelect .= " LEFT JOIN oxrecommlists as lists ON o2l_lists.oxlistid = lists.oxid";
            $sSelect .= " WHERE o2l_lists.oxobjectid IN ( $sIds ) and lists.oxshopid = :oxshopid";
            $sSelect .= " GROUP BY lists.oxid order by (";
            $sSelect .= " SELECT count( order1.oxobjectid ) FROM oxobject2list AS order1";
            $sSelect .= " WHERE order1.oxobjectid IN ( $sIds ) AND o2l_lists.oxlistid = order1.oxlistid";
            $sSelect .= " ) DESC, count( lists.oxid ) DESC";

            $oRecommList->selectString($sSelect, [
                ':oxshopid' => $this->getConfig()->getShopId()
            ]);

            stopProfile(__FUNCTION__);

            if ($oRecommList->count()) {
                startProfile('_loadFirstArticles');

                $this->_loadFirstArticles($oRecommList, $aArticleIds);

                stopProfile('_loadFirstArticles');

                return $oRecommList;
            }
        }
    }

    /**
     * loads first articles to recomm list also ordering them and clearing not usable list objects
     * ordering priorities:
     *     1. first show articles from our search
     *     2. do not shown articles as 1st, which are shown in other recomm lists as 1st
     *
     * @param \OxidEsales\Eshop\Core\Model\ListModel $oRecommList recommendation list
     * @param array                                  $aIds        article ids
     */
    protected function _loadFirstArticles(\OxidEsales\Eshop\Core\Model\ListModel $oRecommList, $aIds)
    {
        $aIds = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aIds);
        $sIds = implode(", ", $aIds);

        $aPrevIds = [];
        $sArtView = getViewName('oxarticles');
        foreach ($oRecommList as $key => $oRecomm) {
            if (count($aPrevIds)) {
                $sNegateSql = " AND $sArtView.oxid not in ( '" . implode("','", $aPrevIds) . "' ) ";
            } else {
                $sNegateSql = '';
            }
            $sArticlesFilter = "$sNegateSql ORDER BY $sArtView.oxid in ( $sIds ) desc";
            $oRecomm->setArticlesFilter($sArticlesFilter);
            $oArtList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
            $oArtList->setSqlLimit(0, 1);
            $oArtList->loadRecommArticles($oRecomm->getId(), $sArticlesFilter);

            if (count($oArtList) == 1) {
                $oArtList->rewind();
                $oArticle = $oArtList->current();
                $sId = $oArticle->getId();
                $aPrevIds[$sId] = $sId;
                unset($aIds[$sId]);
                $sIds = implode(", ", $aIds);
            } else {
                unset($oRecommList[$key]);
            }
        }
    }

    /**
     * Returns user recommendation list objects
     *
     * @param string $sSearchStr Search string
     *
     * @return object oxlist with oxrecommlist objects
     */
    public function getSearchRecommLists($sSearchStr)
    {
        if ($sSearchStr) {
            // sets active page
            $iActPage = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('pgNr');
            $iActPage = ($iActPage < 0) ? 0 : $iActPage;

            // load only lists which we show on screen
            $iNrofCatArticles = $this->getConfig()->getConfigParam('iNrofCatArticles');
            $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

            $oRecommList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oRecommList->init('oxrecommlist');
            $sSelect = $this->_getSearchSelect($sSearchStr);
            $oRecommList->setSqlLimit($iNrofCatArticles * $iActPage, $iNrofCatArticles);
            $oRecommList->selectString($sSelect);

            return $oRecommList;
        }
    }

    /**
     * Returns the amount of lists according to search parameters.
     *
     * @param string $sSearchStr Search string
     *
     * @return int
     */
    public function getSearchRecommListCount($sSearchStr)
    {
        $iCnt = 0;
        $sSelect = $this->_getSearchSelect($sSearchStr);
        if ($sSelect) {
            $sPartial = substr($sSelect, strpos($sSelect, ' from '));
            $sSelect = "select count( distinct rl.oxid ) $sPartial ";
            $iCnt = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($sSelect);
        }

        return $iCnt;
    }

    /**
     * Returns the appropriate SQL select according to search parameters
     *
     * @param string $sSearchStr Search string
     *
     * @return string
     */
    protected function _getSearchSelect($sSearchStr)
    {
        $iShopId = $this->getConfig()->getShopId();
        $sSearchStrQuoted = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quote("%$sSearchStr%");

        $sSelect = "select distinct rl.* from oxrecommlists as rl";
        $sSelect .= " inner join oxobject2list as o2l on o2l.oxlistid = rl.oxid";
        $sSelect .= " where ( rl.oxtitle like $sSearchStrQuoted or rl.oxdesc like $sSearchStrQuoted";
        $sSelect .= " or o2l.oxdesc like $sSearchStrQuoted ) and rl.oxshopid = '$iShopId'";

        return $sSelect;
    }

    /**
     * Calculates and saves product rating average
     *
     * @param integer $iRating new rating value
     */
    public function addToRatingAverage($iRating)
    {
        $dOldRating = $this->oxrecommlists__oxrating->value;
        $dOldCnt = $this->oxrecommlists__oxratingcnt->value;
        $this->oxrecommlists__oxrating = new \OxidEsales\Eshop\Core\Field(($dOldRating * $dOldCnt + $iRating) / ($dOldCnt + 1), \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->oxrecommlists__oxratingcnt = new \OxidEsales\Eshop\Core\Field($dOldCnt + 1, \OxidEsales\Eshop\Core\Field::T_RAW);
        $this->save();
    }

    /**
     * Collects user written reviews about an article.
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function getReviews()
    {
        $oReview = oxNew(\OxidEsales\Eshop\Application\Model\Review::class);
        $oRevs = $oReview->loadList('oxrecommlist', $this->getId());
        //if no review found, return null
        if ($oRevs->count() < 1) {
            return null;
        }

        return $oRevs;
    }

    /**
     * Returns raw recommlist seo url
     *
     * @param int $iLang language id
     * @param int $iPage page number [optional]
     *
     * @return string
     */
    public function getBaseSeoLink($iLang, $iPage = 0)
    {
        $oEncoder = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Application\Model\SeoEncoderRecomm::class);
        if (!$iPage) {
            return $oEncoder->getRecommUrl($this, $iLang);
        }

        return $oEncoder->getRecommPageUrl($this, $iPage, $iLang);
    }

    /**
     * return url to this recomm list page
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getLink($iLang = null)
    {
        if ($iLang === null) {
            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        }

        if (!\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
            return $this->getStdLink($iLang);
        }

        if (!isset($this->_aSeoUrls[$iLang])) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink($iLang);
        }

        return $this->_aSeoUrls[$iLang];
    }

    /**
     * Returns standard (dynamic) object URL
     *
     * @param int   $iLang   language id [optional]
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink($iLang = null, $aParams = [])
    {
        if ($iLang === null) {
            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
        }

        return \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->processUrl($this->getBaseStdLink($iLang), true, $aParams, $iLang);
    }

    /**
     * Returns base dynamic recommlist url: shopurl/index.php?cl=recommlist
     *
     * @param int  $iLang   language id
     * @param bool $blAddId add current object id to url or not
     * @param bool $blFull  return full including domain name [optional]
     *
     * @return string
     */
    public function getBaseStdLink($iLang, $blAddId = true, $blFull = true)
    {
        $sUrl = '';
        if ($blFull) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl($iLang, false);
        }

        return $sUrl . "index.php?cl=recommlist" . ($blAddId ? "&amp;recommid=" . $this->getId() : "");
    }

    /**
     * set sql filter for article loading
     *
     * @param string $sArticlesFilter article filter
     */
    public function setArticlesFilter($sArticlesFilter)
    {
        $this->_sArticlesFilter = $sArticlesFilter;
    }

    /**
     * Save this Object to database, insert or update as needed.
     *
     * @return mixed
     */
    public function save()
    {
        if (!$this->oxrecommlists__oxtitle->value) {
            throw oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class, 'EXCEPTION_RECOMMLIST_NOTITLE');
        }
        $this->onSave();

        return parent::save();
    }

    /**
     * Method is used for overriding when deleting recommendation list.
     */
    protected function onDelete()
    {
    }

    /**
     * Method is used for overriding when saving.
     */
    protected function onSave()
    {
    }
}
