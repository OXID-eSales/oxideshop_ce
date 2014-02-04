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
class oxSeoDecoder extends oxSuperCfg
{
    /**
     * _parseStdUrl parses given url into array of params
     *
     * @param string $sUrl given url
     *
     * @access protected
     * @return array
     */
    public function parseStdUrl($sUrl)
    {
        $oStr = getStr();
        $aRet = array();
        $sUrl = $oStr->html_entity_decode( $sUrl );

        if ( ( $iPos = strpos( $sUrl, '?' ) ) !== false ) {
            parse_str( $oStr->substr( $sUrl, $iPos+1 ), $aRet );
        }

        return $aRet;
    }

    /**
     * Returns ident (md5 of seo url) to fetch seo data from DB
     *
     * @param string $sSeoUrl  seo url to calculate ident
     * @param bool   $blIgnore if FALSE - blocks from direct access when default language seo url with language ident executed
     *
     * @return string
     */
    protected function _getIdent( $sSeoUrl, $blIgnore = false )
    {
        return md5( strtolower( $sSeoUrl ) );
    }

    /**
     * decodeUrl decodes given url into oxid eShop required parameters
     * wich are returned as array
     *
     * @param string $sSeoUrl SEO url
     *
     * @access public
     * @return array || false
     */
    public function decodeUrl( $sSeoUrl )
    {
        $oStr = getStr();
        $sBaseUrl = $this->getConfig()->getShopURL();
        if ( $oStr->strpos( $sSeoUrl, $sBaseUrl ) === 0 ) {
            $sSeoUrl = $oStr->substr( $sSeoUrl, $oStr->strlen( $sBaseUrl ) );
        }
        $sSeoUrl = rawurldecode( $sSeoUrl );
        $iShopId = $this->getConfig()->getShopId();

        $sKey = $this->_getIdent( $sSeoUrl );
        $aRet = false;

        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $oRs = $oDb->select( "select oxstdurl, oxlang from oxseo where oxident=" . $oDb->quote( $sKey ) . " and oxshopid='$iShopId' limit 1");
        if ( !$oRs->EOF ) {
            // primary seo language changed ?
            $aRet = $this->parseStdUrl( $oRs->fields['oxstdurl'] );
            $aRet['lang'] = $oRs->fields['oxlang'];;
        }
        return $aRet;
    }

     /**
     * Checks if url is stored in history table and if it was found - tryes
     * to fetch new url from seo table
     *
     * @param string $sSeoUrl SEO url
     *
     * @access public
     * @return string || false
     */
    protected function _decodeOldUrl( $sSeoUrl )
    {
        $oStr = getStr();
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $sBaseUrl = $this->getConfig()->getShopURL();
        if ( $oStr->strpos( $sSeoUrl, $sBaseUrl ) === 0 ) {
            $sSeoUrl = $oStr->substr( $sSeoUrl, $oStr->strlen( $sBaseUrl ) );
        }
        $iShopId = $this->getConfig()->getShopId();
        $sSeoUrl = rawurldecode($sSeoUrl);

        $sKey = $this->_getIdent( $sSeoUrl, true );

        $sUrl = false;
        $oRs = $oDb->select( "select oxobjectid, oxlang from oxseohistory where oxident = " . $oDb->quote( $sKey ) . " and oxshopid = '{$iShopId}' limit 1");
        if ( !$oRs->EOF ) {
            // updating hit info (oxtimestamp field will be updated automatically)
            $oDb->execute( "update oxseohistory set oxhits = oxhits + 1 where oxident = " . $oDb->quote( $sKey ) . " and oxshopid = '{$iShopId}' limit 1" );

            // fetching new url
            $sUrl = $this->_getSeoUrl($oRs->fields['oxobjectid'], $oRs->fields['oxlang'], $iShopId);

            // appending with $_SERVER["QUERY_STRING"]
            $sUrl = $this->_addQueryString( $sUrl );
        }

        return $sUrl;
    }

    /**
     * Appends and returns given url with $_SERVER["QUERY_STRING"] value
     *
     * @param string $sUrl url to append
     *
     * @return string
     */
    protected function _addQueryString( $sUrl )
    {
        if ( ( $sQ = $_SERVER["QUERY_STRING"] ) ) {
            $sUrl = rtrim( $sUrl, "&?" );
            $sQ   = ltrim( $sQ, "&?" );

            $sUrl .= ( strpos( $sUrl, '?') === false ) ? "?" : "&";
            $sUrl .= $sQ;
        }
        return $sUrl;
    }

    /**
     * retrieve SEO url by its object id
     * normally used for getting the redirect url from seo history
     *
     * @param string $sObjectId object id
     * @param int    $iLang     language to fetch
     * @param int    $iShopId   shop id
     *
     * @return string
     */
    protected function _getSeoUrl($sObjectId, $iLang, $iShopId)
    {
        $oDb = oxDb::getDb( oxDb::FETCH_MODE_ASSOC );
        $aInfo = $oDb->getRow( "select oxseourl, oxtype from oxseo where oxobjectid =  " . $oDb->quote( $sObjectId ) . " and oxlang =  " . $oDb->quote( $iLang ) . " and oxshopid = " . $oDb->quote( $iShopId ) . " order by oxparams limit 1" );
        if ('oxarticle' == $aInfo['oxtype']) {
            $sMainCatId = $oDb->getOne( "select oxcatnid from ".getViewName( "oxobject2category" )." where oxobjectid = " . $oDb->quote( $sObjectId ) . " order by oxtime" );
            if ($sMainCatId) {
                $sUrl = $oDb->getOne( "select oxseourl from oxseo where oxobjectid =  " . $oDb->quote( $sObjectId ) . " and oxlang =  " . $oDb->quote( $iLang ) . " and oxshopid = " . $oDb->quote( $iShopId ) . " and oxparams = " . $oDb->quote( $sMainCatId ) . "  order by oxexpired" );
                if ($sUrl) {
                    return $sUrl;
                }
            }
        }

        return $aInfo['oxseourl'];
    }

    /**
     * processSeoCall handles Server information and passes it to decoder
     *
     * @param string $sRequest request
     * @param string $sPath    path
     *
     * @access public
     * @return void
     */
    public function processSeoCall( $sRequest = null, $sPath = null )
    {
        // first - collect needed parameters
        if ( !$sRequest ) {
            if ( isset( $_SERVER['REQUEST_URI'] ) && $_SERVER['REQUEST_URI'] ) {
                $sRequest = $_SERVER['REQUEST_URI'];
            } else {
                // try something else
                $sRequest = $_SERVER['SCRIPT_URI'];
            }
        }

        $sPath = $sPath ? $sPath : str_replace( 'oxseo.php', '', $_SERVER['SCRIPT_NAME'] );
        if ( ( $sParams = $this->_getParams( $sRequest, $sPath ) ) ) {

            // in case SEO url is actual
            if ( is_array( $aGet = $this->decodeUrl( $sParams ) ) ) {
                $_GET = array_merge( $aGet, $_GET );
                oxRegistry::getLang()->resetBaseLanguage();
            } elseif ( ( $sRedirectUrl = $this->_decodeOldUrl( $sParams ) ) ) {
                // in case SEO url was changed - redirecting to new location
                oxRegistry::getUtils()->redirect( $this->getConfig()->getShopURL().$sRedirectUrl, false );
            } elseif ( ( $sRedirectUrl = $this->_decodeSimpleUrl( $sParams ) ) ) {
                // old type II seo urls
                oxRegistry::getUtils()->redirect( $this->getConfig()->getShopURL().$sRedirectUrl, false );
            } else {
                oxRegistry::getSession()->start();
                // unrecognized url
                error_404_handler( $sParams );
            }
        }
    }

    /**
     * Tries to fetch SEO url according to type II seo url data. If no
     * specified data is found NULL will be returned
     *
     * @param string $sParams request params (url chunk)
     *
     * @return string
     */
    protected function _decodeSimpleUrl( $sParams )
    {
        $oStr = getStr();
        $sLastParam = rtrim( $sParams, '/' );
        $sLastParam = $oStr->substr( $sLastParam, ( ( int ) strrpos( $sLastParam, '/' ) ) - ( $oStr->strlen( $sLastParam ) ) );
        $sLastParam = trim( $sParams, '/' );

        // active object id
        $sUrl = null;

        if ( $sLastParam ) {

            $iLanguage  = oxRegistry::getLang()->getBaseLanguage();

            // article ?
            if ( strpos( $sLastParam, '.htm' ) !== false ) {
                $sUrl = $this->_getObjectUrl( $sLastParam, 'oxarticles', $iLanguage, 'oxarticle' );
            } else {

                // category ?
                if ( !( $sUrl = $this->_getObjectUrl( $sLastParam, 'oxcategories', $iLanguage, 'oxcategory' ) ) ) {
                    // maybe manufacturer ?
                    if ( !( $sUrl = $this->_getObjectUrl( $sLastParam, 'oxmanufacturers', $iLanguage, 'oxmanufacturer' ) ) ) {
                        // then maybe vendor ?
                        $sUrl = $this->_getObjectUrl( $sLastParam, 'oxvendor', $iLanguage, 'oxvendor' );
                    }
                }
            }
        }

        return $sUrl;
    }

    /**
     * Searches and returns (if available) current objects seo url
     *
     * @param string $sSeoId    ident (or last chunk of url)
     * @param string $sTable    name of table to look for data
     * @param int    $iLanguage current language identifier
     * @param string $sType     type of object to search in seo table
     *
     * @return string
     */
    protected function _getObjectUrl( $sSeoId, $sTable, $iLanguage, $sType )
    {
        $oDb     = oxDb::getDb();
        $sTable  = getViewName( $sTable, $iLanguage );
        $sSeoUrl = null;

        // first checking of field exists at all
        if ( $oDb->getOne( "show columns from {$sTable} where field = 'oxseoid'" ) ) {
            // if field exists - searching for object id
            if ( $sObjectId = $oDb->getOne( "select oxid from {$sTable} where oxseoid = ".$oDb->quote( $sSeoId ) ) ) {
                $sSeoUrl = $oDb->getOne( "select oxseourl from oxseo where oxtype = " . $oDb->quote( $sType ) . " and oxobjectid = " . $oDb->quote( $sObjectId ) . " and oxlang = " . $oDb->quote( $iLanguage ) . " " );
            }
        }

        return $sSeoUrl;
    }

    /**
     * Extracts SEO paramteters and returns as array
     *
     * @param string $sRequest request
     * @param string $sPath    path
     *
     * @return array $aParams extracted params
     */
    protected function _getParams( $sRequest, $sPath )
    {
        $oStr = getStr();

        $sParams = $oStr->preg_replace( '/\?.*/', '', $sRequest );
        $sPath   = preg_quote($sPath, '/');
        $sParams = $oStr->preg_replace( "/^$sPath/", '', $sParams );

        // this should not happen on most cases, because this redirect is handled by .htaccess
        if ( $sParams && !$oStr->preg_match( '/\.html$/', $sParams ) && !$oStr->preg_match( '/\/$/', $sParams ) ) {
            oxRegistry::getUtils()->redirect( $this->getConfig()->getShopURL() . $sParams . '/', false );
        }

        return $sParams;
    }

}
