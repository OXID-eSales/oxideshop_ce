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
 * URL utility class
 */
class oxUtilsUrl extends oxSuperCfg
{
    /**
     * oxUtilsUrl class instance.
     *
     * @var oxUtilsUrl
     */
    private static $_instance = null;

    /**
     * Additional url parameters which should be appended to seo/std urls
     *
     * @var array
     */
    protected $_aAddUrlParams = null;

    /**
     * Current shop hosts array
     * @var array
     */
    protected $_aHosts = null;

    /**
     * resturns a single instance of this class
     *
     * @deprecated since v5.0 (2012-08-10); Use oxRegistry::get("oxUtilsUrl") instead
     *
     * @return oxUtilsUrl
     */
    public static function getInstance()
    {
        return oxRegistry::get("oxUtilsUrl");
    }

    /**
     * Returns core parameters which must be added to each url
     *
     * @return array
     */
    public function getBaseAddUrlParams()
    {
        $aAddUrlParams = array();

        return $aAddUrlParams;
    }

    /**
     * Returns parameters which should be appended to seo or std url
     *
     * @return array
     */
    public function getAddUrlParams()
    {
        if ( $this->_aAddUrlParams === null ) {
            $this->_aAddUrlParams = $this->getBaseAddUrlParams();

            // appending currency
            if ( ( $iCur = $this->getConfig()->getShopCurrency() ) ) {
                $this->_aAddUrlParams['cur'] = $iCur;
            }
        }
        return $this->_aAddUrlParams;
    }

    /**
     * prepareUrlForNoSession adds extra url params making it usable without session
     * also removes sid=xxxx&
     *
     * @param string $sUrl given url
     *
     * @access public
     * @return string
     */
    public function prepareUrlForNoSession( $sUrl )
    {
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace( '/(&amp;|\?)$/', '', $sUrl );

        if ( oxRegistry::getUtils()->seoIsActive() ) {
            return $sUrl;
        }

        if ($qpos = $oStr->strpos($sUrl, '?')) {
            if ($qpos == $oStr->strlen($sUrl)-1) {
                $sSep = '';
            } else {
                $sSep = '&amp;';
            }
        } else {
            $sSep = '?';
        }

        if ( !$oStr->preg_match('/[&?](amp;)?lang=[0-9]+/i', $sUrl)) {
            $sUrl .= "{$sSep}lang=".oxRegistry::getLang()->getBaseLanguage();
            $sSep = '&amp;';
        }

        $oConfig = $this->getConfig();
        if ( !$oStr->preg_match('/[&?](amp;)?cur=[0-9]+/i', $sUrl)) {
            $iCur = (int) $oConfig->getShopCurrency();
            if ( $iCur ) {
                $sUrl .= "{$sSep}cur=".$iCur;
                $sSep = '&amp;';
            }
        }

        return $sUrl;
    }

    /**
     * Prepares canonical url
     *
     * @param string $sUrl given url
     *
     * @access public
     * @return string
     */
    public function prepareCanonicalUrl( $sUrl )
    {
        $oConfig = $this->getConfig();
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace( '/(&amp;|\?)$/', '', $sUrl );
        $sSep = ( $oStr->strpos( $sUrl, '?' ) === false ) ? '?' : '&amp;';


        if ( !oxRegistry::getUtils()->seoIsActive() ) {
            // non seo url has no language identifier..
            $iLang = oxRegistry::getLang()->getBaseLanguage();
            if ( !$oStr->preg_match( '/[&?](amp;)?lang=[0-9]+/i', $sUrl ) && $iLang != $oConfig->getConfigParam( 'sDefaultLang' ) ) {
                $sUrl .= "{$sSep}lang=".$iLang;
            }
        }

        return $sUrl;
    }

    /**
     * Appends url with given parameters
     *
     * @param string $sUrl       url to append
     * @param array  $aAddParams parameters to append
     *
     * @return string
     */
    public function appendUrl( $sUrl, $aAddParams )
    {
        $oStr = getStr();
        $sSep = '&amp;';
        if ( $oStr->strpos( $sUrl, '?' ) === false ) {
            $sSep = '?';
        }

        if ( count( $aAddParams ) ) {
            foreach ( $aAddParams as $sName => $sValue ) {
                if ( isset( $sValue ) && !$oStr->preg_match("/\?(.*&(amp;)?)?".preg_quote( $sName )."=/", $sUrl ) ) {
                    $sUrl .= $sSep . $sName . "=" . $sValue;
                    $sSep = '&amp;';
                }
            }
        }
        return $sUrl ? $sUrl.$sSep : '';
    }

    /**
     * Removes any or specified dynamic parameter from given url
     *
     * @param string $sUrl    url to clean
     * @param array  $aParams parameters to remove [optional]
     *
     * @return string
     */
    public function cleanUrl( $sUrl, $aParams = null )
    {
        $oStr = getStr();
        if ( is_array( $aParams ) ) {
            foreach ( $aParams as $sParam ) {
                $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?)'.preg_quote( $sParam ).'=[a-z0-9\.]+&?(amp;)?/i', '\1', $sUrl );
            }
        } else {
            $sUrl = $oStr->preg_replace( '/(\?|&(amp;)?).+/i', '\1', $sUrl );
        }

        return trim( $sUrl, "?" );
    }

    /**
     * Performs base url processing - adds required parameters to given url
     *
     * @param string $sUrl       url to process
     * @param bool   $blFinalUrl should url be finalized or should it end with ? or &amp; (default true)
     * @param array  $aParams    additional parameters (default null)
     * @param int    $iLang      url target language (default null)
     *
     * @return string
     */
    public function processUrl( $sUrl, $blFinalUrl = true, $aParams = null, $iLang = null )
    {
        $aAddParams = $this->getAddUrlParams();
        if ( is_array($aParams) && count( $aParams ) ) {
            $aAddParams = array_merge( $aAddParams, $aParams );
        }

        $ret = oxRegistry::getSession()->processUrl(
            oxRegistry::getLang()->processUrl(
                $this->appendUrl(
                    $sUrl,
                    $aAddParams
                ),
                $iLang
            )
        );

        if ($blFinalUrl) {
            $ret = getStr()->preg_replace('/(\?|&(amp;)?)$/', '', $ret);
        }
        return $ret;
    }

    /**
     * Extracts host from given url and appends $aHosts with it
     *
     * @param string $sUrl    url to extract
     * @param array  &$aHosts hosts array
     *
     * @return null
     */
    protected function _addHost( $sUrl, & $aHosts )
    {
        if ( $sUrl && ( $sHost = @parse_url( $sUrl, PHP_URL_HOST ) ) ) {
            if ( !in_array( $sHost, $aHosts ) ) {
                $aHosts[] = $sHost;
            }
        }
    }

    /**
     * Collects and returns current shop hosts array
     *
     * @return array
     */
    protected function _getHosts()
    {
        if ( $this->_aHosts === null ) {
            $this->_aHosts = array();
            $oConfig = $this->getConfig();

            // mall (ssl) url
            $this->_addHost( $oConfig->getConfigParam( "sMallShopURL" ), $this->_aHosts );
            $this->_addHost( $oConfig->getConfigParam( "sMallSSLShopURL" ), $this->_aHosts );

            // language url
            $this->_addHost( $oConfig->getConfigParam( 'aLanguageURLs' ), $this->_aHosts );
            $this->_addHost( $oConfig->getConfigParam( 'aLanguageSSLURLs' ), $this->_aHosts );

            if ( !count( $aHosts ) && !$oConfig->mustAddShopIdToRequest() ) {
                // current url
                $this->_addHost( $oConfig->getConfigParam( "sShopURL" ), $this->_aHosts );
                $this->_addHost( $oConfig->getConfigParam( "sSSLShopURL" ), $this->_aHosts );
            }

            if ( $this->isAdmin() ) {
                $this->_addHost( $oConfig->getConfigParam( "sAdminSSLURL" ), $this->_aHosts );
            }
        }

        return $this->_aHosts;
    }

    /**
     * Compares current URL to supplied string
     *
     * @param string $sUrl URL
     *
     * @return bool true if $sURL is equal to current page URL
     */
    public function isCurrentShopHost( $sUrl )
    {
        $blCurrent = false;
        if ( $sUrl ) {
            // host of given url
            $sUrlHost = @parse_url( $sUrl, PHP_URL_HOST );

            // configured hosts
            $aHosts = $this->_getHosts();

            foreach ( $aHosts as $sHost ) {
                if ( $sHost === $sUrlHost ) {
                    $blCurrent = true;
                    break;
                }
            }
        }

        return $blCurrent;
    }

    /**
     * Seo url processor: adds various needed parameters, like currency, shop id
     *
     * @param string $sUrl url to process
     *
     * @return string
     */
    public function processSeoUrl( $sUrl )
    {

        if ( !$this->isAdmin() ) {
            $sUrl = $this->getSession()->processUrl( $this->appendUrl( $sUrl, $this->getAddUrlParams() ) );
        }

        $sUrl = $this->cleanUrlParams($sUrl);
        return getStr()->preg_replace( '/(\?|&(amp;)?)$/', '', $sUrl );
    }

    /**
     * Remove duplicate GET parameters and clean &amp; and duplicate &
     *
     * @param string $sUrl 	     url to process
     * @param string $sConnector GET elements connector
     *
     * @return string
     */
    public function cleanUrlParams($sUrl, $sConnector = '&amp;')
    {
        $aUrlParts = explode('?', $sUrl);

        // check for params part
        if ( !is_array($aUrlParts) || count($aUrlParts) != 2) {
            return $sUrl;
        }

        $sUrl = $aUrlParts[0];
        $sUrlParams = $aUrlParts[1];

        $oStrUtils = getStr();
        $sUrlParams = $oStrUtils->preg_replace(
            array('@(\&(amp;){1,})@ix', '@\&{1,}@', '@\?&@x'),
            array('&', '&', '?'),
            $sUrlParams
        );

        // remove duplicate entries
        parse_str($sUrlParams, $aUrlParams);
        $sUrl .= '?'.http_build_query($aUrlParams, '', $sConnector);

        // replace brackets
        $sUrl = str_replace(
            array('%5B', '%5D'),
            array('[', ']'),
            $sUrl
        );

        return $sUrl;
    }

    /**
     * append parameter separator - '?' if it is not in the url or &amp; otherwise
     *
     * @param string $sUrl url
     *
     * @return string
     */
    public function appendParamSeparator($sUrl)
    {
        $oStr = getStr();
        if ( $oStr->preg_match('/(\?|&(amp;)?)$/i', $sUrl ) ) {
            // it is already ok
            return $sUrl;
        }
        if ( $oStr->strpos($sUrl, '?') === false ) {
            return $sUrl.'?';
        }
        return $sUrl.'&amp;';
    }

    /**
     * Return current url
     *
     * @return string
     */
    function getCurrentUrl()
    {
        $oUtilsServer = oxRegistry::get( "oxUtilsServer" );

        $aServerParams["HTTPS"]       = $oUtilsServer->getServerVar( "HTTPS" );
        $aServerParams["HTTP_X_FORWARDED_PROTO"] = $oUtilsServer->getServerVar( "HTTP_X_FORWARDED_PROTO" );
        $aServerParams["HTTP_HOST"]   = $oUtilsServer->getServerVar( "HTTP_HOST" );
        $aServerParams["REQUEST_URI"] = $oUtilsServer->getServerVar( "REQUEST_URI" );

        $sProtocol = "http://";

        if ( isset($aServerParams['HTTPS']) && (($aServerParams['HTTPS'] == 'on' || $aServerParams['HTTPS'] == 1))
            || (isset($aServerParams['HTTP_X_FORWARDED_PROTO']) && $aServerParams['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            $sProtocol = 'https://';
        }

        $sUrl = $sProtocol . $aServerParams['HTTP_HOST'] . $aServerParams['REQUEST_URI'];

        return $sUrl;
    }

    /**
     * Forms parameters array out of a string
     * Takes & and &amp; as delimiters
     * Returns associative array with parameters
     *
     * @param $sValue string
     *
     * @return array
     */
    public function stringToParamsArray( $sValue )
    {
        // url building
        // replace possible ampersands, explode, and filter out empty values
        $sValue = str_replace( "&amp;", "&", $sValue );
        $aNavParams = explode( "&", $sValue );
        $aNavParams = array_filter( $aNavParams );
        $aParams = array();
        foreach ( $aNavParams as $sValue ) {
            $exp = explode( "=", $sValue );
            $aParams[$exp[0]] = $exp[1];
        }

        return $aParams;
    }
}
