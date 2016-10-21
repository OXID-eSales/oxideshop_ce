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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxStrRegular;

/**
 * URL utility class
 */
class UtilsUrl extends \oxSuperCfg
{
    const PARAMETER_SEPARATOR = '&amp;';

    /**
     * Additional url parameters which should be appended to seo/std urls.
     *
     * @var array
     */
    protected $_aAddUrlParams = null;

    /**
     * Current shop hosts array.
     *
     * @var array
     */
    protected $_aHosts = null;

    /**
     * Returns core parameters which must be added to each url.
     *
     * @return array
     */
    public function getBaseAddUrlParams()
    {
        return array();
    }

    /**
     * Returns parameters which should be appended to seo or std url.
     *
     * @return array
     */
    public function getAddUrlParams()
    {
        if ($this->_aAddUrlParams === null) {
            $this->_aAddUrlParams = $this->getBaseAddUrlParams();

            // appending currency
            if (($iCur = $this->getConfig()->getShopCurrency())) {
                $this->_aAddUrlParams['cur'] = $iCur;
            }
        }

        return $this->_aAddUrlParams;
    }

    /**
     * prepareUrlForNoSession adds extra url params making it usable without session
     * also removes sid=xxxx&.
     *
     * @param string $sUrl given url
     *
     * @access public
     * @return string
     */
    public function prepareUrlForNoSession($sUrl)
    {
        /** @var oxStrRegular $oStr */
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace('/(&amp;|\?)$/', '', $sUrl);

        if (oxRegistry::getUtils()->seoIsActive()) {
            return $sUrl;
        }

        if ($qpos = $oStr->strpos($sUrl, '?')) {
            if ($qpos == $oStr->strlen($sUrl) - 1) {
                $sSep = '';
            } else {
                $sSep = '&amp;';
            }
        } else {
            $sSep = '?';
        }

        if (!$oStr->preg_match('/[&?](amp;)?lang=[0-9]+/i', $sUrl)) {
            $sUrl .= "{$sSep}lang=" . oxRegistry::getLang()->getBaseLanguage();
            $sSep = '&amp;';
        }

        $oConfig = $this->getConfig();
        if (!$oStr->preg_match('/[&?](amp;)?cur=[0-9]+/i', $sUrl)) {
            $iCur = (int) $oConfig->getShopCurrency();
            if ($iCur) {
                $sUrl .= "{$sSep}cur=" . $iCur;
                $sSep = '&amp;';
            }
        }

        return $sUrl;
    }

    /**
     * Prepares canonical url.
     *
     * @param string $sUrl given url
     *
     * @access public
     * @return string
     */
    public function prepareCanonicalUrl($sUrl)
    {
        $oConfig = $this->getConfig();
        /** @var oxStrRegular $oStr */
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace('/(&amp;|\?)$/', '', $sUrl);
        $sSep = ($oStr->strpos($sUrl, '?') === false) ? '?' : '&amp;';

        if (!oxRegistry::getUtils()->seoIsActive()) {
            // non seo url has no language identifier..
            $iLang = oxRegistry::getLang()->getBaseLanguage();
            if (!$oStr->preg_match('/[&?](amp;)?lang=[0-9]+/i', $sUrl) &&
                $iLang != $oConfig->getConfigParam('sDefaultLang')
            ) {
                $sUrl .= "{$sSep}lang=" . $iLang;
            }
        }

        return $sUrl;
    }

    /**
     * Appends url with given parameters.
     *
     * @param string $sUrl                    url to append
     * @param array  $parametersToAdd         parameters to append
     * @param bool   $blFinalUrl              final url
     * @param bool   $allowParameterOverwrite Decides if same parameters should overwrite query parameters.
     *
     * @return string
     */
    public function appendUrl($sUrl, $parametersToAdd, $blFinalUrl = false, $allowParameterOverwrite = false)
    {
        $paramSeparator = self::PARAMETER_SEPARATOR;
        $finalParameters = $this->removeNotSetParameters($parametersToAdd);

        if (is_array($finalParameters) && !empty($finalParameters)) {
            $urlWithoutQuery = $sUrl;
            $separatorPlace = strpos($sUrl, '?');
            if ($separatorPlace !== false) {
                $urlWithoutQuery = substr($sUrl, 0, $separatorPlace);
                $urlQueryEscaped = substr($sUrl, $separatorPlace + 1);
                $urlQuery = str_replace($paramSeparator, '&', $urlQueryEscaped);

                $finalParameters = $this->mergeDuplicatedParameters($finalParameters, $urlQuery, $allowParameterOverwrite);
            }

            $sUrl = $this->appendParamSeparator($urlWithoutQuery);
            $sUrl .= http_build_query($finalParameters, null, $paramSeparator);
        }

        if ($sUrl && !$blFinalUrl) {
            $sUrl = $this->appendParamSeparator($sUrl);
        }

        return $sUrl;
    }

    /**
     * Removes any or specified dynamic parameter from given url.
     *
     * @param string $sUrl    url to clean.
     * @param array  $aParams parameters to remove [optional].
     *
     * @return string
     */
    public function cleanUrl($sUrl, $aParams = null)
    {
        /** @var oxStrRegular $oStr */
        $oStr = getStr();
        if (is_array($aParams)) {
            foreach ($aParams as $sParam) {
                $sUrl = $oStr->preg_replace(
                    '/(\?|&(amp;)?)' . preg_quote($sParam) . '=[a-z0-9\.]+&?(amp;)?/i',
                    '\1',
                    $sUrl
                );
            }
        } else {
            $sUrl = $oStr->preg_replace('/(\?|&(amp;)?).+/i', '\1', $sUrl);
        }

        return trim($sUrl, "?");
    }


    /**
     * Adds shop host if url does not start with it.
     *
     * @param string $sUrl
     *
     * @return string
     */
    public function addShopHost($sUrl)
    {
        if (!preg_match("#^https?://#i", $sUrl)) {
            $sShopUrl = $this->getConfig()->getSslShopUrl();
            $sUrl = $sShopUrl . $sUrl;
        }

        return $sUrl;
    }

    /**
     * Performs base url processing - adds required parameters to given url.
     *
     * @param string $sUrl       url to process.
     * @param bool   $blFinalUrl should url be finalized or should it end with ? or &amp; (default true).
     * @param array  $aParams    additional parameters (default null).
     * @param int    $iLang      url target language (default null).
     *
     * @return string
     */
    public function processUrl($sUrl, $blFinalUrl = true, $aParams = null, $iLang = null)
    {
        $sUrl = $this->appendUrl($sUrl, $aParams, $blFinalUrl);

        if ($this->isCurrentShopHost($sUrl)) {
            $sUrl = $this->processShopUrl($sUrl, $blFinalUrl, $iLang);
        }

        return $sUrl;
    }

    /**
     * Adds additional shop url parameters, session id, language id when needed.
     *
     * @param string $sUrl       url to process.
     * @param bool   $blFinalUrl should url be finalized or should it end with ? or &amp;.
     * @param int    $iLang      url target language.
     *
     * @return string
     */
    public function processShopUrl($sUrl, $blFinalUrl = true, $iLang = null)
    {
        $aAddParams = $this->getAddUrlParams();

        $sUrl = $this->appendUrl($sUrl, $aAddParams, $blFinalUrl);
        $sUrl = oxRegistry::getLang()->processUrl($sUrl, $iLang);
        $sUrl = oxRegistry::getSession()->processUrl($sUrl);

        if ($blFinalUrl) {
            $sUrl = $this->rightTrimAmp($sUrl);
        }

        return $sUrl;
    }

    /**
     * Method returns active shop host.
     *
     * @return string
     */
    public function getActiveShopHost()
    {
        $shopUrl = $this->getConfig()->getShopUrl();

        return $this->extractHost($shopUrl);
    }

    /**
     * Extract host from url.
     *
     * @param string $url
     *
     * @return string
     */
    public function extractHost($url)
    {
        return $this->parseUrlAndAppendSchema($url, PHP_URL_HOST) ?: $url;
    }

    /**
     * Method returns shop URL part - path.
     *
     * @return null|string
     */
    public function getActiveShopUrlPath()
    {
        $shopUrl = oxRegistry::getConfig()->getShopUrl();

        return $this->extractUrlPath($shopUrl);
    }

    /**
     * Method returns URL part - path.
     *
     * @param string $shopUrl
     *
     * @return string|null
     */
    public function extractUrlPath($shopUrl)
    {
        return $this->parseUrlAndAppendSchema($shopUrl, PHP_URL_PATH);
    }

    /**
     * Compares current URL to supplied string.
     *
     * @param string $sUrl
     *
     * @return bool true if $sUrl is equal to current page URL.
     */
    public function isCurrentShopHost($sUrl)
    {
        $blCurrent = false;
        $sUrlHost = @parse_url($sUrl, PHP_URL_HOST);
        // checks if it is relative url.
        if (is_null($sUrlHost)) {
            $blCurrent = true;
        } else {
            $aHosts = $this->_getHosts();

            foreach ($aHosts as $sHost) {
                if ($sHost === $sUrlHost) {
                    $blCurrent = true;
                    break;
                }
            }
        }

        return $blCurrent;
    }

    /**
     * Improved url parsing with parse_url as base and scheme checking improvement in url preprocessing
     *
     * @param string $url
     * @param string $flag
     * @param string $appendScheme Append this scheme to url if no scheme found
     *
     * @return string
     */
    private function parseUrlAndAppendSchema($url, $flag, $appendScheme = 'http')
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $url = $appendScheme . '://' . $url;
        }

        return parse_url($url, $flag);
    }

    /**
     * Seo url processor: adds various needed parameters, like currency, shop id.
     *
     * @param string $sUrl url to process.
     *
     * @return string
     */
    public function processSeoUrl($sUrl)
    {
        if (!$this->isAdmin()) {
            $sUrl = $this->getSession()->processUrl($this->appendUrl($sUrl, $this->getAddUrlParams()));
        }

        $sUrl = $this->cleanUrlParams($sUrl);

        return $this->rightTrimAmp($sUrl);
    }

    /**
     * Remove duplicate GET parameters and clean &amp; and duplicate &.
     *
     * @param string $sUrl       url to process.
     * @param string $sConnector GET elements connector.
     *
     * @return string
     */
    public function cleanUrlParams($sUrl, $sConnector = '&amp;')
    {
        $aUrlParts = explode('?', $sUrl);

        // check for params part
        if (!is_array($aUrlParts) || count($aUrlParts) != 2) {
            return $sUrl;
        }

        $sUrl = $aUrlParts[0];
        $sUrlParams = $aUrlParts[1];

        /** @var oxStrRegular $oStrUtils */
        $oStrUtils = getStr();
        $sUrlParams = $oStrUtils->preg_replace(
            array('@(\&(amp;){1,})@ix', '@\&{1,}@', '@\?&@x'),
            array('&', '&', '?'),
            $sUrlParams
        );

        // remove duplicate entries
        parse_str($sUrlParams, $aUrlParams);
        $sUrl .= '?' . http_build_query($aUrlParams, '', $sConnector);

        // replace brackets
        $sUrl = str_replace(
            array('%5B', '%5D'),
            array('[', ']'),
            $sUrl
        );

        return $sUrl;
    }

    /**
     * Appends parameter separator - '?' if it is not in the url or &amp; otherwise.
     *
     * @param string $sUrl url
     *
     * @return string
     */
    public function appendParamSeparator($sUrl)
    {
        return $sUrl . $this->getUrlParametersSeparator($sUrl);
    }

    /**
     * Return current url.
     *
     * @return string
     */
    public function getCurrentUrl()
    {
        $oUtilsServer = oxRegistry::get("oxUtilsServer");

        $aServerParams["HTTPS"] = $oUtilsServer->getServerVar("HTTPS");
        $aServerParams["HTTP_X_FORWARDED_PROTO"] = $oUtilsServer->getServerVar("HTTP_X_FORWARDED_PROTO");
        $aServerParams["HTTP_HOST"] = $oUtilsServer->getServerVar("HTTP_HOST");
        $aServerParams["REQUEST_URI"] = $oUtilsServer->getServerVar("REQUEST_URI");

        $sProtocol = "http://";

        if (isset($aServerParams['HTTPS']) && (($aServerParams['HTTPS'] == 'on' || $aServerParams['HTTPS'] == 1))
            || (isset($aServerParams['HTTP_X_FORWARDED_PROTO']) && $aServerParams['HTTP_X_FORWARDED_PROTO'] == 'https')
        ) {
            $sProtocol = 'https://';
        }

        return $sProtocol . $aServerParams['HTTP_HOST'] . $aServerParams['REQUEST_URI'];
    }

    /**
     * Forms parameters array out of a string.
     * Takes & and &amp; as delimiters.
     * Returns associative array with parameters.
     *
     * @param string $sValue String
     *
     * @return array
     */
    public function stringToParamsArray($sValue)
    {
        // url building
        // replace possible ampersands, explode, and filter out empty values
        $sValue = str_replace("&amp;", "&", $sValue);
        $aNavParams = explode("&", $sValue);
        $aNavParams = array_filter($aNavParams);
        $aParams = array();
        foreach ($aNavParams as $sValue) {
            $exp = explode("=", $sValue);
            $aParams[$exp[0]] = $exp[1];
        }

        return $aParams;
    }

    /**
     * Return array of language key and language value.
     *
     * @param integer $languageId
     *
     * @return array
     */
    public function getUrlLanguageParameter($languageId)
    {
        return [oxRegistry::getLang()->getName() => $languageId];
    }

    /**
     * Extracts host from given url and appends $aHosts with it
     *
     * @param string $sUrl   url to extract
     * @param array  $aHosts hosts array
     */
    protected function _addHost($sUrl, &$aHosts)
    {
        if ($sUrl && ($sHost = @parse_url($sUrl, PHP_URL_HOST))) {
            if (!in_array($sHost, $aHosts)) {
                $aHosts[] = $sHost;
            }
        }
    }

    /**
     * Appends language urls to $aHosts.
     *
     * @param array $aLanguageUrls array of language urls to extract
     * @param array $aHosts        hosts array
     */
    protected function _addLanguageHost($aLanguageUrls, &$aHosts)
    {
        $iLanguageId = oxRegistry::getLang()->getBaseLanguage();

        if (isset($aLanguageUrls[$iLanguageId])) {
            $this->_addHost($aLanguageUrls[$iLanguageId], $aHosts);
        }
    }

    /**
     * Collects and returns current shop hosts array.
     *
     * @return array
     */
    protected function _getHosts()
    {
        if ($this->_aHosts === null) {
            $this->_aHosts = array();
            $oConfig = $this->getConfig();

            $this->_addMallHosts($this->_aHosts);

            // language url
            $this->_addLanguageHost($oConfig->getConfigParam('aLanguageURLs'), $this->_aHosts);
            $this->_addLanguageHost($oConfig->getConfigParam('aLanguageSSLURLs'), $this->_aHosts);

            // current url
            $this->_addHost($oConfig->getConfigParam("sShopURL"), $this->_aHosts);
            $this->_addHost($oConfig->getConfigParam("sSSLShopURL"), $this->_aHosts);

            if ($this->isAdmin()) {
                $this->_addHost($oConfig->getConfigParam("sAdminSSLURL"), $this->_aHosts);
            }
        }

        return $this->_aHosts;
    }

    /**
     * Appends shop mall urls to $aHosts if needed
     *
     * @param array $aHosts hosts array
     */
    protected function _addMallHosts(&$aHosts)
    {
    }

    /**
     * Returns url separator (?,&amp;) for adding new parameters.
     *
     * @param string $url
     *
     * @return string
     */
    private function getUrlParametersSeparator($url)
    {
        /** @var oxStrRegular $oStr */
        $oStr = getStr();

        $urlSeparator = '&amp;';
        if ($oStr->preg_match('/(\?|&(amp;)?)$/i', $url)) {
            $urlSeparator = '';
        } elseif ($oStr->strpos($url, '?') === false) {
            $urlSeparator = '?';
        }

        return $urlSeparator;
    }

    /**
     * Removes parameters which are not set.
     *
     * @param string $parametersToAdd
     *
     * @return string
     */
    private function removeNotSetParameters($parametersToAdd)
    {
        if (is_array($parametersToAdd) && !empty($parametersToAdd)) {
            foreach ($parametersToAdd as $key => $value) {
                if (is_null($value)) {
                    unset($parametersToAdd[$key]);
                }
            }
        }

        return $parametersToAdd;
    }

    /**
     * @param array  $aAddParams              parameters to add to URL
     * @param string $query                   URL query part
     * @param bool   $allowParameterOverwrite Decides if same parameters should overwrite query parameters
     *
     * @return array
     */
    private function mergeDuplicatedParameters($aAddParams, $query, $allowParameterOverwrite = true)
    {
        parse_str($query, $currentUrlParameters);
        if ($allowParameterOverwrite) {
            $newParameters = array_merge($currentUrlParameters, $aAddParams);
        } else {
            $newFilteredParameters = array_diff_key($aAddParams, $currentUrlParameters);
            $newParameters = array_merge($currentUrlParameters, $newFilteredParameters);
        }

        return $newParameters;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function rightTrimAmp($url)
    {
        return getStr()->preg_replace('/(\?|&(amp;)?)$/i', '', $url);
    }
}
