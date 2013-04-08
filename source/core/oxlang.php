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

/**
 * Language related utility class
 */
class oxLang extends oxSuperCfg
{
    /**
     * oxLang instance.
     *
     * @var oxlang
     */
    private static $_instance = null;

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
    protected $_aLangCache = array();

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
    protected $_aAdditionalLangFiles = array();

    /**
     * registered additional language filesets to load
     *
     * @var array
     */
    protected $_aLangMap = array();

    /**
     * Active module Ids and paths array
     *
     * @var array
     */
    protected $_aActiveModuleInfo = null;

    /**
     * resturns a single instance of this class
     *
     * @return oxLang
     */
    public static function getInstance()
    {
        if ( defined('OXID_PHP_UNIT')) {
            if ( ($oClassMod = modInstances::getMod(__CLASS__))  && is_object($oClassMod) ) {
                return $oClassMod;
            } else {
                $inst = oxNew( 'oxLang' );
                 modInstances::addMod( __CLASS__, $inst );
                 return $inst;
            }
        }

        if ( !self::$_instance instanceof oxLang ) {

            self::$_instance = oxNew( 'oxLang');
        }
        return self::$_instance;
    }

    /**
     * resetBaseLanguage resets base language id cache
     *
     * @access public
     * @return void
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
        if ( $this->_iBaseLanguageId === null ) {

            $myConfig = $this->getConfig();
            $blAdmin = $this->isAdmin();

            // languages and search engines
            if ( $blAdmin && ( ( $iSeLang = oxConfig::getParameter( 'changelang' ) ) !== null ) ) {
                $this->_iBaseLanguageId = $iSeLang;
            }

            if ( is_null( $this->_iBaseLanguageId ) ) {
                $this->_iBaseLanguageId = oxConfig::getParameter( 'lang' );
            }

            //or determining by domain
            $aLanguageUrls = $myConfig->getConfigParam( 'aLanguageURLs' );

            if ( !$blAdmin && is_array( $aLanguageUrls ) ) {
                foreach ( $aLanguageUrls as $iId => $sUrl ) {
                    if ( $myConfig->isCurrentUrl( $sUrl ) ) {
                        $this->_iBaseLanguageId = $iId;
                        break;
                    }
                }
            }

            if ( is_null( $this->_iBaseLanguageId ) ) {
                $this->_iBaseLanguageId = oxConfig::getParameter( 'language' );
                if (!isset($this->_iBaseLanguageId)) {
                    $this->_iBaseLanguageId = oxSession::getVar('language');
                }
            }

            // if language still not setted and not search engine browsing,
            // getting language from browser
            if ( is_null( $this->_iBaseLanguageId ) && !$blAdmin && !oxUtils::getInstance()->isSearchEngine() ) {

                // getting from cookie
                $this->_iBaseLanguageId = oxUtilsServer::getInstance()->getOxCookie( 'language' );

                // getting from browser
                if ( is_null( $this->_iBaseLanguageId ) ) {
                    $this->_iBaseLanguageId = $this->detectLanguageByBrowser();
                }
            }

            if ( is_null( $this->_iBaseLanguageId ) ) {
                $this->_iBaseLanguageId = $myConfig->getConfigParam( 'sDefaultLang' );
            }

            $this->_iBaseLanguageId = (int) $this->_iBaseLanguageId;

            // validating language
            $this->_iBaseLanguageId = $this->validateLanguage( $this->_iBaseLanguageId );

            // setting language to cookie
            oxUtilsServer::getInstance()->setOxCookie( 'language', $this->_iBaseLanguageId );
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
        if ( $this->_iObjectTplLanguageId === null ) {
            $this->_iObjectTplLanguageId = $this->getTplLanguage();
            $aLanguages = $this->getAdminTplLanguageArray();
            if ( !isset( $aLanguages[$this->_iObjectTplLanguageId] ) ||
                 $aLanguages[$this->_iObjectTplLanguageId]->active == 0 ) {
                $this->_iObjectTplLanguageId = key( $aLanguages );
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
        if ( $this->_iTplLanguageId === null ) {
            $iSessLang = oxSession::getVar( 'tpllanguage' );
            $this->_iTplLanguageId = $this->isAdmin() ? $this->setTplLanguage( $iSessLang ) : $this->getBaseLanguage();
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
        if ( $this->_iEditLanguageId === null ) {

            if ( !$this->isAdmin() ) {
                $this->_iEditLanguageId = $this->getBaseLanguage();
            } else {

                $iLang = null;
                // choosing language ident
                // check if we really need to set the new language
                if ( "saveinnlang" == $this->getConfig()->getActiveView()->getFncName() ) {
                    $iLang = oxConfig::getParameter( "new_lang");
                }
                $iLang = ( $iLang === null ) ? oxConfig::getParameter( 'editlanguage' ) : $iLang;
                $iLang = ( $iLang === null ) ? oxSession::getVar( 'editlanguage' ) : $iLang;
                $iLang = ( $iLang === null ) ? $this->getBaseLanguage() : $iLang;

                // validating language
                $this->_iEditLanguageId = $this->validateLanguage( $iLang );

                // writing to session
                oxSession::setVar( 'editlanguage', $this->_iEditLanguageId );
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
    public function getLanguageArray( $iLanguage = null, $blOnlyActive = false, $blSort = false )
    {
        $myConfig = $this->getConfig();

        if ( is_null($iLanguage) ) {
            $iLanguage = $this->_iBaseLanguageId;
        }

        $aLanguages = array();
        $aConfLanguages = $myConfig->getConfigParam( 'aLanguages' );
        $aLangParams    = $myConfig->getConfigParam( 'aLanguageParams' );

        if ( is_array( $aConfLanguages ) ) {
            $i = 0;
            reset( $aConfLanguages );
            while ( list( $key, $val ) = each( $aConfLanguages ) ) {

                if ( $blOnlyActive && is_array($aLangParams) ) {
                    //skipping non active languages
                    if ( !$aLangParams[$key]['active'] ) {
                        $i++;
                        continue;
                    }
                }

                if ( $val ) {
                    $oLang = new oxStdClass();
                    $oLang->id   = isset($aLangParams[$key]['baseId']) ? $aLangParams[$key]['baseId'] : $i;
                    $oLang->oxid = $key;
                    $oLang->abbr = $key;
                    $oLang->name = $val;

                    if ( is_array( $aLangParams ) ) {
                        $oLang->active = $aLangParams[$key]['active'];
                        $oLang->sort   = $aLangParams[$key]['sort'];
                    }

                    $oLang->selected = ( isset( $iLanguage ) && $oLang->id == $iLanguage ) ? 1 : 0;
                    $aLanguages[$oLang->id] = $oLang;
                }
                ++$i;
            }
        }

        if ( $blSort && is_array($aLangParams) ) {
            uasort( $aLanguages, array($this, '_sortLanguagesCallback') );
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
        if ( $this->_aAdminTplLanguageArray === null ) {

            // #656 add admin languages
            $aLangData = array();
            $aLangIds  = $this->getLanguageIds();

            $sSourceDir = $this->getConfig()->getStdLanguagePath( "", true, false );
            foreach ( glob( $sSourceDir."*", GLOB_ONLYDIR ) as $sDir ) {
                $sFilePath = "{$sDir}/lang.php";
                if ( file_exists( $sFilePath ) && is_readable( $sFilePath ) ) {
                    $sLangName = "";
                    $sAbbr = strtolower( basename( $sDir ) );
                    if ( !in_array( $sAbbr, $aLangIds ) ) {
                        include $sFilePath;
                        $aLangData[$sAbbr] = new oxStdClass();
                        $aLangData[$sAbbr]->name = $sLangName;
                        $aLangData[$sAbbr]->abbr = $sAbbr;
                    }
                }
            }

            $this->_aAdminTplLanguageArray = $this->getLanguageArray();
            if ( count( $aLangData ) ) {

                // sorting languages for selection list view
                ksort( $aLangData );
                $iSort = max( array_keys( $this->_aAdminTplLanguageArray ) );

                // appending other languages
                foreach ( $aLangData as $oLang ) {
                    $oLang->id = $oLang->sort = ++$iSort;
                    $oLang->selected = 0;
                    $oLang->active   = 0;
                    $this->_aAdminTplLanguageArray[$iSort] = $oLang;
                }
            }
        }

        // moving pointer to beginning
        reset( $this->_aAdminTplLanguageArray );
        return $this->_aAdminTplLanguageArray;
    }

    /**
     * Returns selected language abbervation
     *
     * @param int $iLanguage language id [optional]
     *
     * @return string
     */
    public function getLanguageAbbr( $iLanguage = null )
    {
        if ( $this->_aLangAbbr === null ) {
            $this->_aLangAbbr = array();
            if ( $this->isAdmin() ) {
                foreach ( $this->getAdminTplLanguageArray() as $oLang ) {
                    $this->_aLangAbbr[$oLang->id] = $oLang->abbr;
                }
            } else {
                $this->_aLangAbbr = $this->getLanguageIds();
            }
        }

        $iLanguage = isset( $iLanguage ) ? (int) $iLanguage : $this->getBaseLanguage();
        if ( isset( $this->_aLangAbbr[$iLanguage] ) ) {
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
        $aConfLanguages = $this->getConfig()->getConfigParam( 'aLanguages' );
        $aLangIds = $this->getLanguageIds();
        $aLanguages = array();
        foreach ( $aLangIds as $iId => $sValue ) {
            $aLanguages[$iId] = $aConfLanguages[$sValue];
        }
        return $aLanguages;
    }

    /**
     * Returns available language IDs (abbervations)
     *
     * @return array
     */
    public function getLanguageIds()
    {
        $myConfig = $this->getConfig();
        $aIds = array();

        //if exists language parameters array, extract lang id's from there
        $aLangParams = $myConfig->getConfigParam( 'aLanguageParams' );
        if ( is_array( $aLangParams ) ) {
            foreach ( $aLangParams as $sAbbr => $aValue ) {
                $iBaseId = (int) $aValue['baseId'];
                $aIds[$iBaseId] = $sAbbr;
            }
        } else {
            $aIds = array_keys( $myConfig->getConfigParam( 'aLanguages' ) );
        }

        return $aIds;
    }

    /**
     * register additional language files to be loaded for this session
     *
     * @param string $sFile file name
     *
     * @return null
     * @deprecated since 2012-01-24 marked in version 4.5.7
     */
    public function registerAdditionalLangFile($sFile)
    {
        if (!$sFile || !is_readable($sFile)) {
            $oErr = oxNew( "oxFileException", 'EXCEPTION_FILENOTFOUND');
            $oErr->setFileName($sFile);
            throw $oErr;
        }
        $this->_aAdditionalLangFiles[] = $sFile;
    }

    /**
     * Searches for translation string in file and on success returns translation,
     * otherwise returns initial string.
     *
     * @param string $sStringToTranslate Initial string
     * @param int    $iLang              optional language number
     * @param bool   $blAdminMode        on special case you can force mode, to load language constant from admin/shops language file
     *
     * @throws oxLanguageException in debug mode
     *
     * @return string
     */
    public function translateString( $sStringToTranslate, $iLang = null, $blAdminMode = null )
    {
        // checking if in cash exist
        $aLang = $this->_getLangTranslationArray( $iLang, $blAdminMode );
        if ( isset( $aLang[$sStringToTranslate] ) ) {
            return $aLang[$sStringToTranslate];
        }

        // checking if in map exist
        $aMap = $this->_getLanguageMap( $iLang, $blAdminMode );
        if ( isset( $aLang[$aMap[$sStringToTranslate]] ) ) {
            return $aLang[$aMap[$sStringToTranslate]];
        }

        // checking if in theme options exist
        if (count($this->_aAdditionalLangFiles)) {
            $aLang = $this->_getLangTranslationArray( $iLang, $blAdminMode, $this->_aAdditionalLangFiles);
            if (isset( $aLang[$sStringToTranslate] )) {
                return $aLang[$sStringToTranslate];
            }
        }

        // @deprecated, text files should not be used any more (2011.07.06)
            $blIsAdmin = isset( $blAdminMode ) ? $blAdminMode : $this->isAdmin();
            if ( !$blIsAdmin ) {
                return $this->_readTranslateStrFromTextFile( $sStringToTranslate, $iLang, $blIsAdmin );
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
     */
    protected function _collectSimilar( $aData, $sKey, $aCollection = array() )
    {
        foreach ( $aData as $sValKey => $sValue ) {
            if ( strpos( $sValKey, $sKey ) === 0 ) {
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
    public function getSimilarByKey( $sKey, $iLang = null, $blAdmin = null )
    {
        startProfile("getSimilarByKey");

        $iLang   = isset( $iLang ) ? $iLang : $this->getTplLanguage();
        $blAdmin = isset( $blAdmin ) ? $blAdmin : $this->isAdmin();

        // checking if in cash exist
        $aLang = $this->_getLangTranslationArray( $iLang, $blAdmin );
        $aSimilarConst = $this->_collectSimilar( $aLang, $sKey );

        // checking if in map exist
        $aMap = $this->_getLanguageMap( $iLang, $blAdminMode );
        $aSimilarConst = $this->_collectSimilar( $aMap, $sKey, $aSimilarConst );

        // checking if in theme options exist
        if ( count( $this->_aAdditionalLangFiles ) ) {
            $aLang = $this->_getLangTranslationArray( $iLang, $blAdminMode, $this->_aAdditionalLangFiles);
            $aSimilarConst = $this->_collectSimilar( $aLang, $sKey, $aSimilarConst );
        }

        stopProfile("getSimilarByKey");

        return $aSimilarConst;
    }

    /**
     * Returns formatted number, according to active currency formatting standards.
     *
     * @param double $dValue  Plain price
     * @param object $oActCur Object of active currency
     *
     * @return string
     */
    public function formatCurrency( $dValue, $oActCur = null )
    {
        if ( !$oActCur ) {
            $oActCur = $this->getConfig()->getActShopCurrencyObject();
        }
        return number_format( (double)$dValue, $oActCur->decimal, $oActCur->dec, $oActCur->thousand );
    }

    /**
     * Returns formatted vat value, according to formatting standards.
     *
     * @param double $dValue  Plain price
     * @param object $oActCur Object of active currency
     *
     * @return string
     */
    public function formatVat( $dValue, $oActCur = null )
    {
        $iDecPos = 0;
        $sValue  = ( string ) $dValue;
        $oStr = getStr();
        if ( ( $iDotPos = $oStr->strpos( $sValue, '.' ) ) !== false ) {
            $iDecPos = $oStr->strlen( $oStr->substr( $sValue, $iDotPos + 1 ) );
        }

        $oActCur = $oActCur ? $oActCur : $this->getConfig()->getActShopCurrencyObject();
        $iDecPos = ( $iDecPos < $oActCur->decimal ) ? $iDecPos : $oActCur->decimal;
        return number_format( (double)$dValue, $iDecPos, $oActCur->dec, $oActCur->thousand );
    }

    /**
     * According to user configuration forms and return language prefix.
     *
     * @param integer $iLanguage User selected language (default null)
     *
     * @return string
     */
    public function getLanguageTag( $iLanguage = null)
    {
        if ( !isset( $iLanguage ) ) {
            $iLanguage = $this->getBaseLanguage();
        }

        $iLanguage = (int) $iLanguage;

        return ( ( $iLanguage )?"_$iLanguage":"" );
    }

    /**
     * Validate language id. If not valid id, returns default value
     *
     * @param int $iLang Language id
     *
     * @return int
     */
    public function validateLanguage( $iLang = null )
    {
        $iLang = (int) $iLang;

        // checking if this language is valid
        $aLanguages = $this->getLanguageArray( null, !$this->isAdmin() );
        if ( !isset( $aLanguages[$iLang] ) && is_array( $aLanguages ) ) {
            $oLang = current( $aLanguages );
            if (isset($oLang->id)) {
                $iLang = $oLang->id;
            }
        }

        return $iLang;
    }

    /**
     * Set base shop language
     *
     * @param int $iLang Language id
     *
     * @return null
     */
    public function setBaseLanguage( $iLang = null )
    {
        if ( is_null($iLang) ) {
            $iLang = $this->getBaseLanguage();
        } else {
            $this->_iBaseLanguageId = (int) $iLang;
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            modSession::getInstance();
        }

        oxSession::setVar( 'language', $iLang );
    }

    /**
     * Validates and sets templates language id
     *
     * @param int $iLang Language id
     *
     * @return null
     */
    public function setTplLanguage( $iLang = null )
    {
        $this->_iTplLanguageId = isset( $iLang ) ? (int) $iLang : $this->getBaseLanguage();
        if ( $this->isAdmin() ) {
            $aLanguages = $this->getAdminTplLanguageArray();
            if ( !isset( $aLanguages[$this->_iTplLanguageId] ) ) {
                $this->_iTplLanguageId = key( $aLanguages );
            }
        }

        if ( defined( 'OXID_PHP_UNIT' ) ) {
            modSession::getInstance();
        }

        oxSession::setVar( 'tpllanguage', $this->_iTplLanguageId );
        return $this->_iTplLanguageId;
    }

    /**
     * Goes through language array and recodes its values. Returns recoded data
     *
     * @param array  $aLangArray   language data
     * @param string $sCharset     charset which was used while making file
     * @param bool   $blRecodeKeys leave keys untouched or recode it
     *
     * @return array
     */
    protected function _recodeLangArray( $aLangArray, $sCharset, $blRecodeKeys = false )
    {
        $aLangs = array();
        foreach ( $aLangArray as $sKey => $sValue ) {
            $sItemKey = $sKey;
            if ($blRecodeKeys === true) {
                $sItemKey = iconv($sCharset, 'UTF-8', $sItemKey);
            }

            $aLangs[$sItemKey] = iconv( $sCharset, 'UTF-8', $sValue );
            unset($aLangArray[$sKey]);
        }

        return $aLangs;
    }

    /**
     * Returns array with paths where language files are stored
     *
     * @param bool $blAdmin admin mode
     * @param int  $iLang   active language
     *
     * @return array
     */
    protected function _getLangFilesPathArray( $blAdmin, $iLang )
    {
        $myConfig = $this->getConfig();
        $aLangFiles = array();

        $sOutDir        = $myConfig->getOutDir();
        $sLang          = oxLang::getInstance()->getLanguageAbbr( $iLang );
        $sTheme         = $myConfig->getConfigParam( "sTheme" );
        $sCustomTheme   = $myConfig->getConfigParam( "sCustomTheme" );
        $sShopId        = $myConfig->getShopId();
        $aModuleInfo    = $this->_getActiveModuleInfo();

        if ( $blAdmin ) {
            // admin lang files
            $sAdminPath = $sOutDir . 'admin/' . $sLang . '/';
            $aLangFiles[] = $sAdminPath . "lang.php";
            $aTmpFiles = glob( $sAdminPath."*_lang.php" );
            if ( is_array( $aTmpFiles ) && count( $aTmpFiles ) ) {
                $aLangFiles = array_merge( $aLangFiles, $aTmpFiles);
            }

            // themes options lang files
            $sThemePath = $sOutDir . '*/' . $sLang . '/theme_options.php';
            $aTmpFiles = glob( $sThemePath );
            if ( is_array( $aTmpFiles ) && count( $aTmpFiles ) ) {
                $aLangFiles = array_merge( $aLangFiles, $aTmpFiles);
            }

            //load admin modules lang files
            if ( is_array( $aModuleInfo ) ) {
                foreach ( $aModuleInfo as $sPath ) {
                    $aModuleFiles = glob( $myConfig->getModulesDir().$sPath.'/out/admin/' . $sLang . '/*_lang.php' );
                    if ( is_array( $aModuleFiles ) && count( $aModuleFiles ) ) {
                        $aLangFiles = array_merge( $aLangFiles, $aModuleFiles );
                    }

                    //load admin modules options lang files
                    $aLangFiles[] = $myConfig->getModulesDir().$sPath.'/out/admin/' . $sLang . '/module_options.php';
                }
            }

            return count( $aLangFiles ) ? $aLangFiles : false;
        }

        //get generic lang files
        $sGenericPath = $sOutDir . $sLang . '/';
        if ( $sGenericPath ) {
            $aLangFiles[] = $sGenericPath . "lang.php";
            $aTmpFiles = glob( $sGenericPath."*_lang.php" );
            if ( is_array($aTmpFiles ) && count( $aTmpFiles ) ) {
                $aLangFiles = array_merge( $aLangFiles, $aTmpFiles );
            }
        }

        //get theme lang files
        if ( $sTheme ) {
            $sThemePath = $sOutDir . $sTheme .'/' . $sLang . '/';
            $aLangFiles[] = $sThemePath . "lang.php";
            $aTmpFiles = glob( $sThemePath."*_lang.php" );
            if ( is_array( $aTmpFiles) && count( $aTmpFiles ) ) {
                $aLangFiles = array_merge( $aLangFiles, $aTmpFiles );
            }
        }

        //get custom theme lang files
        if ( $sCustomTheme ) {
            $sCustPath = $sOutDir . $sCustomTheme .'/' . $sLang . '/';
            $aLangFiles[] = $sCustPath . "lang.php";
            $aTmpFiles = glob( $sCustPath."*_lang.php" );
            if ( is_array( $aTmpFiles ) && count( $aTmpFiles ) ) {
                $aLangFiles = array_merge( $aLangFiles, $aTmpFiles );
            }

            // custom theme shop languages
            if ( $sCustomTheme ) {
                $sShopPath = $sOutDir . $sCustomTheme .'/' . $sShopId . '/' . $sLang . '/';
                $aLangFiles[] = $sShopPath . "lang.php";
                $aTmpFiles = glob( $sShopPath."*_lang.php" );
                if ( is_array( $aTmpFiles ) && count( $aTmpFiles ) ) {
                    $aLangFiles = array_merge( $aLangFiles, $aTmpFiles );
                }
            }
        } else {
            // theme shop languages
            if ( $sTheme ) {
                $sShopPath = $sOutDir . $sTheme .'/' . $sShopId . '/' . $sLang . '/';
                $aLangFiles[] = $sShopPath . "lang.php";
                $aTmpFiles = glob( $sShopPath."*_lang.php" );
                if ( is_array( $aTmpFiles ) && count( $aTmpFiles ) ) {
                    $aLangFiles = array_merge( $aLangFiles, $aTmpFiles );
                }
            }
        }

        //modules language files
        if ( is_array( $aModuleInfo ) ) {
            foreach ( $aModuleInfo as $sPath ) {
                $aModuleFiles = glob( $myConfig->getModulesDir() . $sPath . '/out/lang/' . $sLang . '/*_lang.php');
                if (is_array($aModuleFiles) && count($aModuleFiles)) {
                    $aLangFiles = array_merge( $aLangFiles, $aModuleFiles );
                }
            }
        }

        return count( $aLangFiles ) ? $aLangFiles : false;
    }

    /**
     * Returns language cache file name
     *
     * @param bool  $blAdmin    admin or not
     * @param int   $iLang      current language id
     * @param array $aLangFiles language files to load [optional]
     *
     * @return string
     */
    protected function _getLangFileCacheName( $blAdmin, $iLang, $aLangFiles = null )
    {
        $myConfig = $this->getConfig();
        $sLangFilesIdent = '_default';
        if (is_array($aLangFiles) && $aLangFiles) {
            $sLangFilesIdent = '_'.md5(implode('+', $aLangFiles));
        }
        return "langcache_" . ( (int) $blAdmin ) . "_{$iLang}_" . $myConfig->getShopId() . "_" . $myConfig->getConfigParam( 'sTheme' ).$sLangFilesIdent;
    }

    /**
     * Returns language cache array
     *
     * @param bool  $blAdmin    admin or not [optional]
     * @param int   $iLang      current language id [optional]
     * @param array $aLangFiles language files to load [optional]
     *
     * @return array
     */
    protected function _getLanguageFileData( $blAdmin = false, $iLang = 0, $aLangFiles = null )
    {
        $myConfig = $this->getConfig();
        $myUtils  = oxUtils::getInstance();

        $sCacheName = $this->_getLangFileCacheName( $blAdmin, $iLang, $aLangFiles );
        $aLangCache = $myUtils->getLangCache( $sCacheName );
        if ( !$aLangCache && $aLangFiles === null ) {
            $aLangFiles = $this->_getLangFilesPathArray( $blAdmin, $iLang );
        }
        if ( !$aLangCache && $aLangFiles ) {
            $aLangCache = array();
            $sBaseCharset = false;
            $aLang = array();
            $aLangSeoReplaceChars = array();
            foreach ( $aLangFiles as $sLangFile ) {

                if ( file_exists( $sLangFile ) && is_readable( $sLangFile ) ) {
                    $aSeoReplaceChars = array();
                    include $sLangFile;

                    // including only (!) thoose, which has charset defined
                    if ( isset( $aLang['charset'] ) ) {

                        // recoding only in utf
                        if ( $myConfig->isUtf() ) {
                            $aLang = $this->_recodeLangArray( $aLang, $aLang['charset'] );

                            if (isset($aSeoReplaceChars) && is_array($aSeoReplaceChars)) {
                                $aSeoReplaceChars = $this->_recodeLangArray( $aSeoReplaceChars, $aLang['charset'], true );
                            }

                            // overriding charset
                            $aLang['charset'] = 'UTF-8';
                        }

                        if (isset($aSeoReplaceChars) && is_array($aSeoReplaceChars)) {
                            $aLangSeoReplaceChars = array_merge($aLangSeoReplaceChars, $aSeoReplaceChars);
                        }

                        if ( !$sBaseCharset ) {
                            $sBaseCharset = $aLang['charset'];
                        }

                        $aLangCache = array_merge( $aLangCache, $aLang );
                    }
                }
            }

            // setting base charset
            if ( $sBaseCharset ) {
                $aLangCache['charset'] = $sBaseCharset;
            }

            // special character replacement list
            $aLangCache['_aSeoReplaceChars'] = $aLangSeoReplaceChars;

            //save to cache
            $myUtils->setLangCache( $sCacheName, $aLangCache );
        }

        return $aLangCache;
    }

    /**
     * Returns language map array
     *
     * @param int  $iLang   language index
     * @param bool $blAdmin admin mode [default NULL]
     *
     * @return array
     */
    protected function _getLanguageMap( $iLang, $blAdmin = null )
    {
        $blAdmin = isset( $blAdmin ) ? $blAdmin : $this->isAdmin();
        $sKey = $iLang . ( (int) $blAdmin );
        if ( !isset( $this->_aLangMap[$sKey] ) ) {
            $this->_aLangMap[$sKey] = array();
            $myConfig = $this->getConfig();
            $sMapFile = $myConfig->getOutDir() . '/' .  ( $blAdmin ? 'admin' : $myConfig->getConfigParam( "sTheme" ) ) .'/' . oxLang::getInstance()->getLanguageAbbr( $iLang ) . '/map.php';
            if ( $sMapFile ) {
                if ( file_exists( $sMapFile ) && is_readable( $sMapFile ) ) {
                    include $sMapFile;
                    $this->_aLangMap[$sKey] = $aMap;
                }
            }
        }

        return $this->_aLangMap[$sKey];
    }

    /**
     * Returns current language cache language id
     *
     * @param bool $blAdmin admin mode
     * @param int  $iLang   language id [optional]
     *
     * @return int
     */
    protected function _getCacheLanguageId( $blAdmin, $iLang = null )
    {
        $iLang = ( $iLang === null && $blAdmin ) ? $this->getTplLanguage() : $iLang;
        if ( !isset( $iLang ) ) {
            $iLang = $this->getBaseLanguage();
            if ( !isset( $iLang ) ) {
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
     */
    protected function _getLangTranslationArray( $iLang = null, $blAdmin = null, $aLangFiles = null )
    {
        startProfile("_getLangTranslationArray");

        $blAdmin    = isset( $blAdmin ) ? $blAdmin : $this->isAdmin();
        $iLang      = $this->_getCacheLanguageId( $blAdmin, $iLang );
        $sCacheName = $this->_getLangFileCacheName( $blAdmin, $iLang, $aLangFiles );

        if ( !isset( $this->_aLangCache[$sCacheName] ) ) {
            $this->_aLangCache[$sCacheName] = array();
        }
        if ( !isset( $this->_aLangCache[$sCacheName][$iLang] ) ) {
            // loading main lang files data
            $this->_aLangCache[$sCacheName][$iLang] = $this->_getLanguageFileData( $blAdmin, $iLang, $aLangFiles );
        }

        stopProfile("_getLangTranslationArray");

        // if language array exists ..
        return ( isset( $this->_aLangCache[$sCacheName][$iLang] ) ? $this->_aLangCache[$sCacheName][$iLang] : array() );
    }

    /**
     * translates a given string
     *
     * @param string $sStringToTranslate string that should be translated
     * @param int    $iLang              language id (optional)
     * @param bool   $blIsAdmin          admin mode switch (default null)
     *
     * @deprecated, text files should not be used any more (2011.07.06)
     *
     * @return string translation
     */
    protected function _readTranslateStrFromTextFile( $sStringToTranslate, $iLang = null, $blIsAdmin = null )
    {
        $blIsAdmin = isset( $blIsAdmin ) ? $blIsAdmin : $this->isAdmin();
        $iLang  = ( $iLang === null && $blIsAdmin)?$this->getTplLanguage():$iLang;
        if ( !isset( $iLang ) ) {
            $iLang = (int) $this->getBaseLanguage();
        }

        $sFileName = $this->getConfig()->getLanguagePath('lang.txt', $blIsAdmin, $iLang);
        if ( is_file ( $sFileName ) && is_readable( $sFileName ) ) {

            static $aUserLangCache = array();

            if ( !isset( $aUserLangCache[$sFileName] ) ) {
                $handle = @fopen( $sFileName, "r" );
                if ( $handle === false ) {
                    return $sStringToTranslate;
                }

                $contents = fread( $handle, filesize ( $sFileName ) );
                fclose( $handle );
                $fileArray = explode( "\n", $contents );
                $aUserLangCache[$sFileName] = array();
                $aLang = &$aUserLangCache[$sFileName];
                $oStr = getStr();

                while ( list( $nr,$line ) = each( $fileArray ) ) {
                    $line = ltrim( $line );
                    if ( $line[0]!="#" && $oStr->strpos( $line, "=" ) > 0 ) {
                        $index = trim( $oStr->substr( $line, 0, $oStr->strpos($line, "=" ) ) );
                        $value = trim( $oStr->substr( $line, $oStr->strpos( $line, "=" ) + 1, $oStr->strlen( $line ) ) );
                        $aLang[trim($index)] = trim($value);
                    }
                }
            }

            if ( !isset( $aLang ) && isset( $aUserLangCache[$sFileName] ) ) {
                $aLang = &$aUserLangCache[$sFileName];
            }

            if ( isset( $aLang[$sStringToTranslate] ) ) {
                $sStringToTranslate = $aLang[$sStringToTranslate];
            }
        }

        return $sStringToTranslate;
    }

    /**
     * Language sorting callback function
     *
     * @param object $a1 first value to check
     * @param object $a2 second value to check
     *
     * @return bool
     */
    protected function _sortLanguagesCallback( $a1, $a2 )
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
        $sLang = null;
        if ( !$this->isAdmin()) {
            $sLang = "<input type=\"hidden\" name=\"".$this->getName()."\" value=\"". $this->getBaseLanguage() . "\">";
        }
        return $sLang;
    }

    /**
     * Returns url language parameter
     *
     * @param int $iLang lanugage id [optional]
     *
     * @return string
     */
    public function getUrlLang( $iLang = null )
    {
        $sLang = null;
        if ( !$this->isAdmin()) {
            $iLang = isset( $iLang ) ? $iLang : $this->getBaseLanguage();
            $sLang = $this->getName()."=". $iLang;
        }
        return $sLang;
    }

    /**
     * Is needed appends url with language parameter
     * Direct usage of this method to retrieve end url result is discouraged - instead
     * see oxUtilsUrl::processUrl
     *
     * @param string $sUrl  url to process
     * @param int    $iLang language id [optional]
     *
     * @see oxUtilsUrl::processUrl
     *
     * @return string
     */
    public function processUrl( $sUrl, $iLang = null )
    {
        $iLang = isset( $iLang ) ? $iLang : $this->getBaseLanguage();
        $oStr = getStr();

        if ( !$this->isAdmin() ) {
            $sParam = $this->getUrlLang( $iLang );
            if ( !$oStr->preg_match('/(\?|&(amp;)?)lang=[0-9]+/', $sUrl)  && ($iLang != oxConfig::getInstance()->getConfigParam( 'sDefaultLang' ))) {
                if ( $sUrl ) {
                    if ($oStr->strpos( $sUrl, '?') === false) {
                        $sUrl .= "?";
                    } elseif ( !$oStr->preg_match('/(\?|&(amp;)?)$/', $sUrl ) ) {
                        $sUrl .= "&amp;";
                    }
                }
                $sUrl .= $sParam."&amp;";
            } else {
                $sUrl = getStr()->preg_replace('/(\?|&(amp;)?)lang=[0-9]+/', '\1'.$sParam, $sUrl);
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
        $sBrowserLang = strtolower( substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) );

        if ( !$sBrowserLang ) {
            return;
        }

        $aLangs = $this->getLanguageArray(null, true );

        foreach ( $aLangs as $oLang ) {
            if ( $oLang->abbr == $sBrowserLang ) {
                return (int) $oLang->id;
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
        $aTables = array( "oxarticles", "oxartextends", "oxattribute",
                          "oxcategories", "oxcontents", "oxcountry",
                          "oxdelivery", "oxdiscount", "oxgroups",
                          "oxlinks", "oxnews", "oxobject2attribute",
                          "oxpayments", "oxselectlist", "oxshops",
                          "oxactions", "oxwrapping", "oxdeliveryset",
                          "oxvendor", "oxmanufacturers", "oxmediaurls",
                          "oxstates" );


        $aMultiLangTables = $this->getConfig()->getConfigParam( 'aMultiLangTables' );

        if ( is_array( $aMultiLangTables ) ) {
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
        // compatibility: check for global settings
        $aGlobalSeoReplaceChars = $this->getConfig()->getConfigParam('aSeoReplaceChars');
        if (!is_array($aGlobalSeoReplaceChars)) {
            $aGlobalSeoReplaceChars = array();
        }

        // get language replace chars
        $aSeoReplaceChars = $this->translateString('_aSeoReplaceChars', $iLang);
        if (!is_array($aSeoReplaceChars)) {
            $aSeoReplaceChars = array();
        }

        $aSeoReplaceChars = array_merge($aGlobalSeoReplaceChars, $aSeoReplaceChars);
        return $aSeoReplaceChars;
    }

    /**
     * Returns active module Ids
     *
     * @return array
     */
    protected function _getActiveModuleInfo()
    {
        if ($this->_aActiveModuleInfo === null) {
            $oModulelist = oxNew('oxmodulelist');
            $this->_aActiveModuleInfo = $oModulelist->getActiveModuleInfo();
        }
        return $this->_aActiveModuleInfo;
    }

}
