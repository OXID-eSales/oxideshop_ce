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
 * Recommendation list manager class.
 *
 * @package model
 */
class oxRecommList extends oxBase implements oxIUrl
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
    protected $_oArticles  = null;

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
    protected $_aSeoUrls = array();

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxrecommlists' );
    }

    /**
     * Returns list of recommendation list items
     *
     * @param integer $iStart        start for sql limit
     * @param integer $iNrofArticles nr of items per page
     * @param bool    $blReload      if TRUE forces to reload list
     *
     * @return oxList
     */
    public function getArticles(  $iStart = null, $iNrofArticles = null, $blReload = false )
    {
        // cached ?
        if ( $this->_oArticles !== null && !$blReload ) {
            return $this->_oArticles;
        }

        $this->_oArticles = oxNew( 'oxarticlelist' );

        if ( $iStart !== null && $iNrofArticles !== null ) {
            $this->_oArticles->setSqlLimit( $iStart, $iNrofArticles );
        }

        // loading basket items
        $this->_oArticles->loadRecommArticles( $this->getId(), $this->_sArticlesFilter );

        return $this->_oArticles;
    }

    /**
     * Returns count of recommendation list items
     *
     * @return array of oxUserBasketItems
     */
    public function getArtCount()
    {
        $iCnt = 0;
        $sSelect = $this->_getArticleSelect();
        if ( $sSelect ) {
            $iCnt = oxDb::getDb()->getOne( $sSelect );
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
        $sArtView = getViewName( 'oxarticles' );
        $sSelect  = "select count(distinct $sArtView.oxid) from oxobject2list ";
        $sSelect .= "left join $sArtView on oxobject2list.oxobjectid = $sArtView.oxid ";
        $sSelect .= "where (oxobject2list.oxlistid = '".$this->getId()."') ";

        return $sSelect;
    }

    /**
     * returns first article from this list's article list
     *
     * @return oxArticle
     */
    public function getFirstArticle()
    {
        $oArtList = oxNew( 'oxarticlelist' );
        $oArtList->setSqlLimit( 0, 1 );
        $oArtList->loadRecommArticles( $this->getId(), $this->_sArticlesFilter );
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
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }

        if ( ( $blDelete = parent::delete( $sOXID ) ) ) {
            $oDb = oxDb::getDb();
            // cleaning up related data
            $oDb->execute( "delete from oxobject2list where oxlistid = ".$oDb->quote( $sOXID ) );


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
    public function getArtDescription( $sOXID )
    {
        if ( !$sOXID ) {
            return false;
        }

        $oDb = oxDb::getDb();
        $sSelect = 'select oxdesc from oxobject2list where oxlistid = '.$oDb->quote( $this->getId() ).' and oxobjectid = '.$oDb->quote( $sOXID );
        return $oDb->getOne( $sSelect );
    }

    /**
     * Remove article from recommendation list
     *
     * @param string $sOXID Object ID
     *
     * @return bool
     */
    public function removeArticle( $sOXID )
    {
        if ( $sOXID ) {

            $oDb = oxDb::getDb();
            $sQ = "delete from oxobject2list where oxobjectid = ".$oDb->quote( $sOXID ) ." and oxlistid=".$oDb->quote( $this->getId() );
            return $oDb->execute( $sQ );
        }
    }

    /**
     * Add article to recommendation list
     *
     * @param string $sOXID Object ID
     * @param string $sDesc recommended article description
     *
     * @return bool
     */
    public function addArticle( $sOXID, $sDesc )
    {
        $blAdd = false;
        if ( $sOXID ) {
            $oDb = oxDb::getDb();
            if ( !$oDb->getOne( "select oxid from oxobject2list where oxobjectid=".$oDb->quote( $sOXID )." and oxlistid=".$oDb->quote( $this->getId() ), false, false) ) {
                $sUid  = oxUtilsObject::getInstance()->generateUID();
                $sQ    = "insert into oxobject2list ( oxid, oxobjectid, oxlistid, oxdesc ) values ( '$sUid', ".$oDb->quote( $sOXID ).", ".$oDb->quote( $this->getId() ).", ".$oDb->quote( $sDesc )." )";
                $blAdd = $oDb->execute( $sQ );
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
     * @return oxList
     */
    public function getRecommListsByIds( $aArticleIds )
    {
        if ( count( $aArticleIds ) ) {
            startProfile(__FUNCTION__);

            $sIds = implode( ",", oxDb::getInstance()->quoteArray( $aArticleIds ) );

            $oRecommList = oxNew( 'oxlist' );
            $oRecommList->init( 'oxrecommlist' );

            $iShopId = $this->getConfig()->getShopId();
            $iCnt = $this->getConfig()->getConfigParam( 'iNrofCrossellArticles' );

            $oRecommList->setSqlLimit( 0, $iCnt );

            $sSelect = "SELECT distinct lists.* FROM oxobject2list AS o2l_lists";
            $sSelect.= " LEFT JOIN oxobject2list AS o2l_count ON o2l_lists.oxlistid = o2l_count.oxlistid";
            $sSelect.= " LEFT JOIN oxrecommlists as lists ON o2l_lists.oxlistid = lists.oxid";
            $sSelect.= " WHERE o2l_lists.oxobjectid IN ( $sIds ) and lists.oxshopid ='$iShopId'";
            $sSelect.= " GROUP BY lists.oxid order by (";
            $sSelect.= " SELECT count( order1.oxobjectid ) FROM oxobject2list AS order1";
            $sSelect.= " WHERE order1.oxobjectid IN ( $sIds ) AND o2l_lists.oxlistid = order1.oxlistid";
            $sSelect.= " ) DESC, count( lists.oxid ) DESC";

            $oRecommList->selectString( $sSelect );

            stopProfile(__FUNCTION__);

            if ( $oRecommList->count() ) {
                startProfile('_loadFirstArticles');

                $this->_loadFirstArticles( $oRecommList, $aArticleIds );

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
     * @param oxList $oRecommList recommendation list
     * @param array  $aIds        article ids
     *
     * @return null
     */
    protected function _loadFirstArticles(oxList $oRecommList, $aIds)
    {
        $aIds = oxDb::getInstance()->quoteArray( $aIds );
        $sIds = implode(", ", $aIds);

        $aPrevIds = array();
        $sArtView = getViewName( 'oxarticles' );
        foreach ($oRecommList as $key => $oRecomm) {

            if (count($aPrevIds)) {
                $sNegateSql = " AND $sArtView.oxid not in ( '".implode("','", $aPrevIds)."' ) ";
            } else {
                $sNegateSql = '';
            }
            $sArticlesFilter = "$sNegateSql ORDER BY $sArtView.oxid in ( $sIds ) desc";
            $oRecomm->setArticlesFilter($sArticlesFilter);
            $oArtList = oxNew( 'oxarticlelist' );
            $oArtList->setSqlLimit( 0, 1 );
            $oArtList->loadRecommArticles( $oRecomm->getId(), $sArticlesFilter );

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
    public function getSearchRecommLists( $sSearchStr )
    {
        if ( $sSearchStr ) {
            // sets active page
            $iActPage = (int) oxConfig::getParameter( 'pgNr' );
            $iActPage = ($iActPage < 0) ? 0 : $iActPage;

            // load only lists which we show on screen
            $iNrofCatArticles = $this->getConfig()->getConfigParam( 'iNrofCatArticles' );
            $iNrofCatArticles = $iNrofCatArticles ? $iNrofCatArticles : 10;

            $oRecommList = oxNew( 'oxlist' );
            $oRecommList->init( 'oxrecommlist' );
            $sSelect = $this->_getSearchSelect( $sSearchStr );
            $oRecommList->setSqlLimit( $iNrofCatArticles * $iActPage, $iNrofCatArticles );
            $oRecommList->selectString( $sSelect );

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
    public function getSearchRecommListCount( $sSearchStr )
    {
        $iCnt = 0;
        $sSelect = $this->_getSearchSelect( $sSearchStr );
        if ( $sSelect ) {

            $sPartial = substr( $sSelect, strpos( $sSelect, ' from ' ) );
            $sSelect  = "select count( distinct rl.oxid ) $sPartial ";
            $iCnt = oxDb::getDb()->getOne( $sSelect );
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
    protected function _getSearchSelect( $sSearchStr )
    {
        $iShopId          = $this->getConfig()->getShopId();
        $sSearchStrQuoted = oxDb::getDb()->quote( "%$sSearchStr%" );

        $sSelect = "select distinct rl.* from oxrecommlists as rl";
        $sSelect.= " inner join oxobject2list as o2l on o2l.oxlistid = rl.oxid";
        $sSelect.= " where ( rl.oxtitle like $sSearchStrQuoted or rl.oxdesc like $sSearchStrQuoted";
        $sSelect.= " or o2l.oxdesc like $sSearchStrQuoted ) and rl.oxshopid = '$iShopId'";

        return $sSelect;
    }

    /**
     * Calculates and saves product rating average
     *
     * @param integer $iRating new rating value
     *
     * @return null
     */
    public function addToRatingAverage( $iRating)
    {
        $dOldRating = $this->oxrecommlists__oxrating->value;
        $dOldCnt    = $this->oxrecommlists__oxratingcnt->value;
        $this->oxrecommlists__oxrating    = new oxField(( $dOldRating * $dOldCnt + $iRating ) / ($dOldCnt + 1), oxField::T_RAW);
        $this->oxrecommlists__oxratingcnt = new oxField($dOldCnt + 1, oxField::T_RAW);
        $this->save();
    }

    /**
     * Collects user written reviews about an article.
     *
     * @return oxList
     */
    public function getReviews()
    {
        $oReview = oxNew('oxreview');
        $oRevs = $oReview->loadList('oxrecommlist', $this->getId());
        //if no review found, return null
        if ( $oRevs->count() < 1 ) {
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
    public function getBaseSeoLink( $iLang, $iPage = 0 )
    {
        $oEncoder = oxRegistry::get("oxSeoEncoderRecomm");
        if ( !$iPage ) {
            return $oEncoder->getRecommUrl( $this, $iLang );
        }
        return $oEncoder->getRecommPageUrl( $this, $iPage, $iLang );
    }

    /**
     * return url to this recomm list page
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        if ( $iLang === null ) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }

        if ( !oxRegistry::getUtils()->seoIsActive() ) {
            return $this->getStdLink( $iLang );
        }

        if ( !isset( $this->_aSeoUrls[$iLang] ) ) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink( $iLang );
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
    public function getStdLink( $iLang = null, $aParams = array() )
    {
        if ( $iLang === null ) {
            $iLang = oxRegistry::getLang()->getBaseLanguage();
        }

        return oxRegistry::get("oxUtilsUrl")->processUrl( $this->getBaseStdLink( $iLang ), true, $aParams, $iLang);
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
    public function getBaseStdLink( $iLang, $blAddId = true, $blFull = true )
    {
        $sUrl = '';
        if ( $blFull ) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl( $iLang, false );
        }

        return $sUrl . "index.php?cl=recommlist" . ( $blAddId ? "&amp;recommid=".$this->getId() : "" );
    }

    /**
     * set sql filter for article loading
     *
     * @param string $sArticlesFilter article filter
     *
     * @return null
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
            throw oxNew( "oxObjectException", 'EXCEPTION_RECOMMLIST_NOTITLE');
        }

        return parent::save();
    }


}
