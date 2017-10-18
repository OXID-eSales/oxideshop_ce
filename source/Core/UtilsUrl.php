<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * URL utility class
 */
class UtilsUrl extends \OxidEsales\Eshop\Core\Base
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
        return [];
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
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace('/(&amp;|\?)$/', '', $sUrl);

        if (\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
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
            $sUrl .= "{$sSep}lang=" . \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
            $sSep = '&amp;';
        }

        $oConfig = $this->getConfig();
        if (!$oStr->preg_match('/[&?](amp;)?cur=[0-9]+/i', $sUrl)) {
            $iCur = (int) $oConfig->getShopCurrency();
            if ($iCur) {
                $sUrl .= "{$sSep}cur=" . $iCur;
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
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = getStr();

        // cleaning up session id..
        $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[a-z0-9\._]+&?(amp;)?/i', '\1', $sUrl);
        $sUrl = $oStr->preg_replace('/(&amp;|\?)$/', '', $sUrl);
        $sSep = ($oStr->strpos($sUrl, '?') === false) ? '?' : '&amp;';

        if (!\OxidEsales\Eshop\Core\Registry::getUtils()->seoIsActive()) {
            // non seo url has no language identifier..
            $iLang = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();
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
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
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
        $sUrl = \OxidEsales\Eshop\Core\Registry::getLang()->processUrl($sUrl, $iLang);
        $sUrl = \OxidEsales\Eshop\Core\Registry::getSession()->processUrl($sUrl);

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
        $shopUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getShopUrl();

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

        /** @var \OxidEsales\Eshop\Core\StrRegular $oStrUtils */
        $oStrUtils = getStr();
        $sUrlParams = $oStrUtils->preg_replace(
            ['@(\&(amp;){1,})@ix', '@\&{1,}@', '@\?&@x'],
            ['&', '&', '?'],
            $sUrlParams
        );

        // remove duplicate entries
        parse_str($sUrlParams, $aUrlParams);
        $sUrl .= '?' . http_build_query($aUrlParams, '', $sConnector);

        // replace brackets
        $sUrl = str_replace(
            ['%5B', '%5D'],
            ['[', ']'],
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
        $oUtilsServer = \OxidEsales\Eshop\Core\Registry::getUtilsServer();

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
        $aParams = [];
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
        return [\OxidEsales\Eshop\Core\Registry::getLang()->getName() => $languageId];
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
        $iLanguageId = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

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
            $this->_aHosts = [];
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
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
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
