<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use stdClass;
use OxidEsales\Eshop\Core\Registry;

/**
 * Language related utility class
 */
class Language extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Language parameter name
     *
     * @var string
     */
    protected $_sName = 'lang';

    /**
     * Current shop base language Id
     *
     * @var int
     */
    protected $_iBaseLanguageId = null;

    /**
     * Templates language Id
     *
     * @var int
     */
    protected $_iTplLanguageId = null;

    /**
     * Editing object language Id
     *
     * @var int
     */
    protected $_iEditLanguageId = null;

    /**
     * Language translations array cache
     *
     * @var array
     */
    protected $_aLangCache = [];

    /**
     * Array containing possible admin template translations
     *
     * @var array
     */
    protected $_aAdminTplLanguageArray = null;

    /**
     * Language abbreviation array
     *
     * @var array
     */
    protected $_aLangAbbr = null;

    /**
     * registered additional language filesets to load
     *
     * @var array
     */
    protected $_aAdditionalLangFiles = [];

    /**
     * registered additional language filesets to load
     *
     * @var array
     */
    protected $_aLangMap = [];

    /**
     * Active module Ids and paths array
     *
     * @var array
     */
    protected $_aActiveModuleInfo = null;

    /**
     * Disabled module Ids and paths array
     *
     * @var array
     */
    protected $_aDisabledModuleInfo = null;

    /**
     * State is string translated or not
     *
     * @var bool
     */
    protected $_blIsTranslated = true;

    /**
     * Template language id.
     *
     * @var int
     */
    protected $_iObjectTplLanguageId = null;

    /**
     * The module translation path finder.
     *
     * @var \OxidEsales\Eshop\Core\Module\ModuleTranslationPathFinder
     */
    protected $moduleTranslationPathFinder = null;

    /**
     * Set translation state
     *
     * @param bool $blIsTranslated State is string translated or not. Default true.
     */
    public function setIsTranslated($blIsTranslated = true)
    {
        $this->_blIsTranslated = $blIsTranslated;
    }

    /**
     * Set translation state
     *
     * @return bool
     */
    public function isTranslated()
    {
        return $this->_blIsTranslated;
    }

    /**
     * resetBaseLanguage resets base language id cache
     *
     * @access public
     */
    public function resetBaseLanguage()
    {
        $this->_iBaseLanguageId = null;
    }

    /**
     * Returns active shop language id
     *
     * @return string
     */
    public function getBaseLanguage()
    {
        if ($this->_iBaseLanguageId === null) {
            $myConfig = $this->getConfig();
            $blAdmin = $this->isAdmin();

            // languages and search engines
            if ($blAdmin && (($iSeLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('changelang')) !== null)) {
                $this->_iBaseLanguageId = $iSeLang;
            }

            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('lang');
            }

            //or determining by domain
            $aLanguageUrls = $myConfig->getConfigParam('aLanguageURLs');

            if (!$blAdmin && is_array($aLanguageUrls)) {
                foreach ($aLanguageUrls as $iId => $sUrl) {
                    if ($sUrl && $myConfig->isCurrentUrl($sUrl)) {
                        $this->_iBaseLanguageId = $iId;
                        break;
                    }
                }
            }

            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('language');
                if (!isset($this->_iBaseLanguageId)) {
                    $this->_iBaseLanguageId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('language');
                }
            }

            // if language still not set and not search engine browsing,
            // getting language from browser
            if (is_null($this->_iBaseLanguageId) && !$blAdmin && !\OxidEsales\Eshop\Core\Registry::getUtils()->isSearchEngine()) {
                // getting from cookie
                $this->_iBaseLanguageId = \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('language');

                // getting from browser
                if (is_null($this->_iBaseLanguageId)) {
                    $this->_iBaseLanguageId = $this->detectLanguageByBrowser();
                }
            }

            if (is_null($this->_iBaseLanguageId)) {
                $this->_iBaseLanguageId = $myConfig->getConfigParam('sDefaultLang');
            }

            $this->_iBaseLanguageId = (int) $this->_iBaseLanguageId;

            // validating language
            $this->_iBaseLanguageId = $this->validateLanguage($this->_iBaseLanguageId);

            \OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie('language', $this->_iBaseLanguageId);
        }

        return $this->_iBaseLanguageId;
    }

    /**
     * Returns language id used to load objects according to current template language
     *
     * @return int
     */
    public function getObjectTplLanguage()
    {
        if ($this->_iObjectTplLanguageId === null) {
            $this->_iObjectTplLanguageId = $this->getTplLanguage();

            if ($this->isAdmin()) {
                $aLanguages = $this->getAdminTplLanguageArray();
                if (
                    !isset($aLanguages[$this->_iObjectTplLanguageId]) ||
                    $aLanguages[$this->_iObjectTplLanguageId]->active == 0
                ) {
                    $this->_iObjectTplLanguageId = key($aLanguages);
                }
            }
        }

        return $this->_iObjectTplLanguageId;
    }

    /**
     * Returns active shop templates language id
     * If it is not an admin area, template language id is same
     * as base shop language id
     *
     * @return string
     */
    public function getTplLanguage()
    {
        if ($this->_iTplLanguageId === null) {
            $iSessLang = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('tpllanguage');
            $this->_iTplLanguageId = $this->isAdmin() ? $this->setTplLanguage($iSessLang) : $this->getBaseLanguage();
        }

        return $this->_iTplLanguageId;
    }

    /**
     * Returns editing object working language id
     *
     * @return string
     */
    public function getEditLanguage()
    {
        if ($this->_iEditLanguageId === null) {
            if (!$this->isAdmin()) {
                $this->_iEditLanguageId = $this->getBaseLanguage();
            } else {
                $iLang = null;
                // choosing language ident
                // check if we really need to set the new language
                if ("saveinnlang" == $this->getConfig()->getActiveView()->getFncName()) {
                    $iLang = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("new_lang");
                }
                $iLang = ($iLang === null) ? \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editlanguage') : $iLang;
                $iLang = ($iLang === null) ? \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('editlanguage') : $iLang;
                $iLang = ($iLang === null) ? $this->getBaseLanguage() : $iLang;

                // validating language
                $this->_iEditLanguageId = $this->validateLanguage($iLang);

                // writing to session
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('editlanguage', $this->_iEditLanguageId);
            }
        }

        return $this->_iEditLanguageId;
    }

    /**
     * Returns array of available languages.
     *
     * @param integer $iLanguage    Number if current language (default null)
     * @param bool    $blOnlyActive load only current language or all
     * @param bool    $blSort       enable sorting or not
     *
     * @return array
     */
    public function getLanguageArray($iLanguage = null, $blOnlyActive = false, $blSort = false)
    {
        $myConfig = $this->getConfig();

        if (is_null($iLanguage)) {
            $iLanguage = $this->_iBaseLanguageId;
        }

        $aLanguages = [];
        $aConfLanguages = $myConfig->getConfigParam('aLanguages');
        $aLangParams = $myConfig->getConfigParam('aLanguageParams');

        if (is_array($aConfLanguages)) {
            $i = 0;
            reset($aConfLanguages);
            foreach ($aConfLanguages as $key => $val) {
                if ($blOnlyActive && is_array($aLangParams)) {
                    //skipping non active languages
                    if (!$aLangParams[$key]['active']) {
                        $i++;
                        continue;
                    }
                }

                if ($val) {
                    $oLang = new stdClass();
                    $oLang->id = isset($aLangParams[$key]['baseId']) ? $aLangParams[$key]['baseId'] : $i;
                    $oLang->oxid = $key;
                    $oLang->abbr = $key;
                    $oLang->name = $val;

                    if (is_array($aLangParams)) {
                        $oLang->active = $aLangParams[$key]['active'];
                        $oLang->sort = $aLangParams[$key]['sort'];
                    }

                    $oLang->selected = (isset($iLanguage) && $oLang->id == $iLanguage) ? 1 : 0;
                    $aLanguages[$oLang->id] = $oLang;
                }
                ++$i;
            }
        }

        if ($blSort && is_array($aLangParams)) {
            uasort($aLanguages, [$this, '_sortLanguagesCallback']);
        }

        return $aLanguages;
    }

    /**
     * Returns languages array containing possible admin template translations
     *
     * @return array
     */
    public function getAdminTplLanguageArray()
    {
        if ($this->_aAdminTplLanguageArray === null) {
            $config = $this->getConfig();

            $langArray = $this->getLanguageArray();
            $this->_aAdminTplLanguageArray = [];

            $adminThemeName = $this->getContainer()
                ->get(AdminThemeBridgeInterface::class)
                ->getActiveTheme();
            $sourceDirectory =
                $config->getAppDir() .
                'views' . DIRECTORY_SEPARATOR .
                $adminThemeName . DIRECTORY_SEPARATOR;

            foreach ($langArray as $langKey => $language) {
                $filePath = $sourceDirectory . $language->abbr . DIRECTORY_SEPARATOR . 'lang.php';
                if (file_exists($filePath) && is_readable($filePath)) {
                    $this->_aAdminTplLanguageArray[$langKey] = $language;
                }
            }
        }

        // moving pointer to beginning
        reset($this->_aAdminTplLanguageArray);

        return $this->_aAdminTplLanguageArray;
    }

    /**
     * Returns selected language abbreviation
     *
     * @param int $iLanguage language id [optional]
     *
     * @return string
     */
    public function getLanguageAbbr($iLanguage = null)
    {
        if ($this->_aLangAbbr === null) {
            $this->_aLangAbbr = $this->getLanguageIds();
        }

        $iLanguage = isset($iLanguage) ? (int) $iLanguage : $this->getBaseLanguage();
        if (isset($this->_aLangAbbr[$iLanguage])) {
            $iLanguage = $this->_aLangAbbr[$iLanguage];
        }

        return $iLanguage;
    }

    /**
     * getLanguageNames returns array of language names e.g. array('Deutch', 'English')
     *
     * @access public
     * @return array
     */
    public function getLanguageNames()
    {
        $aConfLanguages = $this->getConfig()->getConfigParam('aLanguages');
        $aLangIds = $this->getLanguageIds();
        $aLanguages = [];
        foreach ($aLangIds as $iId => $sValue) {
            $aLanguages[$iId] = $aConfLanguages[$sValue];
        }

        return $aLanguages;
    }

    /**
     * Searches for translation string in file and on success returns translation,
     * otherwise returns initial string.
     *
     * @param string $sStringToTranslate Initial string
     * @param int    $iLang              optional language number
     * @param bool   $blAdminMode        on special case you can force mode, to load language constant from admin/shops language file
     *
     * @return string
     */
    public function translateString($sStringToTranslate, $iLang = null, $blAdminMode = null)
    {
        $this->setIsTranslated();
        // checking if in cache exist
        $aLang = $this->_getLangTranslationArray($iLang, $blAdminMode);
        if (isset($aLang[$sStringToTranslate])) {
            return $aLang[$sStringToTranslate];
        }

        // checking if in map exist
        $aMap = $this->_getLanguageMap($iLang, $blAdminMode);
        if (isset($aMap[$sStringToTranslate], $aLang[$aMap[$sStringToTranslate]])) {
            return $aLang[$aMap[$sStringToTranslate]];
        }

        // checking if in theme options exist
        if (count($this->_aAdditionalLangFiles)) {
            $aLang = $this->_getLangTranslationArray($iLang, $blAdminMode, $this->_aAdditionalLangFiles);
            if (isset($aLang[$sStringToTranslate])) {
                return $aLang[$sStringToTranslate];
            }
        }

        $this->setIsTranslated(false);

        if (!$this->isTranslated()) {
            Registry::getLogger()->warning(
                "translation for $sStringToTranslate not found",
                compact('iLang', 'blAdminMode')
            );
        }

        return $sStringToTranslate;
    }

    /**
     * Iterates through given array ($aData) and collects data if array key is similar as
     * searchable key ($sKey*). If you pass $aCollection, it will be appended with found items
     *
     * @param array  $aData       array to search in
     * @param string $sKey        key to look for (looking for similar with strpos)
     * @param array  $aCollection array to append found items [optional]
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "collectSimilar" in next major
     */
    protected function _collectSimilar($aData, $sKey, $aCollection = []) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        foreach ($aData as $sValKey => $sValue) {
            if (strpos($sValKey, $sKey) === 0) {
                $aCollection[$sValKey] = $sValue;
            }
        }

        return $aCollection;
    }

    /**
     * Returns array( "MY_TRANSLATION_KEY" => "MY_TRANSLATION_VALUE", ... ) by
     * given filter "MY_TRANSLATION_" from language files
     *
     * @param string $sKey    key to look
     * @param int    $iLang   language files to search [optional]
     * @param bool   $blAdmin admin/non admin mode [optional]
     *
     * @return array
     */
    public function getSimilarByKey($sKey, $iLang = null, $blAdmin = null)
    {
        startProfile("getSimilarByKey");

        $iLang = isset($iLang) ? $iLang : $this->getTplLanguage();
        $blAdmin = isset($blAdmin) ? $blAdmin : $this->isAdmin();

        // checking if exists in cache
        $aLang = $this->_getLangTranslationArray($iLang, $blAdmin);
        $aSimilarConst = $this->_collectSimilar($aLang, $sKey);

        // checking if in map exist
        $aMap = $this->_getLanguageMap($iLang, $blAdmin);
        $aSimilarConst = $this->_collectSimilar($aMap, $sKey, $aSimilarConst);

        // checking if in theme options exist
        if (count($this->_aAdditionalLangFiles)) {
            $aLang = $this->_getLangTranslationArray($iLang, $blAdmin, $this->_aAdditionalLangFiles);
            $aSimilarConst = $this->_collectSimilar($aLang, $sKey, $aSimilarConst);
        }

        stopProfile("getSimilarByKey");

        return $aSimilarConst;
    }

    /**
     * Returns formatted number, according to active currency formatting standards.
     *
     * @param float  $dValue  Plain price
     * @param object $oActCur Object of active currency
     *
     * @return string
     */
    public function formatCurrency($dValue, $oActCur = null)
    {
        if (!$oActCur) {
            $oActCur = $this->getConfig()->getActShopCurrencyObject();
        }
        $sValue = \OxidEsales\Eshop\Core\Registry::getUtils()->fRound($dValue, $oActCur);

        return number_format((double) $sValue, $oActCur->decimal, $oActCur->dec, $oActCur->thousand);
    }

    /**
     * Returns formatted vat value, according to formatting standards.
     *
     * @param float  $dValue  Plain price
     * @param object $oActCur Object of active currency
     *
     * @return string
     */
    public function formatVat($dValue, $oActCur = null)
    {
        $iDecPos = 0;
        $sValue = (string) $dValue;
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = getStr();
        if (($iDotPos = $oStr->strpos($sValue, '.')) !== false) {
            $iDecPos = $oStr->strlen($oStr->substr($sValue, $iDotPos + 1));
        }

        $oActCur = $oActCur ? $oActCur : $this->getConfig()->getActShopCurrencyObject();
        $iDecPos = ($iDecPos < $oActCur->decimal) ? $iDecPos : $oActCur->decimal;

        return number_format((double) $dValue, $iDecPos, $oActCur->dec, $oActCur->thousand);
    }

    /**
     * According to user configuration forms and return language prefix.
     *
     * @param integer $iLanguage User selected language (default null)
     *
     * @return string
     */
    public function getLanguageTag($iLanguage = null)
    {
        if (!isset($iLanguage)) {
            $iLanguage = $this->getBaseLanguage();
        }

        $iLanguage = (int) $iLanguage;

        return ($iLanguage) ? "_$iLanguage" : "";
    }

    /**
     * Validate language id. If not valid id, returns default value
     *
     * @param int $iLang Language id
     *
     * @return int
     */
    public function validateLanguage($iLang = null)
    {
        // checking if this language is valid
        $aLanguages = $this->getLanguageArray(null, !$this->isAdmin());
        if (!isset($aLanguages[$iLang]) && is_array($aLanguages)) {
            $oLang = current($aLanguages);
            if (isset($oLang->id)) {
                $iLang = $oLang->id;
            }
        }

        return (int) $iLang;
    }

    /**
     * Set base shop language
     *
     * @param int $iLang Language id
     */
    public function setBaseLanguage($iLang = null)
    {
        if (is_null($iLang)) {
            $iLang = $this->getBaseLanguage();
        } else {
            $this->_iBaseLanguageId = (int) $iLang;
        }

        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('language', $iLang);
    }

    /**
     * Validates and sets templates language id
     *
     * @param int $iLang Language id
     *
     * @return null
     */
    public function setTplLanguage($iLang = null)
    {
        $this->_iTplLanguageId = isset($iLang) ? (int) $iLang : $this->getBaseLanguage();
        if ($this->isAdmin()) {
            $aLanguages = $this->getAdminTplLanguageArray();
            if (!isset($aLanguages[$this->_iTplLanguageId])) {
                $this->_iTplLanguageId = key($aLanguages);
            }
        }

        \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('tpllanguage', $this->_iTplLanguageId);

        return $this->_iTplLanguageId;
    }

    /**
     * Goes through language array and recodes its values if encoding does not fit with needed one. Returns recoded data
     *
     * @param array  $aLangArray   language data
     * @param string $sCharset     charset which was used while making file
     * @param bool   $blRecodeKeys leave keys untouched or recode it
     *
     * @deprecated since 6.0 (2016-12-07) As the shop installation is utf-8, this method will be removed.
     *
     * @return array
     */
    protected function _recodeLangArray($aLangArray, $sCharset, $blRecodeKeys = false) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $newEncoding = $this->getTranslationsExpectedEncoding();

        if ($sCharset == $newEncoding) {
            return $aLangArray;
        }
        if ($blRecodeKeys) {
            $aLangArray = $this->_recodeLangArrayWithKeys($aLangArray, $sCharset, $newEncoding);
        } else {
            $this->_recodeLangArrayValues($aLangArray, $sCharset, $newEncoding);
        }

        return $aLangArray;
    }

    /**
     * Goes through language array and recodes its values.
     *
     * @param array  $aLangArray  language data
     * @param string $sCharset    charset which was used while making file
     * @param string $newEncoding charset which was used while making file
     *
     * @deprecated since 6.0 (2016-12-07) As the shop installation is utf-8, this method will be removed.
     */
    protected function _recodeLangArrayValues(&$aLangArray, $sCharset, $newEncoding) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        foreach ($aLangArray as $sItemKey => &$sValue) {
            $sValue = iconv($sCharset, $newEncoding, $sValue);
        }
    }

    /**
     * Goes through language array and recodes its values and keys. Returns recoded data
     *
     * @param array  $aLangArray  language data
     * @param string $sCharset    charset which was used while making file
     * @param string $newEncoding charset which was used while making file
     *
     * @deprecated since 6.0 (2016-12-07) As the shop installation is utf-8, this method will be removed.
     *
     * @return array
     */
    protected function _recodeLangArrayWithKeys($aLangArray, $sCharset, $newEncoding) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aLangs = [];
        foreach ($aLangArray as $sItemKey => $sValue) {
            $sItemKey = iconv($sCharset, $newEncoding, $sItemKey);
            $aLangs[$sItemKey] = iconv($sCharset, $newEncoding, $sValue);
        }

        return $aLangs;
    }


    /**
     * Returns the encoding all translations will be converted to.
     *
     * @return string
     */
    protected function getTranslationsExpectedEncoding()
    {
        return 'UTF-8';
    }

    /**
     * Returns array with paths where frontend language files are stored
     *
     * @param int $iLang active language
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLangFilesPathArray" in next major
     */
    protected function _getLangFilesPathArray($iLang) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oConfig = $this->getConfig();
        $aLangFiles = [];

        $sAppDir = $oConfig->getAppDir();
        $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageAbbr($iLang);
        $sTheme = $oConfig->getConfigParam("sTheme");
        $aModulePaths = $this->_getActiveModuleInfo();

        //get generic lang files
        $sGenericPath = $sAppDir . 'translations/' . $sLang;
        if ($sGenericPath) {
            $aLangFiles[] = $sGenericPath . "/lang.php";
            $aLangFiles = $this->_appendLangFile($aLangFiles, $sGenericPath);
        }

        //get theme lang files
        if ($sTheme) {
            $sThemePath = $sAppDir . 'views/' . $sTheme . '/' . $sLang;
            $aLangFiles[] = $sThemePath . "/lang.php";
            $aLangFiles = $this->_appendLangFile($aLangFiles, $sThemePath);
        }

        $aLangFiles = array_merge($aLangFiles, $this->getCustomThemeLanguageFiles($iLang));

        // modules language files
        $aLangFiles = $this->_appendModuleLangFiles($aLangFiles, $aModulePaths, $sLang);

        // custom language files
        $aLangFiles = $this->_appendCustomLangFiles($aLangFiles, $sLang);

        return count($aLangFiles) ? $aLangFiles : false;
    }

    /**
     * Returns custom theme language files.
     *
     * @param int $language active language
     *
     * @return array
     */
    protected function getCustomThemeLanguageFiles($language)
    {
        $oConfig = $this->getConfig();
        $sCustomTheme = $oConfig->getConfigParam("sCustomTheme");
        $sAppDir = $oConfig->getAppDir();
        $sLang = \OxidEsales\Eshop\Core\Registry::getLang()->getLanguageAbbr($language);
        $aLangFiles = [];

        if ($sCustomTheme) {
            $sCustPath = $sAppDir . 'views/' . $sCustomTheme . '/' . $sLang;
            $aLangFiles[] = $sCustPath . "/lang.php";
            $aLangFiles = $this->_appendLangFile($aLangFiles, $sCustPath);
        }

        return $aLangFiles;
    }

    /**
     * Returns array with paths where admin language files are stored
     *
     * @param int $activeLanguage The active language
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getAdminLangFilesPathArray" in next major
     */
    protected function _getAdminLangFilesPathArray($activeLanguage) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $config = $this->getConfig();
        $langFiles = [];

        $appDirectory = $config->getAppDir();
        $language = Registry::getLang()->getLanguageAbbr($activeLanguage);

        $modulePaths = [];
        $modulePaths = array_merge($modulePaths, $this->_getActiveModuleInfo());
        $modulePaths = array_merge($modulePaths, $this->_getDisabledModuleInfo());

        // admin lang files
        $adminThemeName = $this->getContainer()->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        $adminPath = $appDirectory .
            'views' . DIRECTORY_SEPARATOR .
            $adminThemeName . DIRECTORY_SEPARATOR .
            $language;

        $langFiles[] = $adminPath . DIRECTORY_SEPARATOR . 'lang.php';
        $langFiles[] = $appDirectory .
            'translations' . DIRECTORY_SEPARATOR .
            $language . DIRECTORY_SEPARATOR .
            'translit_lang.php';
        $langFiles = $this->_appendLangFile($langFiles, $adminPath);

        // themes options lang files
        $themePath = $appDirectory . 'views/*/' . $language;
        $langFiles = $this->_appendLangFile($langFiles, $themePath, "options");

        // module language files
        $langFiles = $this->_appendModuleLangFiles($langFiles, $modulePaths, $language, true);

        // custom language files
        $langFiles = $this->_appendCustomLangFiles($langFiles, $language, true);

        return count($langFiles) ? $langFiles : false;
    }

    /**
     * Appends lang or options files if exists, except custom lang files
     *
     * @param array  $aLangFiles   existing language files
     * @param string $sFullPath    path to language files to append
     * @param string $sFilePattern file pattern to search for, default is "lang"
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "appendLangFile" in next major
     */
    protected function _appendLangFile($aLangFiles, $sFullPath, $sFilePattern = "lang") // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aFiles = glob($sFullPath . "/*_{$sFilePattern}.php");
        if (is_array($aFiles) && count($aFiles)) {
            foreach ($aFiles as $sFile) {
                if (!strpos($sFile, 'cust_lang.php')) {
                    $aLangFiles[] = $sFile;
                }
            }
        }

        return $aLangFiles;
    }

    /**
     * Appends Custom language files cust_lang.php
     *
     * @param array  $languageFiles existing language files
     * @param string $language      language abbreviation
     * @param bool   $forAdmin      add files for admin
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "appendCustomLangFiles" in next major
     */
    protected function _appendCustomLangFiles($languageFiles, $language, $forAdmin = false) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if ($forAdmin) {
            $adminThemeName = $this->getContainer()->get(AdminThemeBridgeInterface::class)->getActiveTheme();
            $languageFiles[] = $this->getCustomFilePath($language, $adminThemeName);
        } else {
            $config = $this->getConfig();
            if ($config->getConfigParam("sTheme")) {
                $languageFiles[] = $this->getCustomFilePath($language, $config->getConfigParam("sTheme"));
            }
            if ($config->getConfigParam("sCustomTheme")) {
                $languageFiles[] = $this->getCustomFilePath($language, $config->getConfigParam("sCustomTheme"));
            }
        }

        return $languageFiles;
    }

    /**
     * @param int    $language  The language index
     * @param string $themeName The name of the theme
     *
     * @return string
     */
    private function getCustomFilePath($language, $themeName)
    {
        $config = $this->getConfig();
        return $config->getAppDir() .
            'views' . DIRECTORY_SEPARATOR .
            $themeName . DIRECTORY_SEPARATOR  .
            $language . DIRECTORY_SEPARATOR .
            'cust_lang.php';
    }

    /**
     * Appends module lang or options files if exists
     *
     * @param array  $aLangFiles   existing language files
     * @param array  $aModulePaths module language file paths
     * @param string $sLang        language abbreviation
     * @param bool   $blForAdmin   add files for admin
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "appendModuleLangFiles" in next major
     */
    protected function _appendModuleLangFiles($aLangFiles, $aModulePaths, $sLang, $blForAdmin = false) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if (is_array($aModulePaths)) {
            foreach ($aModulePaths as $sPath) {
                $moduleTranslationPathFinder = $this->getModuleTranslationPathFinder();
                $sFullPath = $moduleTranslationPathFinder->findTranslationPath($sLang, $blForAdmin, $sPath);

                $aLangFiles = $this->_appendLangFile($aLangFiles, $sFullPath);
                //load admin modules options lang files
                if ($blForAdmin) {
                    $aLangFiles[] = $sFullPath . '/module_options.php';
                }
            }
        }

        return $aLangFiles;
    }

    /**
     * Returns language cache file name
     *
     * @param bool  $blAdmin    admin or not
     * @param int   $iLang      current language id
     * @param array $aLangFiles language files to load [optional]
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLangFileCacheName" in next major
     */
    protected function _getLangFileCacheName($blAdmin, $iLang, $aLangFiles = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $myConfig = $this->getConfig();
        $sLangFilesIdent = '_default';
        if (is_array($aLangFiles) && $aLangFiles) {
            $sLangFilesIdent = '_' . md5(implode('+', $aLangFiles));
        }

        return "langcache_" . ((int) $blAdmin) . "_{$iLang}_" . $myConfig->getShopId() . "_" . $myConfig->getConfigParam('sTheme') . $sLangFilesIdent;
    }

    /**
     * Returns language cache array
     *
     * @param bool  $blAdmin    admin or not [optional]
     * @param int   $iLang      current language id [optional]
     * @param array $aLangFiles language files to load [optional]
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLanguageFileData" in next major
     */
    protected function _getLanguageFileData($blAdmin = false, $iLang = 0, $aLangFiles = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();

        $sCacheName = $this->_getLangFileCacheName($blAdmin, $iLang, $aLangFiles);
        $aLangCache = $myUtils->getLangCache($sCacheName);
        if (!$aLangCache && $aLangFiles === null) {
            if ($blAdmin) {
                $aLangFiles = $this->_getAdminLangFilesPathArray($iLang);
            } else {
                $aLangFiles = $this->_getLangFilesPathArray($iLang);
            }
        }
        if (!$aLangCache && $aLangFiles) {
            $aLangCache = [];
            $sBaseCharset = $this->getTranslationsExpectedEncoding();
            $aLang = [];
            $aLangSeoReplaceChars = [];
            foreach ($aLangFiles as $sLangFile) {
                if (file_exists($sLangFile) && is_readable($sLangFile)) {
                    //$aSeoReplaceChars null indicates that there is no setting made
                    $aSeoReplaceChars = null;
                    include $sLangFile;

                    $aLang = array_merge(['charset' => 'UTF-8'], $aLang);

                    if (isset($aSeoReplaceChars) && is_array($aSeoReplaceChars)) {
                        $aLangSeoReplaceChars = array_merge($aLangSeoReplaceChars, $aSeoReplaceChars);
                    }

                    $aLangCache = array_merge($aLangCache, $aLang);
                }
            }

            $aLangCache['charset'] = $sBaseCharset;

            // special character replacement list
            $aLangCache['_aSeoReplaceChars'] = $aLangSeoReplaceChars;

            //save to cache
            $myUtils->setLangCache($sCacheName, $aLangCache);
        }

        return $aLangCache;
    }

    /**
     * Returns language map array
     *
     * @param int  $language   language index
     * @param bool $isAdmin admin mode [default NULL]
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLanguageMap" in next major
     */
    protected function _getLanguageMap($language, $isAdmin = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $isAdmin = isset($isAdmin) ? $isAdmin : $this->isAdmin();
        $key = $language . ((int) $isAdmin);
        if (!isset($this->_aLangMap[$key])) {
            $this->_aLangMap[$key] = [];
            $config = $this->getConfig();

            $mapFile = '';
            $parentThemeDirectory = $this->getRealThemeName($config->getConfigParam("sTheme"), $isAdmin);
            $customThemeDirectory = $this->getRealThemeName($config->getConfigParam("sCustomTheme"), $isAdmin);

            $parentMapFile = $this->getLanguageMappingFilePath($language, $parentThemeDirectory);
            $customThemeMapFile = $this->getLanguageMappingFilePath($language, $customThemeDirectory);

            if (file_exists($customThemeMapFile) && is_readable($customThemeMapFile)) {
                $mapFile = $customThemeMapFile;
            } elseif (file_exists($parentMapFile) && is_readable($parentMapFile)) {
                $mapFile = $parentMapFile;
            }

            if ($mapFile) {
                include $mapFile;
                $this->_aLangMap[$key] = $aMap;
            }
        }

        return $this->_aLangMap[$key];
    }

    /**
     * @param int    $language  The language index
     * @param string $themeName The name of the theme
     *
     * @return string
     */
    private function getLanguageMappingFilePath($language, $themeName)
    {
        $config = $this->getConfig();
        return $config->getAppDir() . DIRECTORY_SEPARATOR .
            'views' . DIRECTORY_SEPARATOR .
            $themeName . DIRECTORY_SEPARATOR .
            Registry::getLang()->getLanguageAbbr($language) . DIRECTORY_SEPARATOR .
            'map.php';
    }

    /**
     * @param bool   $isAdmin   The admin mode [default NULL]
     * @param string $themeName The name of the theme
     *
     * @return string
     */
    private function getRealThemeName($themeName, $isAdmin = null)
    {
        $adminTheme = $this->getContainer()->get(AdminThemeBridgeInterface::class)->getActiveTheme();
        return ($isAdmin ? $adminTheme : $themeName);
    }

    /**
     * Returns current language cache language id
     *
     * @param bool $blAdmin admin mode
     * @param int  $iLang   language id [optional]
     *
     * @return int
     * @deprecated underscore prefix violates PSR12, will be renamed to "getCacheLanguageId" in next major
     */
    protected function _getCacheLanguageId($blAdmin, $iLang = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $iLang = ($iLang === null && $blAdmin) ? $this->getTplLanguage() : $iLang;
        if (!isset($iLang)) {
            $iLang = $this->getBaseLanguage();
            if (!isset($iLang)) {
                $iLang = 0;
            }
        }

        return (int) $iLang;
    }

    /**
     * get language array from lang translation file
     *
     * @param int   $iLang      optional language
     * @param bool  $blAdmin    admin mode switch
     * @param array $aLangFiles language files to load [optional]
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLangTranslationArray" in next major
     */
    protected function _getLangTranslationArray($iLang = null, $blAdmin = null, $aLangFiles = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        startProfile("_getLangTranslationArray");

        $blAdmin = isset($blAdmin) ? $blAdmin : $this->isAdmin();
        $iLang = $this->_getCacheLanguageId($blAdmin, $iLang);
        $sCacheName = $this->_getLangFileCacheName($blAdmin, $iLang, $aLangFiles);

        if (!isset($this->_aLangCache[$sCacheName])) {
            $this->_aLangCache[$sCacheName] = [];
        }
        if (!isset($this->_aLangCache[$sCacheName][$iLang])) {
            // loading main lang files data
            $this->_aLangCache[$sCacheName][$iLang] = $this->_getLanguageFileData($blAdmin, $iLang, $aLangFiles);
        }

        stopProfile("_getLangTranslationArray");

        // if language array exists ..
        return (isset($this->_aLangCache[$sCacheName][$iLang]) ? $this->_aLangCache[$sCacheName][$iLang] : []);
    }

    /**
     * Language sorting callback function
     *
     * @param object $a1 first value to check
     * @param object $a2 second value to check
     *
     * @return bool
     * @deprecated underscore prefix violates PSR12, will be renamed to "sortLanguagesCallback" in next major
     */
    protected function _sortLanguagesCallback($a1, $a2) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return ($a1->sort > $a2->sort);
    }

    /**
     * Returns language id param name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_sName;
    }

    /**
     * Returns form hidden language parameter
     *
     * @return string
     */
    public function getFormLang()
    {
        if (!$this->isAdmin()) {
            return "<input type=\"hidden\" name=\"" . $this->getName() . "\" value=\"" . $this->getBaseLanguage() . "\" />";
        }
    }

    /**
     * Returns url language parameter
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getUrlLang($iLang = null)
    {
        if (!$this->isAdmin()) {
            $iLang = isset($iLang) ? $iLang : $this->getBaseLanguage();
            return $this->getName() . "=" . $iLang;
        }
    }

    /**
     * Is needed appends url with language parameter
     * Direct usage of this method to retrieve end url result is discouraged - instead
     * see \OxidEsales\Eshop\Core\UtilsUrl::processUrl
     *
     * @param string $sUrl  url to process
     * @param int    $iLang language id [optional]
     *
     * @see \OxidEsales\Eshop\Core\UtilsUrl::processUrl
     *
     * @return string
     */
    public function processUrl($sUrl, $iLang = null)
    {
        $iLang = isset($iLang) ? $iLang : $this->getBaseLanguage();
        $iDefaultLang = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sDefaultLang');
        $iBrowserLanguage = (int) $this->detectLanguageByBrowser();
        /** @var \OxidEsales\Eshop\Core\StrRegular $oStr */
        $oStr = getStr();

        if (!$this->isAdmin()) {
            $sParam = $this->getUrlLang($iLang);
            if (
                !$oStr->preg_match('/(\?|&(amp;)?)lang=[0-9]+/', $sUrl) &&
                ($iLang != $iDefaultLang || $iDefaultLang != $iBrowserLanguage)
            ) {
                if ($sUrl) {
                    if ($oStr->strpos($sUrl, '?') === false) {
                        $sUrl .= "?";
                    } elseif (!$oStr->preg_match('/(\?|&(amp;)?)$/', $sUrl)) {
                        $sUrl .= "&amp;";
                    }
                }
                $sUrl .= $sParam . "&amp;";
            } else {
                $sUrl = $oStr->preg_replace('/(\?|&(amp;)?)lang=[0-9]+/', '\1' . $sParam, $sUrl);
            }
        }

        return $sUrl;
    }

    /**
     * Detect language by user browser settings. Returns language ID if
     * detected, otherwise returns null.
     *
     * @return int
     */
    public function detectLanguageByBrowser()
    {
        $sBrowserLanguage = $this->_getBrowserLanguage();

        if (!is_null($sBrowserLanguage)) {
            $aLanguages = $this->getLanguageArray(null, true);
            foreach ($aLanguages as $oLang) {
                if ($oLang->abbr == $sBrowserLanguage) {
                    return $oLang->id;
                }
            }
        }
    }

    /**
     * Returns all multi language tables
     *
     * @return array
     */
    public function getMultiLangTables()
    {
        $aTables = ["oxarticles", "oxartextends", "oxattribute",
                         "oxcategories", "oxcontents", "oxcountry",
                         "oxdelivery", "oxdiscount", "oxgroups",
                         "oxlinks",
                         // @deprecated 6.5.6 "News" feature will be removed completely
                         "oxnews",
                         // END deprecated
                         "oxobject2attribute",
                         "oxpayments", "oxselectlist", "oxshops",
                         "oxactions", "oxwrapping", "oxdeliveryset",
                         "oxvendor", "oxmanufacturers", "oxmediaurls",
                         "oxstates"];

        $aMultiLangTables = $this->getConfig()->getConfigParam('aMultiLangTables');

        if (is_array($aMultiLangTables)) {
            $aTables = array_merge($aTables, $aMultiLangTables);
        }

        return $aTables;
    }

    /**
     * Get SEO spec. chars replacement list for current language
     *
     * @param int $iLang language ID
     *
     * @return null
     */
    public function getSeoReplaceChars($iLang)
    {
        // get language replace chars
        $aSeoReplaceChars = $this->translateString('_aSeoReplaceChars', $iLang);
        if (!is_array($aSeoReplaceChars)) {
            $aSeoReplaceChars = [];
        }

        return $aSeoReplaceChars;
    }

    /**
     * Returns active module Ids with paths
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getActiveModuleInfo" in next major
     */
    protected function _getActiveModuleInfo() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if ($this->_aActiveModuleInfo === null) {
            $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
            $this->_aActiveModuleInfo = $oModuleList->getActiveModuleInfo();
        }

        return $this->_aActiveModuleInfo;
    }

    /**
     * Returns active module Ids with paths
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getDisabledModuleInfo" in next major
     */
    protected function _getDisabledModuleInfo() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if ($this->_aDisabledModuleInfo === null) {
            $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
            $this->_aDisabledModuleInfo = $oModuleList->getDisabledModuleInfo();
        }

        return $this->_aDisabledModuleInfo;
    }

    /**
     * Gets browser language.
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getBrowserLanguage" in next major
     */
    protected function _getBrowserLanguage() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $_SERVER['HTTP_ACCEPT_LANGUAGE']) {
            return strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
        }
    }

    /**
     * Returns available language IDs (abbreviations) for all sub shops
     *
     * @return array
     */
    public function getAllShopLanguageIds()
    {
        return $this->_getLanguageIdsFromDatabase();
    }

    /**
     * Get current Shop language ids.
     *
     * @param int $iShopId shop id
     *
     * @return array
     */
    public function getLanguageIds($iShopId = null)
    {
        if (empty($iShopId) || $iShopId == $this->getConfig()->getShopId()) {
            $aLanguages = $this->getActiveShopLanguageIds();
        } else {
            $aLanguages = $this->_getLanguageIdsFromDatabase($iShopId);
        }

        return $aLanguages;
    }

    /**
     * Returns available language IDs (abbreviations)
     *
     * @return array
     */
    public function getActiveShopLanguageIds()
    {
        $oConfig = $this->getConfig();

        //if exists language parameters array, extract lang id's from there
        $aLangParams = $oConfig->getConfigParam('aLanguageParams');
        if (is_array($aLangParams)) {
            $aIds = $this->_getLanguageIdsFromLanguageParamsArray($aLangParams);
        } else {
            $aIds = $this->_getLanguageIdsFromLanguagesArray($oConfig->getConfigParam('aLanguages'));
        }

        return $aIds;
    }

    /**
     * Gets language Ids for given shopId or for all subshops
     *
     * @param null $shopId
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLanguageIdsFromDatabase" in next major
     */
    protected function _getLanguageIdsFromDatabase($shopId = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return $this->getLanguageIds();
    }

    /**
     * Returns list of all language codes taken from config values of given 'aLanguages' (for all subshops)
     *
     * @param string $sLanguageParameterName language config parameter name
     * @param int    $iShopId                shop id
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getConfigLanguageValues" in next major
     */
    protected function _getConfigLanguageValues($sLanguageParameterName, $iShopId = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aConfigDecodedValues = [];
        $aConfigValues = $this->_selectLanguageParamValues($sLanguageParameterName, $iShopId);

        foreach ($aConfigValues as $sConfigValue) {
            $aConfigLanguages = unserialize($sConfigValue['oxvarvalue']);

            $aLanguages = [];
            if ($sLanguageParameterName == 'aLanguageParams') {
                $aLanguages = $this->_getLanguageIdsFromLanguageParamsArray($aConfigLanguages);
            } elseif ($sLanguageParameterName == 'aLanguages') {
                $aLanguages = $this->_getLanguageIdsFromLanguagesArray($aConfigLanguages);
            }

            $aConfigDecodedValues = array_unique(array_merge($aConfigDecodedValues, $aLanguages));
        }

        return $aConfigDecodedValues;
    }

    /**
     * Returns array of all config values of given paramName
     *
     * @param string      $sParamName Parameter name
     * @param string|null $sShopId    Shop id
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "selectLanguageParamValues" in next major
     */
    protected function _selectLanguageParamValues($sParamName, $sShopId = null) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $params = [
            ':oxvarname' => $sParamName
        ];
        $sQuery = "
            select " . $oConfig->getDecodeValueQuery() . " as oxvarvalue
            from oxconfig
            where oxvarname = :oxvarname";

        if (!empty($sShopId)) {
            $sQuery .= " and oxshopid = :oxshopid limit 1";
            $params[':oxshopid'] = $sShopId;
        }

        return $oDb->getAll($sQuery, $params);
    }

    /**
     * gets language code array from aLanguageParams array
     *
     * @param array $aLanguageParams Language parameters
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLanguageIdsFromLanguageParamsArray" in next major
     */
    protected function _getLanguageIdsFromLanguageParamsArray($aLanguageParams) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aLanguages = [];
        foreach ($aLanguageParams as $sAbbr => $aValue) {
            $iBaseId = (int) $aValue['baseId'];
            $aLanguages[$iBaseId] = $sAbbr;
        }

        return $aLanguages;
    }

    /**
     * gets language code array from aLanguages array
     *
     * @param array $aLanguages Languages
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getLanguageIdsFromLanguagesArray" in next major
     */
    protected function _getLanguageIdsFromLanguagesArray($aLanguages) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        return array_keys($aLanguages);
    }

    /**
     * Getter for the module translation path finder.
     *
     * @return \OxidEsales\Eshop\Core\Module\ModuleTranslationPathFinder The module translation finder.
     */
    protected function getModuleTranslationPathFinder()
    {
        if (is_null($this->moduleTranslationPathFinder)) {
            $this->moduleTranslationPathFinder = oxNew(\OxidEsales\Eshop\Core\Module\ModuleTranslationPathFinder::class);
        }

        return $this->moduleTranslationPathFinder;
    }
}
