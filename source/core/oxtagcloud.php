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
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

if (!defined('OXTAGCLOUD_MINFONT')) {
    define('OXTAGCLOUD_MINFONT', 100);
    define('OXTAGCLOUD_MAXFONT', 400);
    define('OXTAGCLOUD_MINOCCURENCETOSHOW', 2);
    //depends on mysql server configuration
    define('OXTAGCLOUD_MINTAGLENGTH', 4);
    define('OXTAGCLOUD_STARTPAGECOUNT', 20);
    define('OXTAGCLOUD_EXTENDEDCOUNT', 200);
}

/**
 * Class dedicateg to tag cloud handling
 *
 */
class oxTagCloud extends oxSuperCfg
{
    /**
     * Cloud cache key
     *
     * @var string
     */
    protected $_sCacheKey = "tagcloud_";

    /**
     * Extended mode
     *
     * @var bool
     */
    protected $_blExtended = false;

    /**
     * Product id
     *
     * @var string
     */
    protected $_sProductId = null;

    /**
     * Language id
     *
     * @var int
     */
    protected $_iLangId = null;

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
     * Tag separator.
     * @deprecated since 5.0.0,  Separator as space is not used any more. The default value is  ','
     *
     * @var string
     */
    protected $_sSeparator = ' ';

    /**
     * Maximum tag's length
     * Maximum size of one tag in admin area and limits tag input field in front end
     *
     * @var int
     */
    protected $_iTagMaxLength = 60;

    /**
     * Meta characters.
     * Array of meta chars used for FULLTEXT index.
     *
     * @var array
     */

    protected $_aMetaChars = array('+','-','>','<','(',')','~','*','"','\'','\\','[',']','{','}',';',':','.','/','|','!','@','#','$','%','^','&','?','=','`');

    /**
     * Object constructor. Initializes separator.
     *
     */
    public function __construct()
    {
        $sSeparator = $this->getConfig()->getConfigParam("sTagSeparator");
        if ($sSeparator)
            $this->_sSeparator = $sSeparator;
    }

    /**
     * Tag cloud product id setter
     *
     * @param string $sProductId product id
     *
     * @return null
     */
    public function setProductId( $sProductId )
    {
        $this->_sProductId = $sProductId;
    }

    /**
     * Tag cloud language id setter
     *
     * @param int $iLangId language id
     *
     * @return null
     */
    public function setLanguageId( $iLangId )
    {
        $this->_iLangId = $iLangId;
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
     * Returns current tag cloud language id
     *
     * @return int
     */
    public function getLanguageId()
    {
        if ( $this->_iLangId === null ) {
            $this->_iLangId = oxLang::getInstance()->getBaseLanguage();
        }
        return $this->_iLangId;
    }

    /**
     * Returns current tag cloud product id (if available)
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->_sProductId;
    }

    /**
     * Returns current maximum tag length
     *
     * @return int
     */
    public function getTagMaxLength()
    {
        return $this->_iTagMaxLength;
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
        // collecting cloud info
        $iLang      = ( $iLang === null ) ? (int) $this->getLanguageId() : $iLang;
        $blExtended = ( $blExtended === null ) ? $this->isExtended() : $blExtended;
        $sProductId = ( $sProductId === null ) ? (string) $this->getProductId() : $sProductId;

        // checking if current data is allready loaded
        $sCacheIdent = $this->_getCacheKey( $blExtended, $iLang );
        if ($sProductId ) {
            $sCacheIdent .= "_".$sProductId;
        }
        if ( !isset( $this->_aCloudArray[$sCacheIdent] ) ) {

            $myUtils = oxUtils::getInstance();

            // checking cache
            $aCloudArray = ( !$sProductId ) ? $myUtils->fromFileCache( $sCacheIdent ) : null;

            // loading cloud info
            if ( $aCloudArray === null ) {
                $aCloudArray = $this->getTags( $sProductId, $blExtended, $iLang );
                // updating cache
                if ( !$sProductId ) {
                    $myUtils->toFileCache( $sCacheIdent, $aCloudArray );
                }
            }

            $this->_aCloudArray[$sCacheIdent] = $aCloudArray;
        }
        return $this->_aCloudArray[$sCacheIdent];
    }

    /**
     * Returns tag url (seo or dynamic depends on shop mode)
     *
     * @param string $sTag tag title
     *
     * @return string
     */
    public function getTagLink( $sTag )
    {
        $oSeoEncoderTag = oxSeoEncoderTag::getInstance();
        $iLang = $this->getLanguageId();

        $sUrl = false;
        if ( oxUtils::getInstance()->seoIsActive() ) {
            $sUrl = $oSeoEncoderTag->getTagUrl( $sTag, $iLang );
        }
        return $sUrl ? $sUrl : $this->getConfig()->getShopUrl() . $oSeoEncoderTag->getStdTagUri( $sTag ) . "&amp;lang=" . $iLang;
    }

    /**
     * Returns html safe tag title
     *
     * @param string $sTag tag title
     *
     * @return string
     */
    public function getTagTitle( $sTag )
    {
        return getStr()->htmlentities( $sTag );
    }

    /**
     * Returns max hit
     *
     * @return int
     */
    protected function _getMaxHit()
    {
        if ( $this->_iMaxHit === null ) {
            $this->_iMaxHit = max( $this->getCloudArray() );
        }
        return $this->_iMaxHit;
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
        $iCurrSize = $this->_getFontSize( $aCloudArray[ $sTag ], $this->_getMaxHit() );

        // calculating min size
        return floor( $iCurrSize / OXTAGCLOUD_MINFONT ) * OXTAGCLOUD_MINFONT;
    }


    /**
     * Returns tag array
     *
     * @param string $sArtId     article id
     * @param bool   $blExtended if can extend tags
     * @param int    $iLang      preferred language [optional]
     *
     * @return array
     */
    public function getTags( $sArtId = null, $blExtended = false, $iLang = null )
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        if ($blExtended) {
            $iAmount = OXTAGCLOUD_EXTENDEDCOUNT;
        } else {
            $iAmount = OXTAGCLOUD_STARTPAGECOUNT;
        }

        $sArtView  = getViewName( 'oxarticles', $iLang );
        $sViewName = getViewName( 'oxartextends', $iLang );

        // check if article is still active
        $oArticle   = oxNew( 'oxarticle' );
        $oArticle->setLanguage( $iLang );
        $sArtActive = $oArticle->getActiveCheckQuery(true);


        $sQ = "SELECT {$sViewName}.`oxtags` AS `oxtags`
            FROM {$sArtView} AS `oxarticles`
                LEFT JOIN {$sViewName} ON `oxarticles`.`oxid` = {$sViewName}.`oxid`
            WHERE `oxarticles`.`oxactive` = 1 AND $sArtActive";

        if ( $sArtId ) {
            $sQ = "SELECT {$sViewName}.`oxtags` AS `oxtags` FROM {$sViewName} WHERE `oxid` = " . $oDb->quote( $sArtId );
            $iAmount = 0;
        }

        $oDb->setFetchMode( oxDb::FETCH_MODE_ASSOC );
        $rs = $oDb->select( $sQ );
        $aTags = array();
        while ( $rs && $rs->recordCount() && !$rs->EOF ) {
            $sTags = $this->trimTags( $rs->fields['oxtags'] );
            $aArticleTags = explode( $this->_sSeparator, $sTags );
            foreach ( $aArticleTags as $sTag ) {
                if ( trim( $sTag ) ) {
                    ++$aTags[$sTag];
                }
            }
            $rs->moveNext();
        }

        //taking only top tags
        if ( $iAmount ) {
            arsort( $aTags );
            $aTags = array_slice( $aTags, 0, $iAmount, true );
        }

        $aTags = $this->_sortTags( $aTags );
        return $aTags;
    }

    /**
     * Sorts passed tag array. Using MySQL for sorting (to keep user defined ordering way).
     *
     * @param array $aTags tags to sort
     * @param int   $iLang preferred language [optional]
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
     * Takes tag string and makes shorter tags longer by adding underscore.
     *
     * @param string $sTag given tag
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
     * Strips any mysql FULLTEXT specific meta characters.
     *
     * @param string $sText given text
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
     * Takes tags string, checks each tag length and makes shorter tags longer if needed.
     * This is needed for FULLTEXT index
     * Also if tag is longer than tag's max length - cuts it.
     *
     * @param string $sTags given tag
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
     * Resets tag cache
     *
     * @param int $iLang preferred language [optional]
     *
     * @return null
     */
    public function resetTagCache( $iLang = null )
    {
        $myUtils = oxUtils::getInstance();

        $sCacheKey1 = $this->_getCacheKey( true, $iLang );
        $myUtils->toFileCache( $sCacheKey1, null );

        $sCacheKey2 = $this->_getCacheKey( false, $iLang );
        $myUtils->toFileCache( $sCacheKey2, null );
    }

    /**
     * Returns tag cache key name.
     *
     * @param bool $blExtended Whether to display full list
     * @param int  $iLang      preferred language [optional]
     *
     * @return null
     */
    protected function _getCacheKey( $blExtended, $iLang = null )
    {
        return $this->_sCacheKey."_".$this->getConfig()->getShopId()."_".( ( $iLang !== null ) ? $iLang : oxLang::getInstance()->getBaseLanguage() ) ."_".($blExtended?1:0);
    }

    /**
     * Checks if tags was already tagged for the same product
     *
     * @param string $sTagTitle given tag
     *
     * @return bool
     */
    public function canBeTagged( $sTagTitle )
    {
        $aProducts = oxSession::getVar("aTaggedProducts");
        if ( isset($aProducts) && $aTags = $aProducts[$this->getProductId()]) {
            if ( $aTags[$sTagTitle] == 1 ) {
                return false;
            }
        }
        return true;
    }

}
