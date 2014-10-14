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

if (!defined('OXTAGCLOUD_MINFONT')) {
    define('OXTAGCLOUD_MINFONT', 100);
    define('OXTAGCLOUD_MAXFONT', 400);
    define('OXTAGCLOUD_MINOCCURENCETOSHOW', 2);
    define('OXTAGCLOUD_STARTPAGECOUNT', 20);
    define('OXTAGCLOUD_EXTENDEDCOUNT', 200);
}

/**
 * Class dedicateg to tag cloud handling
 *
 * @package model
 */
class oxTagCloud extends oxSuperCfg
{
    /**
     * Cloud cache key
     *
     * @var string
     */
    protected $_sCacheKey = "tagcloud";

    /**
     * Max hit
     *
     * @var int
     */
    protected $_iMaxHit = null;

    /**
     * Cloud array
     *
     * @var array
     */
    protected $_aCloudArray = null;

    /**
     * Extended mode
     *
     * @var bool
     */
    protected $_blExtended = false;

    /**
     * Tag separator.
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxtagset
     *
     * @var string
     */
    protected $_sSeparator = ',';

    /**
     * Maximum tag's length
     * Maximum size of one tag in admin area and limits tag input field in front end
     *
     * @deprecated since  v5.0.3 (2012-01-02), moved to oxtag
     *
     * @var int
     */
    protected $_iTagMaxLength = 60;

    /**
     * Product id
     *
     * @deprecated since  v5.0.3 (2012-01-02), moved to oxarticlelisttagset
     *
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Language id
     *
     * @deprecated since  v5.0.3 (2012-01-02), moved to oxarticlelisttagset
     *
     * @var int
     */
    protected $_iLangId = null;

    /**
     * Meta characters.
     * Array of meta chars used for FULLTEXT index.
     *
     * @deprecated since  v5.0.3 (2012-01-02), moved to oxtag
     *
     * @var array
     */
    protected $_aMetaChars = array('+','-','>','<','(',')','~','*','"','\'','\\','[',']','{','}',';',':','.','/','|','!','@','#','$','%','^','&','?','=','`');

    /**
     * Object constructor. Initializes separator.
     */
    public function __construct()
    {
    }

    /**
     * Created oxArticleTagSet object and passes it to oxTagCloud::setTagSet
     *
     * @param string $sProductId product id
     *
     * @deprecated since  v5.0.3 (2012-01-02), pass oxITagCloudSet object to oxTagCloud::setTagSet method instead
     *
     * @return null
     */
    public function setProductId( $sProductId )
    {
        $this->_sProductId = $sProductId;
        $oTagList = oxNew('oxarticletaglist');
        $oTagList->setArticleId( $sProductId );
        $oTagList->setLanguage( $this->getLanguageId() );
        $this->setTagList($oTagList);
    }

    /**
     * Returns current tag cloud product id (if available)
     *
     * @deprecated since  v5.0.3 (2012-01-02), class is now made article independent
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->_sProductId;
    }

    /**
     * Tag cloud language id setter
     *
     * @param int $iLangId language id
     *
     * @deprecated since  v5.0.3 (2012-01-02), Class is language independent now
     *
     * @return null
     */
    public function setLanguageId( $iLangId )
    {
        $this->_iLangId = $iLangId;
    }

    /**
     * Returns current tag cloud language id
     *
     * @deprecated since  v5.0.3 (2012-01-02), Class is language independent now
     *
     * @return int
     */
    public function getLanguageId()
    {
        if ( $this->_iLangId === null ) {
            $this->_iLangId = oxRegistry::getLang()->getBaseLanguage();
        }
        return $this->_iLangId;
    }

    /**
     * Tag cloud mode setter (extended or not)
     *
     * @param bool $blExtended if true - extended cloud array will be returned
     *
     * @return null
     */
    public function setExtendedMode( $blExtended )
    {
        $this->_blExtended = $blExtended;
    }

    /**
     * Extended mode getter
     *
     * @return bool
     */
    public function isExtended()
    {
        return $this->_blExtended;
    }

    /**
     * Sets oxITagList object
     *
     * @param oxITagList $oTagList Tag cloud set object, which implements oxITagList
     *
     * @return bool
     */
    public function setTagList( oxITagList $oTagList )
    {
        $this->_oTagList = $oTagList;
    }

    /**
     * Returns oxITagList object
     *
     * @return oxITagList
     */
    public function getTagList()
    {
        return $this->_oTagList;
    }

    /**
     * Sets tag cloud array
     *
     * @param array $aTagCloudArray tag cloud array
     *
     * @return void
     */
    public function setCloudArray( $aTagCloudArray )
    {
        $sCacheIdent = $this->_formCacheKey();
        $this->_aCloudArray[$sCacheIdent] = $aTagCloudArray;
    }

    /**
     * Returns extended tag cloud array
     *
     * @param string $sProductId product id [optional]
     * @param bool   $blExtended extended cloud array mode [optional]
     * @param int    $iLang      language id [optional]
     *
     * @return array
     */
    public function getCloudArray( $sProductId = null, $blExtended = null, $iLang = null )
    {
        // used to make deprecated functionality working
        if ( $iLang !== null ) {
            $this->setLanguageId( $iLang );
        }
        // used to make deprecated functionality working
        if ( $sProductId !== null ) {
            $this->setProductId( $sProductId );
        }
        // used to make deprecated functionality working
        if ( $blExtended !== null ) {
            $this->setExtendedMode($blExtended);
        }
        $sCacheIdent = $this->_formCacheKey();
        if ( !isset( $this->_aCloudArray[ $sCacheIdent ] ) ) {
            $oTagList = $this->getTagList();
            // used to make deprecated functionality working
            if ( $oTagList === null ) {
                $oTagList = oxNew('oxTagList');
                $oTagList->setLanguage( $this->getLanguageId() );
            }
            $this->_aCloudArray[$sCacheIdent] = $this->formCloudArray( $oTagList );
        }
        return $this->_aCloudArray[$sCacheIdent];
    }

    /**
     * Returns tag cloud array
     *
     * @param oxITagList $oTagList Tag List
     *
     * @return array
     */
    public function formCloudArray( oxITagList $oTagList )
    {
        // checking if current data is allready loaded
        if ( $oTagList->getCacheId() ) {
            $sCacheIdent = $this->_formCacheKey( $oTagList->getCacheId() );
            $myUtils = oxRegistry::getUtils();
            // checking cache
            $aCloudArray = $myUtils->fromFileCache( $sCacheIdent );
        }

        // loading cloud info
        if ( $aCloudArray === null ) {
            $oTagList->loadList();
            $oTagSet = $oTagList->get();
            if ( count( $oTagSet->get() ) > $this->getMaxAmount() ) {
                $oTagSet->sortByHitCount();
                $oTagSet->slice( 0, $this->getMaxAmount() );
            }
            $oTagSet->sort();
            $aCloudArray = $oTagSet->get();
            // updating cache
            if ( $sCacheIdent ) {
                $myUtils->toFileCache( $sCacheIdent, $aCloudArray );
            }
        }

        return $aCloudArray;
    }

    /**
     * Returns tag size
     *
     * @param string $sTag tag title
     *
     * @return int
     */
    public function getTagSize( $sTag )
    {
        $aCloudArray = $this->getCloudArray();
        if ( is_null($aCloudArray[$sTag]) ) {
            return 1;
        }
        $iCurrSize = $this->_getFontSize( $aCloudArray[$sTag]->getHitCount(), $this->_getMaxHit() );

        // calculating min size
        return floor( $iCurrSize / OXTAGCLOUD_MINFONT ) * OXTAGCLOUD_MINFONT;
    }

    /**
     * Returns maximum amount of tags, that should be shown in list
     *
     * @return int
     */
    public function getMaxAmount()
    {
        if ( $this->isExtended() ) {
            return OXTAGCLOUD_EXTENDEDCOUNT;
        } else {
            return OXTAGCLOUD_STARTPAGECOUNT;
        }
    }

    /**
     * Resets tag cache
     *
     * @param int $iLang preferred language [optional]
     *
     * @return null
     */
    public function resetTagCache( $iLang = null )
    {
        if ( $iLang ) {
            $this->setLanguageId( $iLang );
        }
        $this->resetCache();
    }

    /**
     * Resets tag cache
     *
     * @return null
     */
    public function resetCache()
    {
        $myUtils = oxRegistry::getUtils();

        $sCacheId = null;
        if ( ( $oTagList = $this->getTagList() ) !== null ) {
            $sCacheId = $oTagList->getCacheId();
        }

        $myUtils->toFileCache( $this->_formCacheKey( $sCacheId ), null );

        $this->_aCloudArray = null;
    }

    /**
     * Returns tag array
     *
     * @param string $sArtId     article id
     * @param bool   $blExtended if can extend tags
     * @param int    $iLang      preferred language [optional]
     *
     * @deprecated since  v5.0.3 (2012-01-02), moved to oxtaglist
     *
     * @return array
     */
    public function getTags( $sArtId = null, $blExtended = false, $iLang = null )
    {
        if ( $iLang !== null ) {
            $this->setLanguageId( $iLang );
        }
        // used to make deprecated functionality working
        if ( $sArtId !== null ) {
            $this->setProductId( $sArtId );
            $oTagList = $this->getTagList();
        } else {
            $oTagList = oxNew('oxTagList');
        }
        // used to make deprecated functionality working
        if ( $blExtended !== null ) {
            $this->setExtendedMode($blExtended);
        }
        $oTagList->load();
        $oTagSet = $oTagList->get();
        $oTagSet->sort();

        $aTags = array();
        foreach ( $oTagSet->get() as $sKey => $oTag ) {
            $aTags[$sKey] = $oTag->getHitCount();
        }

        return $aTags;
    }

    /**
     * Takes tags string, checks each tag length and makes shorter tags longer if needed.
     * This is needed for FULLTEXT index
     * Also if tag is longer than tag's max length - cuts it.
     *
     * @param string $sTags given tag
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxtag
     *
     * @return string
     */
    public function prepareTags( $sTags )
    {
        $sTags = $this->stripMetaChars($sTags);
        $aTags = explode( $this->_sSeparator, $sTags );
        $aRes = array();
        $oStr = getStr();

        foreach ( $aTags as $sTag ) {
            if ( ( $sTag = trim( $sTag ) ) ) {
                $sRes = '';
                $iLen = $oStr->strlen( $sTag );
                if ( $iLen > $this->_iTagMaxLength ) {
                    $sTag = $oStr->substr($sTag, 0, $this->_iTagMaxLength);
                }
                $sTag = trim( $sTag );
                $aMatches = explode(' ', $sTag);
                foreach ( $aMatches as $iKey => $sMatch ) {
                    $sRes .= $oStr->strtolower( $this->_fixTagLength($sMatch ) )." ";
                }
                $aRes[] = trim( $sRes );
            }
        }

        return implode( $this->_sSeparator, $aRes );
    }

    /**
     * Trims spaces from tags, removes unnecessary commas, dashes and underscores.
     *
     * @param string $sTags given tag
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxArticleTagSet
     *
     * @return string
     */
    public function trimTags( $sTags )
    {
        $oStr = getStr();
        $sTags = $oStr->preg_replace( "/(\s*\,+\s*)+/", ",", trim( $sTags ) );
        $sRes = '';

        if ( $oStr->preg_match_all( "/([\s\,\-]?)([^\s\,\-]+)([\s\,\-]?)/", $sTags, $aMatches ) ) {
            foreach ( $aMatches[0] as $iKey => $sMatch ) {
                $sProc = $aMatches[2][$iKey];
                if ( $oStr->strlen( $sProc ) <= OXTAGCLOUD_MINTAGLENGTH ) {
                    $sProc = rtrim( $sProc, "_" );
                }
                $sRes .= $aMatches[1][$iKey] . $sProc . $aMatches[3][$iKey];
            }
        }

        return trim( $sRes, $this->_sSeparator );
    }

    /**
     * Strips any mysql FULLTEXT specific meta characters.
     *
     * @param string $sText given text
     *
     * @deprecated since v5.0.3 (2012-01-02), use oxTag::stripMetaChars
     *
     * @return string
     */
    public function stripMetaChars( $sText )
    {
        $oStr  = getStr();

        // Remove meta chars
        $sText = str_replace($this->_aMetaChars, ' ', $sText);

        // Replace multiple spaces with single space
        $sText = $oStr->preg_replace( "/\s+/", " ", trim( $sText ) );

        return $sText;
    }

    /**
     * Returns current maximum tag length
     *
     * @deprecated since v5.0.3 (2012-01-02), use oxTag::getMaxLength
     *
     * @return int
     */
    public function getTagMaxLength()
    {
        $oTags = oxNew( 'oxtag' );
        return $oTags->getMaxLength();
    }

    /**
     * Returns tag url (seo or dynamic depends on shop mode)
     *
     * @param string $sTag tag title
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxTag::getLink
     *
     * @return string
     */
    public function getTagLink( $sTag )
    {
        $aCloudArray = $this->getCloudArray();
        return $aCloudArray[$sTag]->getLink();
    }

    /**
     * Returns html safe tag title
     *
     * @param string $sTag tag title
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxArticleTagSet
     *
     * @return string
     */
    public function getTagTitle( $sTag )
    {
        $aCloudArray = $this->getCloudArray();
        return $aCloudArray[$sTag]->getTitle();
    }

    /**
     * Checks if tags was already tagged for the same product
     *
     * @param string $sTagTitle given tag
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxArticleTagSet
     *
     * @return bool
     */
    public function canBeTagged( $sTagTitle )
    {
        $oTags = oxNew( 'oxarticletaglist' );
        $oTags->load($this->getProductId());
        return $oTags->canBeTagged($sTagTitle);
    }

    /**
     * Takes tag string and makes shorter tags longer by adding underscore.
     *
     * @param string $sTag given tag
     *
     * @deprecated since v5.0.3 (2012-01-02), moved to oxArticleTagSet
     *
     * @return string
     */
    public function _fixTagLength( $sTag )
    {
        $oStr = getStr();
        $sTag = trim( $sTag );
        $iLen = $oStr->strlen( $sTag );

        if ( $iLen < OXTAGCLOUD_MINTAGLENGTH ) {
            $sTag .= str_repeat( '_', OXTAGCLOUD_MINTAGLENGTH - $iLen );
        }

        return $sTag;
    }

    /**
     * Returns tag cache key name.
     *
     * @param bool $blExtended Whether to display full list
     * @param int  $iLang      preferred language [optional]
     *
     * @deprecated since v5.0.3 (2012-01-02), use _formCacheKey
     *
     * @return null
     */
    protected function _getCacheKey( $blExtended, $iLang = null )
    {
        return $this->_sCacheKey."_".$this->getConfig()->getShopId()."_".( ( $iLang !== null ) ? $iLang : oxRegistry::getLang()->getBaseLanguage() ) ."_".($blExtended?1:0);
    }

    /**
     * Returns tag cache key name.
     *
     * @param string $sTagListCacheId Whether to display full list
     *
     * @return string formed cache key
     */
    protected function _formCacheKey( $sTagListCacheId = null )
    {
        $sExtended = $this->isExtended()? '1' : '0';
        return $this->_sCacheKey."_".$this->getConfig()->getShopId()."_".$sExtended."_".$sTagListCacheId;
    }

    /**
     * Returns max hit
     *
     * @return int
     */
    protected function _getMaxHit()
    {
        if ( $this->_iMaxHit === null ) {
            $aHits = array_map( array($this, '_getTagHitCount'), $this->getCloudArray());
            $this->_iMaxHit = max( $aHits );
        }
        return $this->_iMaxHit;
    }

    /**
     * Returns tag hit count. Used for _getMaxHit array mapping
     *
     * @param oxTag $oTag tag object
     *
     * @return int
     */
    protected function _getTagHitCount( $oTag )
    {
        return $oTag->getHitCount();
    }

    /**
     * Returns font size value for current occurence depending on max occurence.
     *
     * @param int $iHit    hit count
     * @param int $iMaxHit max hits count
     *
     * @return int
     */
    protected function _getFontSize( $iHit, $iMaxHit )
    {
        //handling special case
        if ($iMaxHit <= OXTAGCLOUD_MINOCCURENCETOSHOW || !$iMaxHit) {
            return OXTAGCLOUD_MINFONT;
        }

        $iFontDiff = OXTAGCLOUD_MAXFONT - OXTAGCLOUD_MINFONT;
        $iMaxHitDiff = $iMaxHit - OXTAGCLOUD_MINOCCURENCETOSHOW;
        $iHitDiff = $iHit - OXTAGCLOUD_MINOCCURENCETOSHOW;

        if ($iHitDiff < 0) {
            $iHitDiff = 0;
        }

        $iSize = round($iHitDiff * $iFontDiff / $iMaxHitDiff) + OXTAGCLOUD_MINFONT;

        return $iSize;
    }

    /**
     * Sorts passed tag array. Using MySQL for sorting (to keep user defined ordering way).
     *
     * @param array $aTags tags to sort
     * @param int   $iLang preferred language [optional]
     *
     * @deprecated since v5.0.3 (2012-01-02), Sorting is now done by php.
     *
     * @return array
     */
    protected function _sortTags( $aTags, $iLang = null )
    {
        if ( is_array( $aTags ) && count( $aTags ) ) {
            $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
            $sSubQ = '';
            foreach ( $aTags as $sKey => $sTag ) {
                if ( $sSubQ ) {
                    $sSubQ .= ' union all ';
                }
                $sSubQ .= 'select '.$oDb->quote( $sKey ).' as _oxsort, '.$oDb->quote( $sTag ).' as _oxval';
            }

            $sViewName = getViewName( "oxartextends", $iLang );

            // forcing collation
            $sSubQ = "select {$sViewName}.oxtags as _oxsort, 'ox_skip' as _oxval from {$sViewName} limit 1 union $sSubQ";
            $sQ = "select _oxtable._oxsort, _oxtable._oxval from ( {$sSubQ} ) as _oxtable order by _oxtable._oxsort desc";

            $aTags = array();
            $oDb->setFetchMode( oxDb::FETCH_MODE_ASSOC );
            $oRs = $oDb->select( $sQ );
            while ( $oRs && $oRs->recordCount() && !$oRs->EOF ) {
                if ( $oRs->fields['_oxval'] != 'ox_skip' ) {
                    $aTags[$oRs->fields['_oxsort']] = $oRs->fields['_oxval'];
                }
                $oRs->moveNext();
            }
        }
        return $aTags;
    }

}
