<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * Seo encoder base
 */
class SeoDecoder extends \OxidEsales\Eshop\Core\Base
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
        $aRet = [];
        $sUrl = $oStr->html_entity_decode($sUrl);

        if (($iPos = strpos($sUrl, '?')) !== false) {
            parse_str($oStr->substr($sUrl, $iPos + 1), $aRet);
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
    protected function _getIdent($sSeoUrl, $blIgnore = false)
    {
        return md5(strtolower($sSeoUrl));
    }

    /**
     * decodeUrl decodes given url into oxid eShop required parameters which are returned as array
     *
     * @param string $seoUrl SEO url
     *
     * @access        public
     * @return array || false
     */
    public function decodeUrl($seoUrl)
    {
        $stringObject = getStr();
        $baseUrl = $this->getConfig()->getShopURL();
        if ($stringObject->strpos($seoUrl, $baseUrl) === 0) {
            $seoUrl = $stringObject->substr($seoUrl, $stringObject->strlen($baseUrl));
        }
        $seoUrl = rawurldecode($seoUrl);

        //extract page number from seo url
        list($seoUrl, $pageNumber) = $this->extractPageNumberFromSeoUrl($seoUrl);
        $shopId = $this->getConfig()->getShopId();

        $key = $this->_getIdent($seoUrl);
        $urlParameters = false;

        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $resultSet = $database->select("select oxstdurl, oxlang from oxseo where oxident = :oxident and oxshopid = :oxshopid limit 1", [
            ':oxident' => $key,
            ':oxshopid' => $shopId
        ]);
        if (!$resultSet->EOF) {
            // primary seo language changed ?
            $urlParameters = $this->parseStdUrl($resultSet->fields['oxstdurl']);
            $urlParameters['lang'] = $resultSet->fields['oxlang'];
        }
        if (is_array($urlParameters) && !is_null($pageNumber) && (1 < $pageNumber)) {
            $urlParameters['pgNr'] = $pageNumber;
        }

        return $urlParameters;
    }

    /**
     * Checks if url is stored in history table and if it was found - tries
     * to fetch new url from seo table
     *
     * @param string $seoUrl SEO url
     *
     * @access         public
     * @return string || false
     */
    protected function _decodeOldUrl($seoUrl)
    {
        $stringObject = getStr();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $baseUrl = $this->getConfig()->getShopURL();
        if ($stringObject->strpos($seoUrl, $baseUrl) === 0) {
            $seoUrl = $stringObject->substr($seoUrl, $stringObject->strlen($baseUrl));
        }
        $shopId = $this->getConfig()->getShopId();
        $seoUrl = rawurldecode($seoUrl);

        //extract page number from seo url
        list($seoUrl, $pageNumber) = $this->extractPageNumberFromSeoUrl($seoUrl);

        $key = $this->_getIdent($seoUrl, true);

        $url = false;
        $resultSet = $database->select("select oxobjectid, oxlang from oxseohistory where oxident = :oxident and oxshopid = :oxshopid limit 1", [
            ':oxident' => $key,
            ':oxshopid' => $shopId
        ]);
        if (!$resultSet->EOF) {
            // updating hit info (oxtimestamp field will be updated automatically)
            $database->execute(
                "update oxseohistory set oxhits = oxhits + 1 where oxident = :oxident and oxshopid = :oxshopid limit 1",
                [
                    ':oxident' => $key,
                    ':oxshopid' => $shopId
                ]
            );

            // fetching new url
            $url = $this->_getSeoUrl($resultSet->fields['oxobjectid'], $resultSet->fields['oxlang'], $shopId);

            // appending with $_SERVER["QUERY_STRING"]
            $url = $this->_addQueryString($url);
        }
        if ($url && !is_null($pageNumber)) {
            $url = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendUrl($url, ['pgNr' => $pageNumber]);
        }

        return $url;
    }

    /**
     * Appends and returns given url with $_SERVER["QUERY_STRING"] value
     *
     * @param string $sUrl url to append
     *
     * @return string
     */
    protected function _addQueryString($sUrl)
    {
        if (($sQ = $_SERVER["QUERY_STRING"])) {
            $sUrl = rtrim($sUrl, "&?");
            $sQ = ltrim($sQ, "&?");

            $sUrl .= (strpos($sUrl, '?') === false) ? "?" : "&";
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
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $aInfo = $oDb->getRow("select oxseourl, oxtype from oxseo where oxobjectid = :oxobjectid and oxlang = :oxlang and oxshopid = :oxshopid order by oxparams limit 1", [
            ':oxobjectid' => $sObjectId,
            ':oxlang' => $iLang,
            ':oxshopid' => $iShopId,
        ]);

        if ('oxarticle' == $aInfo['oxtype']) {
            $sMainCatId = $oDb->getOne("select oxcatnid from " . getViewName("oxobject2category") . " where oxobjectid = :oxobjectid order by oxtime", [
                ':oxobjectid' => $sObjectId
            ]);
            if ($sMainCatId) {
                $sUrl = $oDb->getOne("select oxseourl from oxseo where oxobjectid = :oxobjectid and oxlang = :oxlang and oxshopid = :oxshopid  and oxparams = :oxparams order by oxexpired", [
                    ':oxobjectid' => $sObjectId,
                    ':oxlang' => $iLang,
                    ':oxshopid' => $iShopId,
                    ':oxparams' => $sMainCatId,
                ]);
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
     */
    public function processSeoCall($sRequest = null, $sPath = null)
    {
        // first - collect needed parameters
        if (!$sRequest) {
            if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']) {
                $sRequest = $_SERVER['REQUEST_URI'];
            } else {
                // try something else
                $sRequest = $_SERVER['SCRIPT_URI'];
            }
        }

        $sPath = $sPath ? $sPath : str_replace('oxseo.php', '', $_SERVER['SCRIPT_NAME']);
        if (($sParams = $this->_getParams($sRequest, $sPath))) {
            // in case SEO url is actual
            if (is_array($aGet = $this->decodeUrl($sParams))) {
                $_GET = array_merge($aGet, $_GET);
                \OxidEsales\Eshop\Core\Registry::getLang()->resetBaseLanguage();
            } elseif (($sRedirectUrl = $this->_decodeOldUrl($sParams))) {
                // in case SEO url was changed - redirecting to new location
                \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($this->getConfig()->getShopURL() . $sRedirectUrl, false, 301);
            } elseif (($sRedirectUrl = $this->_decodeSimpleUrl($sParams))) {
                // old type II seo urls
                \OxidEsales\Eshop\Core\Registry::getUtils()->redirect($this->getConfig()->getShopURL() . $sRedirectUrl, false, 301);
            } else {
                \OxidEsales\Eshop\Core\Registry::getSession()->start();
                // unrecognized url
                error_404_handler($sParams);
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
    protected function _decodeSimpleUrl($sParams)
    {
        $sLastParam = trim($sParams, '/');

        // active object id
        $sUrl = null;

        if ($sLastParam) {
            $iLanguage = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

            // article ?
            if (strpos($sLastParam, '.htm') !== false) {
                $sUrl = $this->_getObjectUrl($sLastParam, 'oxarticles', $iLanguage, 'oxarticle');
            } else {
                // category ?
                if (!($sUrl = $this->_getObjectUrl($sLastParam, 'oxcategories', $iLanguage, 'oxcategory'))) {
                    // maybe manufacturer ?
                    if (!($sUrl = $this->_getObjectUrl($sLastParam, 'oxmanufacturers', $iLanguage, 'oxmanufacturer'))) {
                        // then maybe vendor ?
                        $sUrl = $this->_getObjectUrl($sLastParam, 'oxvendor', $iLanguage, 'oxvendor');
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
    protected function _getObjectUrl($sSeoId, $sTable, $iLanguage, $sType)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sTable = getViewName($sTable, $iLanguage);

        // first checking of field exists at all
        if ($oDb->getOne("show columns from {$sTable} where field = 'oxseoid'")) {
            // if field exists - searching for object id
            if ($sObjectId = $oDb->getOne("select oxid from {$sTable} where oxseoid = :oxseoid", [
                ':oxseoid' => $sSeoId
            ])) {
                return $oDb->getOne("select oxseourl from oxseo where oxtype = :oxtype and oxobjectid = :oxobjectid and oxlang = :oxlang", [
                    ':oxtype' => $sType,
                    ':oxobjectid' => $sObjectId,
                    ':oxlang' => $iLanguage,
                ]);
            }
        }
    }

    /**
     * Extracts SEO paramteters and returns as array
     *
     * @param string $sRequest request
     * @param string $sPath    path
     *
     * @return array $aParams extracted params
     */
    protected function _getParams($sRequest, $sPath)
    {
        $oStr = getStr();

        $sParams = $oStr->preg_replace('/\?.*/', '', $sRequest);
        $sPath = preg_quote($sPath, '/');
        $sParams = $oStr->preg_replace("/^$sPath/", '', $sParams);

        // this should not happen on most cases, because this redirect is handled by .htaccess
        if ($sParams && !$oStr->preg_match('/\.html$/', $sParams) && !$oStr->preg_match('/\/$/', $sParams)) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->redirect(\OxidEsales\Eshop\Core\Registry::getConfig()->getShopURL() . $sParams . '/', false, 301);
        }

        return $sParams;
    }

    /**
     * Splits seo url into:
     *     - seo url without page number
     *     - page number
     *
     * @param string $seoUrl
     *
     * @return array
     */
    private function extractPageNumberFromSeoUrl($seoUrl)
    {
        $pageNumber = null;
        if (1 === preg_match('/(.*?)\/(\d+)\/(.*)/', $seoUrl, $matches)) {
            $seoUrl = $matches[1] . '/' . $matches[3];
            $pageNumber = $this->convertSeoPageStringToActualPageNumber($matches[2]);
        }
        return [$seoUrl, $pageNumber];
    }

    /**
     * Converts seo url pagination number to actual page number.
     *
     * @param int $seoPageNumber
     *
     * @return int
     */
    private function convertSeoPageStringToActualPageNumber($seoPageNumber)
    {
        if (!is_null($seoPageNumber)) {
            $seoPageNumber = max(0, (int) $seoPageNumber - 1);
        }
        return $seoPageNumber;
    }
}
