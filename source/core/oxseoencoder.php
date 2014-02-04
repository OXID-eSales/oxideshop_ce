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
 * Seo encoder base
 *
 * @package core
 */
class oxSeoEncoder extends oxSuperCfg
{
    /**
     * Strings that cannot be used in SEO URLs as this may cause
     * compatability/access problems
     *
     * @var array
     */
    protected static $_aReservedWords = array( 'admin' );

    /**
     * cache for reserved path root node keys
     *
     * @var array
     */
    protected static $_aReservedEntryKeys = null;

    /**
     * SEO separator.
     *
     * @var string
     */
    protected static $_sSeparator = null;

    /**
     * SEO id length.
     *
     * @var integer
     */
    protected $_iIdLength = 255;

    /**
     * SEO prefix.
     *
     * @var string
     */
    protected static $_sPrefix = null;

    /**
     * Added parameters.
     *
     * @var string
     */
    protected $_sAddParams = null;

    /**
     * Singleton instance.
     *
     * @var oxseoencoder
     */
    protected static $_instance = null;

    /**
     * Url fixed state cache
     *
     * @return array
     */
    protected static $_aFixedCache = array();

    /**
     * SEO Cache key for active view
     * @var string
     */
    protected static $_sCacheKey = null;

    /**
     * SEO cache array
     * @var array
     */
    protected static $_aCache = array();

    /**
     * Maximum seo/dynamic url length
     * @var int
     */
    protected $_iMaxUrlLength = null;

    /**
     * Singleton method
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxSeoEncoder") instead
     *
     * @return oxseoencoder
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxSeoEncoder");
    }

    /**
     * Returns part of url defining active language
     *
     * @param string $sSeoUrl seo url
     * @param int    $iLang   language id
     *
     * @return string
     */
    public function addLanguageParam( $sSeoUrl, $iLang )
    {
        $iLang    = (int) $iLang;
        $iDefLang = (int) $this->getConfig()->getConfigParam( 'iDefSeoLang' );
        $aLangIds = oxRegistry::getLang()->getLanguageIds();

        if ( $iLang != $iDefLang && isset( $aLangIds[$iLang] ) && getStr()->strpos( $sSeoUrl, $aLangIds[$iLang] . '/' ) !== 0 ) {
            $sSeoUrl = $aLangIds[$iLang] . '/'.$sSeoUrl;
        }

        return $sSeoUrl;
    }

    /**
     * Processes seo url before saving to db:
     *  - oxseoencoder::addLanguageParam();
     *  - oxseoencoder::_getUniqueSeoUrl().
     *
     * @param string $sSeoUrl   seo url to process
     * @param string $sObjectId seo object id [optional]
     * @param int    $iLang     active language id [optional]
     * @param bool   $blExclude exclude language prefix while building seo url
     *
     * @return string
     */
    protected function _processSeoUrl( $sSeoUrl, $sObjectId = null, $iLang = null, $blExclude = false )
    {
        if (!$blExclude) {
            $sSeoUrl = $this->addLanguageParam( $sSeoUrl, $iLang );
        }
        return $this->_getUniqueSeoUrl( $sSeoUrl, $sObjectId, $iLang );
    }

    /**
     * SEO encoder constructor
     */
    public function __construct()
    {
        $myConfig = $this->getConfig();
        if (!self::$_sSeparator) {
            $this->setSeparator( $myConfig->getConfigParam( 'sSEOSeparator' ) );
        }
        if (!self::$_sPrefix) {
            $this->setPrefix( $myConfig->getConfigParam( 'sSEOuprefix' ) );
        }
        $this->setReservedWords( $myConfig->getConfigParam( 'aSEOReservedWords' ) );
    }

    /**
     * Moves current seo record to seo history table
     *
     * @param string $sId     object id
     * @param int    $iShopId active shop id
     * @param int    $iLang   object language
     * @param string $sType   object type (if you pass real object - type is not necessary)
     * @param string $sNewId  new object id, mostly used for static url updates (optional)
     *
     * @return null
     */
    protected function _copyToHistory( $sId, $iShopId, $iLang, $sType = null, $sNewId = null )
    {
        $oDb = oxDb::getDb();
        $sObjectid = $sNewId?$oDb->quote( $sNewId ):'oxobjectid';
        $sType     = $sType?"oxtype =".$oDb->quote( $sType )." and":'';
        $iLang     = (int) $iLang;

        // moving
        $sSub = "select $sObjectid, MD5( LOWER( oxseourl ) ), oxshopid, oxlang, now() from oxseo
                 where {$sType} oxobjectid = ".$oDb->quote( $sId )." and oxshopid = ".$oDb->quote( $iShopId )." and
                 oxlang = {$iLang} and oxexpired = '1'";
        $sQ   = "replace oxseohistory ( oxobjectid, oxident, oxshopid, oxlang, oxinsert ) {$sSub}";
        $oDb->execute( $sQ );
    }

    /**
     * Generates dynamic url object id (calls oxseoencoder::_getStaticObjectId)
     *
     * @param int    $iShopId shop id
     * @param string $sStdUrl standard (dynamic) url
     *
     * @return string
     */
    public function getDynamicObjectId( $iShopId, $sStdUrl )
    {
        return $this->_getStaticObjectId( $iShopId, $sStdUrl );
    }

    /**
     * Returns dynamic object SEO URI
     *
     * @param string $sStdUrl standard url
     * @param string $sSeoUrl seo uri
     * @param int    $iLang   active language
     *
     * @return string
     */
    protected function _getDynamicUri( $sStdUrl, $sSeoUrl, $iLang )
    {
        $iShopId = $this->getConfig()->getShopId();

        $sStdUrl   = $this->_trimUrl( $sStdUrl );
        $sObjectId = $this->getDynamicObjectId( $iShopId, $sStdUrl );
        $sSeoUrl   = $this->_prepareUri( $this->addLanguageParam( $sSeoUrl, $iLang ), $iLang );

        //load details link from DB
        $sOldSeoUrl = $this->_loadFromDb( 'dynamic', $sObjectId, $iLang );
        if ( $sOldSeoUrl === $sSeoUrl ) {
            $sSeoUrl = $sOldSeoUrl;
        } else {

            if ( $sOldSeoUrl ) {
                // old must be transferred to history
                $this->_copyToHistory( $sObjectId, $iShopId, $iLang, 'dynamic' );
            }

            // creating unique
            $sSeoUrl = $this->_processSeoUrl( $sSeoUrl, $sObjectId, $iLang );

            // inserting
            $this->_saveToDb( 'dynamic', $sObjectId, $sStdUrl, $sSeoUrl, $iLang, $iShopId );
        }

        return $sSeoUrl;
    }

    /**
     * Returns SEO url with shop's path + additional params ( oxseoencoder:: _getAddParams)
     *
     * @param string $sSeoUrl seo URL
     * @param int    $iLang   active language
     * @param bool   $blSsl   forces to build ssl url
     *
     * @return string
     */
    protected function _getFullUrl( $sSeoUrl, $iLang = null, $blSsl = false )
    {
        if ( $sSeoUrl ) {
            $sFullUrl = ( $blSsl ? $this->getConfig()->getSslShopUrl( $iLang ) : $this->getConfig()->getShopUrl( $iLang, false ) ) . $sSeoUrl;
            return oxRegistry::get("oxUtilsUrl")->processSeoUrl( $sFullUrl );
        }
        return false;
    }

    /**
     * _getSeoIdent returns seo ident for db search
     *
     * @param string $sSeoUrl seo url
     *
     * @access protected
     *
     * @return string
     */
    protected function _getSeoIdent( $sSeoUrl )
    {
        return md5( strtolower( $sSeoUrl ) );
    }

    /**
     * Returns SEO static uri
     *
     * @param string $sStdUrl standard page url
     * @param int    $iShopId active shop id
     * @param int    $iLang   active language
     *
     * @return string
     */
    protected function _getStaticUri( $sStdUrl, $iShopId, $iLang )
    {
        $sStdUrl = $this->_trimUrl( $sStdUrl, $iLang );
        return $this->_loadFromDb( 'static', $this->_getStaticObjectId( $iShopId, $sStdUrl ), $iLang );
    }

    /**
     * Returns target "extension"
     *
     * @return null
     */
    protected function _getUrlExtension()
    {
        return;
    }

    /**
     * _getUniqueSeoUrl returns possibly modified url
     * for not to be same as already existing in db
     *
     * @param string $sSeoUrl     seo url
     * @param string $sObjectId   current object id, used to skip self in query
     * @param int    $iObjectLang object language id
     *
     * @access protected
     *
     * @return string
     */
    protected function _getUniqueSeoUrl( $sSeoUrl, $sObjectId = null, $iObjectLang = null )
    {
        $sSeoUrl = $this->_prepareUri( $sSeoUrl, $iObjectLang );
        $oStr = getStr();
        $sExt = '';
        if ( $oStr->preg_match( '/(\.html?|\/)$/i', $sSeoUrl, $aMatched ) ) {
            $sExt = $aMatched[0];
        }
        $sBaseSeoUrl = $sSeoUrl;
        if ( $sExt && $oStr->substr( $sSeoUrl, 0 - $oStr->strlen( $sExt ) ) == $sExt ) {
            $sBaseSeoUrl = $oStr->substr( $sSeoUrl, 0, $oStr->strlen( $sSeoUrl ) - $oStr->strlen( $sExt ) );
        }

        $iShopId = $this->getConfig()->getShopId();
        $iCnt = 0;
        $sCheckSeoUrl = $this->_trimUrl( $sSeoUrl );
        $sQ = "select 1 from oxseo where oxshopid = '{$iShopId}'";

        $oDb = oxDb::getDb();
        // skipping self
        if ( $sObjectId && isset($iObjectLang) ) {
            $iObjectLang = (int) $iObjectLang;
            $sQ .= " and not (oxobjectid = " . $oDb->quote( $sObjectId ) . " and oxlang = $iObjectLang)";
        }

        while ( $oDb->getOne( $sQ ." and oxident= " . $oDb->quote( $this->_getSeoIdent( $sCheckSeoUrl ) ) ) ) {
            $sAdd = '';
            if ( self::$_sPrefix ) {
                $sAdd = self::$_sSeparator . self::$_sPrefix;
            }
            if ( $iCnt ) {
                $sAdd .= self::$_sSeparator . $iCnt;
            }
            ++$iCnt;

            $sSeoUrl = $sBaseSeoUrl . $sAdd . $sExt;
            $sCheckSeoUrl = $this->_trimUrl( $sSeoUrl );
        }
        return $sSeoUrl;
    }

    /**
     * check if seo url exist and is fixed
     *
     * @param string $sType               object type
     * @param string $sId                 object identifier
     * @param int    $iLang               active language id
     * @param mixed  $iShopId             active shop id
     * @param string $sParams             additional seo params. optional (mostly used for db indexing)
     * @param bool   $blStrictParamsCheck strict parameters check
     *
     * @access protected
     *
     * @return bool
     */
    protected function _isFixed( $sType, $sId, $iLang, $iShopId = null, $sParams = null, $blStrictParamsCheck = true)
    {
        if ( $iShopId === null ) {
            $iShopId = $this->getConfig()->getShopId();
        }
        $iLang = (int) $iLang;

        if ( !isset( self::$_aFixedCache[$sType][$sShopId][$sId][$iLang] ) ) {
            $oDb = oxDb::getDb();

            $sQ = "SELECT `oxfixed`
                FROM `oxseo`
                WHERE `oxtype` = ".$oDb->quote( $sType )."
                   AND `oxobjectid` = ".$oDb->quote( $sId ) ."
                   AND `oxshopid` = ".$oDb->quote( $iShopId )."
                   AND `oxlang` = '{$iLang}'";

            $sParams = $sParams ? $oDb->quote( $sParams ) : "''";
            if ( $sParams && $blStrictParamsCheck ) {
                $sQ .= " AND `oxparams` = {$sParams}";
            } else {
                $sQ .= " ORDER BY `oxparams` ASC";
            }
            $sQ .= " LIMIT 1";

            self::$_aFixedCache[$sType][$sShopId][$sId][$iLang] = (bool) $oDb->getOne( $sQ );
        }
        return self::$_aFixedCache[$sType][$sShopId][$sId][$iLang];
    }

    /**
     * Returns cache key (in non admin mode)
     *
     * @param string $sType   object type
     * @param int    $iLang   active language id
     * @param mixed  $iShopId active shop id
     * @param string $sParams additional seo params. optional (mostly used for db indexing)
     *
     * @return string
     */
    protected function _getCacheKey( $sType, $iLang = null, $iShopId = null, $sParams = null )
    {
        $blAdmin = $this->isAdmin();
        if ( !$blAdmin && $sType !== "oxarticle" ) {
            return $sType . ( (int) $iLang ) . ( (int) $iShopId ) . "seo";
        }

        // use cache in non admin mode
        if ( self::$_sCacheKey === null ) {
            self::$_sCacheKey = false;
            if ( !$blAdmin && ( $oView = $this->getConfig()->getActiveView() ) ) {
                self::$_sCacheKey = md5( $oView->getViewId() ) . "seo";
            }
        }
        return self::$_sCacheKey;
    }

    /**
     * Loads seo data from cache for active view (in non admin mode)
     *
     * @param string $sCacheIdent cache identifier
     * @param string $sType       object type
     * @param int    $iLang       active language id
     * @param mixed  $iShopId     active shop id
     * @param string $sParams     additional seo params. optional (mostly used for db indexing)
     *
     * @return string
     */
    protected function _loadFromCache( $sCacheIdent, $sType, $iLang = null, $iShopId = null, $sParams = null )
    {
        if ( !$this->getConfig()->getConfigParam( 'blEnableSeoCache' ) ) {
            return false;
        }

        startProfile( "seoencoder_loadFromCache" );

        $sCacheKey = $this->_getCacheKey( $sType, $iLang, $iShopId, $sParams );
        $sCache = false;

        if ( $sCacheKey && !isset( self::$_aCache[$sCacheKey] ) ) {
            self::$_aCache[$sCacheKey] = oxRegistry::getUtils()->fromFileCache( $sCacheKey );
        }

        if ( isset( self::$_aCache[$sCacheKey] ) && isset( self::$_aCache[$sCacheKey][$sCacheIdent] ) ) {
            $sCache = self::$_aCache[$sCacheKey][$sCacheIdent];
        }

        stopProfile( "seoencoder_loadFromCache" );
        return $sCache;
    }

    /**
     * Saves seo cache data for active view (in non admin mode)
     *
     * @param string $sCacheIdent cache identifier
     * @param string $sCache      cacheable data
     * @param string $sType       object type
     * @param int    $iLang       active language id
     * @param mixed  $iShopId     active shop id
     * @param string $sParams     additional seo params. optional (mostly used for db indexing)
     *
     * @return bool
     */
    protected function _saveInCache( $sCacheIdent, $sCache, $sType, $iLang = null, $iShopId = null, $sParams = null )
    {
        if ( !$this->getConfig()->getConfigParam( 'blEnableSeoCache' ) ) {
            return false;
        }

        startProfile( "seoencoder_saveInCache" );

        $blSaved = false;
        if ( $sCache && ( $sCacheKey = $this->_getCacheKey( $sType, $iLang, $iShopId, $sParams ) ) !== false ) {
            self::$_aCache[$sCacheKey][$sCacheIdent] = $sCache;
            $blSaved = oxRegistry::getUtils()->toFileCache( $sCacheKey, self::$_aCache[$sCacheKey] );
        }

        stopProfile( "seoencoder_saveInCache" );
        return $blSaved;
    }

    /**
     * _loadFromDb loads data from oxseo table if exists
     * returns oxseo url
     *
     * @param string $sType               object type
     * @param string $sId                 object identifier
     * @param int    $iLang               active language id
     * @param mixed  $iShopId             active shop id
     * @param string $sParams             additional seo params. optional (mostly used for db indexing)
     * @param bool   $blStrictParamsCheck strict parameters check
     *
     * @access protected
     *
     * @return string || false
     */
    protected function _loadFromDb( $sType, $sId, $iLang, $iShopId = null, $sParams = null, $blStrictParamsCheck = true)
    {

        if ( $iShopId === null ) {
            $iShopId = $this->getConfig()->getShopId();
        }

        $iLang = (int) $iLang;
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );

        $sQ = "
            SELECT
                `oxfixed`,
                `oxseourl`,
                `oxexpired`,
                `oxtype`
            FROM `oxseo`
            WHERE `oxtype` = ".$oDb->quote( $sType )."
               AND `oxobjectid` = ".$oDb->quote( $sId ) ."
               AND `oxshopid` = ".$oDb->quote( $iShopId )."
               AND `oxlang` = '{$iLang}'";

        $sParams = $sParams ? $sParams : '';
        if ( $sParams && $blStrictParamsCheck ) {
            $sQ .= " AND `oxparams` = '{$sParams}'";
        } else {
            $sQ .= " ORDER BY `oxparams` ASC";
        }
        $sQ .= " LIMIT 1";


        // caching to avoid same queries..
        $sIdent = md5( $sQ );

        // looking in cache
        if ( ( $sSeoUrl = $this->_loadFromCache( $sIdent, $sType, $iLang, $iShopId, $sParams ) ) === false ) {
            $oRs = $oDb->select( $sQ );

            if ( $oRs && $oRs->recordCount() > 0 && !$oRs->EOF ) {
                // moving expired static urls to history ..
                if ( $oRs->fields['oxexpired'] && ( $oRs->fields['oxtype'] == 'static' || $oRs->fields['oxtype'] == 'dynamic' ) ) {
                    // if expired - copying to history, marking as not expired
                    $this->_copyToHistory( $sId, $iShopId, $iLang );
                    $oDb->execute( "update oxseo set oxexpired = 0 where oxobjectid = ".$oDb->quote( $sId )." and oxlang = '{$iLang}'" );
                    $sSeoUrl = $oRs->fields['oxseourl'];
                } elseif ( !$oRs->fields['oxexpired'] || $oRs->fields['oxfixed'] ) {
                    // if seo url is available and is valid
                    $sSeoUrl = $oRs->fields['oxseourl'];
                }

                // storing in cache
                $this->_saveInCache( $sIdent, $sSeoUrl, $sType, $iLang, $iShopId, $sParams );
            }
        }
        return $sSeoUrl;
    }

    /**
     * cached getter: check root directory php file names for them not to be in 1st part of seo url
     * because then apache will execute that php file instead of url parser
     *
     * @return array
     */
    protected function _getReservedEntryKeys()
    {
        if ( !isset( self::$_aReservedEntryKeys ) || !is_array( self::$_aReservedEntryKeys ) ) {
            $sDir = getShopBasePath();
            self::$_aReservedEntryKeys = array_map('preg_quote', self::$_aReservedWords, array('#'));
            $oStr = getStr();
            foreach ( glob( "$sDir/*" ) as $sFile ) {
                if ( $oStr->preg_match( '/^(.+)\.php[0-9]*$/i', basename( $sFile ), $aMatches ) ) {
                    self::$_aReservedEntryKeys[] = preg_quote( $aMatches[0], '#' );
                    self::$_aReservedEntryKeys[] = preg_quote( $aMatches[1], '#' );
                } elseif ( is_dir( $sFile ) ) {
                    self::$_aReservedEntryKeys[] = preg_quote( basename( $sFile ), '#' );
                }
            }
            self::$_aReservedEntryKeys = array_unique(self::$_aReservedEntryKeys);
        }
        return self::$_aReservedEntryKeys;
    }

    /**
     * Makes safe seo uri - removes unsupported/reserved characters
     *
     * @param string $sUri  seo uri
     * @param int    $iLang language ID, for which URI should be prepared
     *
     * @return string
     */
    protected function _prepareUri( $sUri, $iLang = false )
    {
        // decoding entities
        $sUri = $this->encodeString( $sUri, true, $iLang );

        // basic string preparation
        $oStr = getStr();
        $sUri = $oStr->strip_tags( $sUri );

        // if found ".html" or "/" at the end - removing it temporary
        $sExt = $this->_getUrlExtension();
        if ($sExt === null) {
            $aMatched = array();
            if ( $oStr->preg_match( '/(\.html?|\/)$/i', $sUri, $aMatched ) ) {
                $sExt = $aMatched[0];
            } else {
                $sExt = '/';
            }
        }
        if ( $sExt && $oStr->substr( $sUri, 0 - $oStr->strlen( $sExt ) ) == $sExt ) {
            $sUri = $oStr->substr( $sUri, 0, $oStr->strlen( $sUri ) - $oStr->strlen( $sExt ) );
        }

        // removing any special characters
        // #0004282 bugfix, php <5.3 does not escape - char, so we do it manually
        $sQuotedPrefix = preg_quote( self::$_sSeparator . self::$_sPrefix, '/');
        if ( phpversion() < '5.3' ) {
            $sQuotedPrefix = str_replace( '-', '\-', $sQuotedPrefix );
        }
        $sRegExp = '/[^A-Za-z0-9' . $sQuotedPrefix . '\/]+/';
        $sUri  = $oStr->preg_replace( array( "/\W*\/\W*/", $sRegExp ), array( "/", self::$_sSeparator ), $sUri );

        // SEO id is empty ?
        if ( !$sUri && self::$_sPrefix ) {
            $sUri = $this->_prepareUri( self::$_sPrefix, $iLang );
        }

        $sAdd = '';
        if ('/' != self::$_sSeparator) {
            $sAdd = self::$_sSeparator . self::$_sPrefix;
            $sUri = trim($sUri, self::$_sSeparator);
        } else {
            $sAdd = '_' . self::$_sPrefix;
        }

        // binding the ending back
        $sUri .= $sExt;

        // fix for not having url, which executes through /other/ script then seo decoder
        $sUri = $oStr->preg_replace( "#^(/*)(".implode('|', $this->_getReservedEntryKeys()).")(/|$)#i", "\$1\$2$sAdd\$3", $sUri );

        // cleaning
        // #0004282 bugfix, php < 5.3 does not escape - char, so we do it manually\
        $sQuotedSeparator = preg_quote( self::$_sSeparator, '/');
        if ( phpversion() < '5.3' ) {
            $sQuotedSeparator = str_replace( '-', '\-', $sQuotedSeparator );
        }
        return $oStr->preg_replace( array( '|//+|', '/' . $sQuotedSeparator . $sQuotedSeparator .'+/' ),
                             array( '/', self::$_sSeparator ), $sUri );
    }


    /**
     * Prepares and returns formatted object SEO id
     *
     * @param string $sTitle         Original object title
     * @param bool   $blSkipTruncate Truncate title into defined lenght or not
     * @param int    $iLang          language ID, for which to prepare the title
     *
     * @return string
     */
    protected function _prepareTitle( $sTitle, $blSkipTruncate = false, $iLang = false )
    {
        $sTitle = $this->encodeString( $sTitle, true, $iLang );
        $sSep = self::$_sSeparator;
        if (!$sSep || ('/' == $sSep)) {
            $sSep = '_';
        }

        $sRegExp = '/[^A-Za-z0-9\/'.preg_quote( self::$_sPrefix, '/').preg_quote($sSep, '/').']+/';
        $sTitle = preg_replace( array("#/+#", $sRegExp, "# +#", "#(".preg_quote($sSep, '/').")+#"), $sSep, $sTitle );

        $oStr = getStr();
        // smart truncate
        if ( !$blSkipTruncate && $oStr->strlen( $sTitle ) > $this->_iIdLength ) {
            $iFirstSpace = $oStr->strpos( $sTitle, $sSep, $this->_iIdLength);
            if ( $iFirstSpace !== false ) {
                $sTitle = $oStr->substr( $sTitle, 0, $iFirstSpace );
            }
        }

        $sTitle = trim( $sTitle, $sSep );

        if (!$sTitle) {
            return self::$_sPrefix;
        }
        // cleaning
        return $sTitle;
    }


    /**
     * _saveToDb saves values to seo table
     *
     * @param string $sType     url type (static, dynamic, oxarticle etc)
     * @param string $sObjectId object identifier
     * @param string $sStdUrl   standard url
     * @param string $sSeoUrl   seo url
     * @param int    $iLang     active object language
     * @param mixed  $iShopId   active object shop id
     * @param bool   $blFixed   seo entry marker. if true, entry should not be automatically changed
     * @param string $sParams   additional seo params. optional (mostly used for db indexing)
     *
     * @access protected
     *
     * @return void
     */
    protected function _saveToDb( $sType, $sObjectId, $sStdUrl, $sSeoUrl, $iLang, $iShopId = null, $blFixed = null, $sParams = null )
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        if ( $iShopId === null ) {
            $iShopId = $this->getConfig()->getShopId();
        }

        $iLang = (int) $iLang;

        $sStdUrl = $this->_trimUrl( $sStdUrl );
        $sSeoUrl = $this->_trimUrl( $sSeoUrl );
        $sIdent  = $this->_getSeoIdent( $sSeoUrl );

        // transferring old url, thus current url will be regenerated
        $sQtedObjectId = $oDb->quote( $sObjectId );
        $iQtedShopId   = $oDb->quote( $iShopId );
        $sQtedType     = $oDb->quote( $sType );
        $sQtedSeoUrl   = $oDb->quote( $sSeoUrl );
        $sQtedStdUrl   = $oDb->quote( $sStdUrl );
        $sQtedParams   = $oDb->quote( $sParams );
        $sQtedIdent    = $oDb->quote( $sIdent );

        // transferring old url, thus current url will be regenerated
        $sQ  = "select oxfixed, oxexpired, ( oxstdurl like {$sQtedStdUrl} ) as samestdurl,
                oxseourl like {$sQtedSeoUrl} as sameseourl from oxseo where oxtype = {$sQtedType} and
                oxobjectid = {$sQtedObjectId} and oxshopid = {$iQtedShopId}  and oxlang = {$iLang} ";

        $sQ .= $sParams ? " and oxparams = {$sQtedParams} " : '';
        $sQ .= "limit 1";
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oRs = $oDb->select( $sQ );
        if ( $oRs && $oRs->recordCount() > 0 && !$oRs->EOF ) {
            if ( $oRs->fields['samestdurl'] && $oRs->fields['sameseourl'] && $oRs->fields['oxexpired'] ) {
                // fixed state change
                $sFixed = isset( $blFixed ) ? ", oxfixed = " . ( (int) $blFixed ) . " " : '';
                // nothing was changed - setting expired status back to 0
                $sSql  = "update oxseo set oxexpired = 0 {$sFixed} where oxtype = {$sQtedType} and
                          oxobjectid = {$sQtedObjectId} and oxshopid = {$iQtedShopId} and oxlang = {$iLang} ";
                $sSql .= $sParams ? " and oxparams = {$sQtedParams} " : '';
                $sSql .= " limit 1";

                return $oDb->execute( $sSql );
            } elseif ( $oRs->fields['oxexpired'] ) {
                // copy to history
                $this->_copyToHistory( $sObjectId, $iShopId, $iLang, $sType );
            }
        }

        // inserting new or updating
        $sParams = $sParams ? $oDb->quote( $sParams ) :'""';
        $blFixed = (int) $blFixed;

        $sQ  = "insert into oxseo
                    (oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype, oxfixed, oxexpired, oxparams)
                values
                    ( {$sQtedObjectId}, {$sQtedIdent}, {$iQtedShopId}, {$iLang}, {$sQtedStdUrl}, {$sQtedSeoUrl}, {$sQtedType}, '$blFixed', '0', {$sParams} )
                on duplicate key update
                    oxident = {$sQtedIdent}, oxstdurl = {$sQtedStdUrl}, oxseourl = {$sQtedSeoUrl}, oxfixed = '$blFixed', oxexpired = '0'";

        return $oDb->execute( $sQ );
    }

    /**
     * Removes shop path part and session id from given url
     *
     * @param string $sUrl  url to clean bad chars
     * @param int    $iLang active language
     *
     * @access protected
     *
     * @return string
     */
    protected function _trimUrl( $sUrl, $iLang = null )
    {
        $myConfig = $this->getConfig();
        $oStr = getStr();
        $sUrl = str_replace( array( $myConfig->getShopUrl( $iLang, false ), $myConfig->getSslShopUrl( $iLang ) ), '', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\.]+&?(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)shp=[0-9]+&?(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)lang=[0-9]+&?(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)cur=[0-9]+&?(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)stoken=[a-z0-9]+&?(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)&(amp;)?/i', '\1', $sUrl );
        $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)+$/i', '', $sUrl );
        $sUrl = trim( $sUrl );

        // max length <= $this->_iMaxUrlLength
        $iLength = $this->_getMaxUrlLength();
        if ( $oStr->strlen( $sUrl ) > $iLength ) {
            $sUrl = $oStr->substr( $sUrl, 0, $iLength );
        }

        return $sUrl;
    }

    /**
     * Returns maximum seo/dynamic url length
     *
     * @return int
     */
    protected function _getMaxUrlLength()
    {
        if ( $this->_iMaxUrlLength === null ) {
            // max length <= 2048 / custom
            $this->_iMaxUrlLength = $this->getConfig()->getConfigParam( "iMaxSeoUrlLength" ) ;
            if ( !$this->_iMaxUrlLength ) {
                $this->_iMaxUrlLength = 2048;
            }
        }
        return $this->_iMaxUrlLength;
    }

    /**
     * Replaces special chars in text
     *
     * @param string $sString        string to encode
     * @param bool   $blReplaceChars is true, replaces user defined (oxlang::getSeoReplaceChars) characters into alternative
     * @param int    $iLang          language, for which to encode the string
     *
     * @return string
     */
    public function encodeString( $sString, $blReplaceChars = true, $iLang = false )
    {
        // decoding entities
        $sString = getStr()->html_entity_decode( $sString );

        if ( $blReplaceChars ) {
            if ($iLang === false || !is_numeric($iLang)) {
                $iLang = oxRegistry::getLang()->getEditLanguage();
            }

            if ( $aReplaceChars = oxRegistry::getLang()->getSeoReplaceChars($iLang) ) {
                $sString = str_replace( array_keys( $aReplaceChars ), array_values( $aReplaceChars ), $sString );
            }
        }


        // special chars
        $aReplaceWhat = array( '&amp;', '&quot;', '&#039;', '&lt;', '&gt;' );
        return str_replace( $aReplaceWhat, '', $sString );
    }

    /**
     * Sets SEO separator
     *
     * @param string $sSeparator SEO seperator
     *
     * @return null
     */
    public function setSeparator( $sSeparator = null )
    {
        self::$_sSeparator = $sSeparator;
        if ( !self::$_sSeparator ) {
            self::$_sSeparator = '-';
        }
    }

    /**
     * Sets SEO prefix
     *
     * @param string $sPrefix SEO prefix
     *
     * @return null
     */
    public function setPrefix( $sPrefix )
    {
        if ($sPrefix) {
            self::$_sPrefix = $sPrefix;
        } else {
            self::$_sPrefix = 'oxid';
        }
    }

    /**
     * sets seo id length
     *
     * @param string $iIdlength id length
     *
     * @return null
     */
    public function setIdLength( $iIdlength = null )
    {
        if ( isset( $iIdlength ) ) {
            $this->_iIdLength = $iIdlength;
        }
    }

    /**
     * Sets array of words which must be checked before building seo url
     * These words are appended by seo prefix if they are the initial uri segment
     *
     * @param array $aReservedWords reserved words
     *
     * @return null
     */
    public function setReservedWords( $aReservedWords )
    {
        self::$_aReservedWords = array_merge( self::$_aReservedWords, $aReservedWords );
    }


    /**
     * Marks object seo records as expired
     *
     * @param string $sId      changed object id. If null is passed, object dependency is not checked
     * @param int    $iShopId  active shop id. Shop id must be passed uf you want to do shop level update (default null)
     * @param int    $iExpStat expiration status: 1 - standard expiration
     * @param int    $iLang    active language (optiona;)
     * @param string $sParams  additional params
     *
     * @return null
     */
    public function markAsExpired( $sId, $iShopId = null, $iExpStat = 1, $iLang = null, $sParams = null )
    {
        $oDb = oxDb::getDb();
        $sWhere  = $sId ? "where oxobjectid =  " . $oDb->quote( $sId ) : '';
        $sWhere .= isset( $iShopId ) ? ( $sWhere ? " and oxshopid = ". $oDb->quote( $iShopId ) : "where oxshopid = ". $oDb->quote( $iShopId ) ) : '';
        $sWhere .= $iLang ? ( $sWhere ? " and oxlang = '{$iLang}'" : "where oxlang = '{$iLang}'" ) : '';
        $sWhere .= $sParams ? ( $sWhere ? " and {$sParams}" : "where {$sParams}" ) : '';

        $sQ = "update oxseo set oxexpired =  " . $oDb->quote( $iExpStat ) . " $sWhere ";
        $oDb->execute( $sQ );
    }

    /**
     * Loads if exists or prepares and saves new seo url for passed object
     *
     * @param oxbase $oObject object to prepare seo data
     * @param string $sType   type of object (oxvendor/oxcategory)
     * @param string $sStdUrl stanradr url
     * @param string $sSeoUrl seo uri
     * @param string $sParams additional params, liek page number etc. mostly used by mysql for indexes
     * @param int    $iLang   language
     * @param bool   $blFixed fixed url marker (default is false)
     *
     * @return string
     */
    protected function _getPageUri( $oObject, $sType, $sStdUrl, $sSeoUrl, $sParams, $iLang = null, $blFixed = false )
    {
        if ( !isset( $iLang ) ) {
            $iLang = $oObject->getLanguage();
        }
        $iShopId = $this->getConfig()->getShopId();

        //load page link from DB
        $sOldSeoUrl = $this->_loadFromDb( $sType, $oObject->getId(), $iLang, $iShopId, $sParams );
        if ( !$sOldSeoUrl ) {
            // generating new..
            $sSeoUrl = $this->_processSeoUrl( $sSeoUrl, $oObject->getId(), $iLang );
            $this->_saveToDb( $sType, $oObject->getId(), $sStdUrl, $sSeoUrl, $iLang, $iShopId, (int) $blFixed, $sParams );
        } else {
            // using old
            $sSeoUrl = $sOldSeoUrl;
        }
        return $sSeoUrl;
    }

    /**
     * Generates static url object id
     *
     * @param int    $iShopId shop id
     * @param string $sStdUrl standard (dynamic) url
     *
     * @return string
     */
    protected function _getStaticObjectId( $iShopId, $sStdUrl )
    {
        return md5( strtolower ( $iShopId . $this->_trimUrl( $sStdUrl ) ) );
    }

    /**
     * Static url encoder
     *
     * @param array $aStaticUrl static url info (contains standard URL and urls for each language)
     * @param int   $iShopId    active shop id
     * @param int   $iLang      active language
     *
     * @return null
     */
    public function encodeStaticUrls( $aStaticUrl, $iShopId, $iLang )
    {
        $oDb = oxDb::getDb();
        $sValues = '';
        $sOldObjectId = null;

        // standard url
        $sStdUrl = $this->_trimUrl( trim( $aStaticUrl['oxseo__oxstdurl'] ) );
        $sObjectId = $aStaticUrl['oxseo__oxobjectid'];

        if ( !$sObjectId || $sObjectId == '-1' ) {
            $sObjectId = $this->_getStaticObjectId( $iShopId, $sStdUrl );
        } else {
            // marking entry as needs to move to history
            $sOldObjectId = $sObjectId;

            // if std url does not match old
            if ( $this->_getStaticObjectId( $iShopId, $sStdUrl ) != $sObjectId ) {
                $sObjectId = $this->_getStaticObjectId( $iShopId, $sStdUrl );
            }
        }

        foreach ( $aStaticUrl['oxseo__oxseourl'] as $iLang => $sSeoUrl ) {

            $iLang = (int) $iLang;

            // generating seo url
            $sSeoUrl = $this->_trimUrl( $sSeoUrl );
            if ( $sSeoUrl ) {
                $sSeoUrl = $this->_processSeoUrl( $sSeoUrl, $sObjectId, $iLang );
            }


            if ( $sOldObjectId ) {
                // move changed records to history
                if ( !$oDb->getOne( "select (" . $oDb->quote( $sSeoUrl ) . " like oxseourl) & (" . $oDb->quote( $sStdUrl ) . " like oxstdurl) from oxseo where oxobjectid = ".$oDb->quote( $sOldObjectId )." and oxshopid = '{$iShopId}' and oxlang = '{$iLang}' ", false, false) ) {
                    $this->_copyToHistory( $sOldObjectId, $iShopId, $iLang, 'static', $sObjectId );
                }
            }

            if ( !$sSeoUrl || !$sStdUrl ) {
                continue;
            }

            $sIdent = $this->_getSeoIdent( $sSeoUrl );

            if ( $sValues ) {
                $sValues .= ', ';
            }

            $sValues .= "( " . $oDb->quote( $sObjectId ) . ", " . $oDb->quote( $sIdent ) . ", " . $oDb->quote( $iShopId ).", '{$iLang}', " . $oDb->quote( $sStdUrl ) . ", " . $oDb->quote( $sSeoUrl ) . ", 'static' )";
        }

        // must delete old before insert/update
        if ( $sOldObjectId ) {
            $oDb->execute( "delete from oxseo where oxobjectid in ( " . $oDb->quote( $sOldObjectId ) . ", " . $oDb->quote( $sObjectId ) . " )" );
        }

        // (re)inserting
        if ( $sValues ) {

            $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype ) values {$sValues} ";
            $oDb->execute( $sQ );
        }

        return $sObjectId;
    }

    /**
     * Method copies static urls from base shop to newly created
     *
     * @param int $iShopId new created shop id
     *
     * @return null
     */
    public function copyStaticUrls( $iShopId )
    {
        $iBaseShopId = $this->getConfig()->getBaseShopId();
        if ( $iShopId != $iBaseShopId ) {
            $oDb = oxDb::getDb();
            foreach (array_keys(oxRegistry::getLang()->getLanguageIds()) as $iLang) {
                $sQ = "insert into oxseo ( oxobjectid, oxident, oxshopid, oxlang, oxstdurl, oxseourl, oxtype )
                       select MD5( LOWER( CONCAT( " . $oDb->quote( $iShopId ) . ", oxstdurl ) ) ), MD5( LOWER( oxseourl ) ),
                       " . $oDb->quote( $iShopId ) . ", oxlang, oxstdurl, oxseourl, oxtype from oxseo where oxshopid = '{$iBaseShopId}' and oxtype = 'static' and oxlang='$iLang' ";
                $oDb->execute( $sQ );
            }
        }
    }

    /**
     * Returns static url for passed standard link (if available)
     *
     * @param string $sStdUrl standard Url
     * @param int    $iLang   active language (optional). default null
     * @param int    $iShopId active shop id (optional). default null
     *
     * @return string
     */
    public function getStaticUrl( $sStdUrl, $iLang = null, $iShopId = null )
    {
        if (!isset($iShopId)) {
            $iShopId = $this->getConfig()->getShopId();
        }
        if (!isset($iLang)) {
            $iLang   = oxRegistry::getLang()->getEditLanguage();
        }

        if ( isset($this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId])) {
            return $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId];
        }

        $sFullUrl = '';
        if ( ( $sSeoUrl = $this->_getStaticUri( $sStdUrl, $iShopId, $iLang ) ) ) {
            $sFullUrl = $this->_getFullUrl( $sSeoUrl, $iLang, strpos( $sStdUrl, "https:" ) === 0 );
        }


        $this->_aStaticUrlCache[$sStdUrl][$iLang][$iShopId] = $sFullUrl;

        return $sFullUrl;
    }

    /**
     * Adds new seo entry to db
     *
     * @param string $sObjectId    objects id
     * @param int    $iShopId      shop id
     * @param int    $iLang        objects language
     * @param string $sStdUrl      default url
     * @param string $sSeoUrl      seo url
     * @param string $sType        object type
     * @param bool   $blFixed      marker to keep seo config unchangeable
     * @param string $sKeywords    seo keywords
     * @param string $sDescription seo description
     * @param string $sParams      additional seo params. optional (mostly used for db indexing)
     * @param bool   $blExclude    exclude language prefix while building seo url
     * @param string $sAltObjectId alternative object id used while saving meta info (used to override object id when saving tags related info)
     *
     * @return null
     */
    public function addSeoEntry( $sObjectId, $iShopId, $iLang, $sStdUrl, $sSeoUrl, $sType, $blFixed = 1, $sKeywords = '', $sDescription = '', $sParams = '', $blExclude = false, $sAltObjectId = null )
    {
        $sSeoUrl = $this->_processSeoUrl( $this->_trimUrl( $sSeoUrl ? $sSeoUrl : $this->_getAltUri( $sAltObjectId ? $sAltObjectId : $sObjectId, $iLang ) ), $sObjectId, $iLang, $blExclude );
        if ( $this->_saveToDb( $sType, $sObjectId, $sStdUrl, $sSeoUrl, $iLang, $iShopId, $blFixed, $sParams ) ) {

            $oDb = oxDb::getDb();

            //
            $sQtedObjectId = $oDb->quote( $sAltObjectId ? $sAltObjectId : $sObjectId );
            $iQtedShopId   = $oDb->quote( $iShopId );

            $oStr = getStr();
            if ( $sKeywords !== false ) {
                $sKeywords = $oDb->quote( $oStr->htmlspecialchars( $this->encodeString( $oStr->strip_tags( $sKeywords ), false, $iLang ) ) );
            }

            if ( $sDescription !== false ) {
                $sDescription = $oDb->quote( $oStr->htmlspecialchars( $oStr->strip_tags( $sDescription ) ) );
            }

            $sQ = "insert into oxobject2seodata
                       ( oxobjectid, oxshopid, oxlang, oxkeywords, oxdescription )
                   values
                       ( {$sQtedObjectId}, {$iQtedShopId}, {$iLang}, ".( $sKeywords ? $sKeywords : "''" ).", ".( $sDescription ? $sDescription : "''" )." )
                   on duplicate key update
                       oxkeywords = ".( $sKeywords ? $sKeywords : "oxkeywords" ).", oxdescription = ".( $sDescription ? $sDescription : "oxdescription" );
            $oDb->execute( $sQ );
        }
    }

    /**
     * Returns alternative uri used while updating seo
     *
     * @param string $sObjectId object id
     * @param int    $iLang     language id
     *
     * @return null
     */
    protected function _getAltUri( $sObjectId, $iLang )
    {
    }

    /**
     * Removes seo entry from db
     *
     * @param string $sObjectId objects id
     * @param int    $iShopId   shop id
     * @param int    $iLang     objects language
     * @param string $sType     object type
     *
     * @return null
     */
    public function deleteSeoEntry( $sObjectId, $iShopId, $iLang, $sType )
    {
        $oDb = oxDb::getDb();
        $sQ = "delete from oxseo where oxobjectid = " . $oDb->quote( $sObjectId ) . " and oxshopid = " . $oDb->quote( $iShopId ) . " and oxlang = " . $oDb->quote( $iLang ) . " and oxtype = " . $oDb->quote( $sType ) . " ";
        $oDb->execute( $sQ );
    }

    /**
     * Returns meta information for preferred object
     *
     * @param string $sObjectId information object id
     * @param string $sMetaType metadata type - "oxkeywords", "oxdescription"
     * @param int    $iShopId   active shop id [optional]
     * @param int    $iLang     active language [optional]
     *
     * @return string
     */
    public function getMetaData( $sObjectId, $sMetaType, $iShopId = null, $iLang = null )
    {
        $oDb = oxDb::getDb();

        $iShopId = ( !isset( $iShopId ) ) ? $this->getConfig()->getShopId():$iShopId;
        $iLang   = ( !isset( $iLang ) ) ? oxRegistry::getLang()->getObjectTplLanguage():((int) $iLang);
        return $oDb->getOne( "select {$sMetaType} from oxobject2seodata where oxobjectid = " . $oDb->quote( $sObjectId ) . " and oxshopid = " . $oDb->quote( $iShopId )." and oxlang = '{$iLang}'" );
    }

    /**
     * getDynamicUrl acts similar to static urls,
     * except, that dynamic url are not shown in admin
     * and they can be re-encoded by providing new seo url
     *
     * @param string $sStdUrl standard url
     * @param string $sSeoUrl part of URL query which will be attached to standard shop url
     * @param int    $iLang   active language
     *
     * @access public
     *
     * @return string
     */
    public function getDynamicUrl( $sStdUrl, $sSeoUrl, $iLang )
    {
        startProfile("getDynamicUrl");
        $sDynUrl = $this->_getFullUrl( $this->_getDynamicUri( $sStdUrl, $sSeoUrl, $iLang ), $iLang, strpos( $sStdUrl, "https:" ) === 0 );
        stopProfile("getDynamicUrl");
        return $sDynUrl;
    }

    /**
     * Searches for seo url in seo table. If not found - FALSE is returned
     *
     * @param string  $sStdUrl   standard url
     * @param integer $iLanguage language
     *
     * @return mixed
     */
    public function fetchSeoUrl( $sStdUrl, $iLanguage = null )
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $iLanguage = isset( $iLanguage ) ? ( (int) $iLanguage ) : oxRegistry::getLang()->getBaseLanguage();
        $sSeoUrl   = false;

        $sShopId = $this->getConfig()->getShopId();

        $sQ = "SELECT `oxseourl`, `oxlang` FROM `oxseo` WHERE `oxstdurl` = " . $oDb->quote( $sStdUrl ) . " AND `oxlang` = '$iLanguage' AND `oxshopid` = '$sShopId' LIMIT 1";

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oRs = $oDb->select( $sQ );

        if ( !$oRs->EOF ) {
            $sSeoUrl = $oRs->fields['oxseourl'];
        }

        return $sSeoUrl;
    }
}
