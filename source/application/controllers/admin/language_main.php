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
 * Admin article main selectlist manager.
 * Performs collection and updatind (on user submit) main item information.
 * @package admin
 */
class Language_Main extends oxAdminDetails
{

    /**
     * Current shop base languages
     *
     * @var arrray
     */
    protected $_aLangData = null;

    /**
     * Current shop base languages parameters
     *
     * @var array
     */
    protected $_aLangParams = null;

    /**
     * Current shop base languages base urls
     *
     * @var array
     */
    protected $_aLanguagesUrls = null;

    /**
     * Current shop base languages base ssl urls
     *
     * @var array
     */
    protected $_aLanguagesSslUrls = null;

    /**
     * Executes parent method parent::render(), creates oxCategoryList object,
     * passes it's data to Smarty engine and returns name of template file
     * "selectlist_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();


        parent::render();

        $sOxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        //loading languages info from config
        $this->_aLangData = $this->_getLanguages();

        if ( $sOxId != -1 ) {
            //checking if translations files exists
            $this->_checkLangTranslations( $sOxId );
        }

        if ( $sOxId != "-1" && isset( $sOxId)) {
            $this->_aViewData["edit"] =  $this->_getLanguageInfo( $sOxId );
        }

        return "language_main.tpl";
    }

    /**
     * Saves selection list parameters changes.
     *
     * @return mixed
     */
    public function save()
    {
        $myConfig  = $this->getConfig();


        parent::save();

        $sOxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval" );

        if ( !isset( $aParams['active'])) {
            $aParams['active'] = 0;
        }

        if ( !isset( $aParams['default'])) {
            $aParams['default'] = false;
        }

        if ( empty( $aParams['sort'])) {
            $aParams['sort'] = '99999';
        }

        //loading languages info from config
        $this->_aLangData = $this->_getLanguages();
        //checking input errors
        if ( !$this->_validateInput() ) {
            return;
        }

        $blViewError = false;

        // if changed language abbervation, updating it for all arrays related with languages
        if ( $sOxId != -1 && $sOxId != $aParams['abbr'] ) {
            // #0004850 preventing changing abbr for main language with base id = 0
            if ( (int) $this->_aLangData['params'][$sOxId]['baseId'] == 0 ) {
                $oEx = oxNew( "oxExceptionToDisplay" );
                $oEx->setMessage( 'LANGUAGE_ABBRCHANGEMAINLANG_WARNING' );
                oxUtilsView::getInstance()->addErrorToDisplay( $oEx );
                $aParams['abbr'] = $sOxId;
            } else {
                $this->_updateAbbervation( $sOxId, $aParams['abbr'] );
                $sOxId = $aParams['abbr'];
                $this->setEditObjectId( $sOxId );

                $blViewError = true;
            }
        }

        // if adding new language, setting lang id to abbervation
        if ( $blNewLanguage = ($sOxId == -1) ) {
            $sOxId = $aParams['abbr'];
            $this->_aLangData['params'][$sOxId]['baseId'] = $this->_getAvailableLangBaseId();
            $this->setEditObjectId( $sOxId );
        }

        //updating language description
        $this->_aLangData['lang'][$sOxId]  = $aParams['desc'];

        //updating language parameters
        $this->_aLangData['params'][$sOxId]['active']  = $aParams['active'];
        $this->_aLangData['params'][$sOxId]['default'] = $aParams['default'];
        $this->_aLangData['params'][$sOxId]['sort']   = $aParams['sort'];

        //if setting lang as default
        if ( $aParams['default'] == '1' ) {
            $this->_setDefaultLang( $sOxId );
        }

        //updating language urls
        $iBaseId = $this->_aLangData['params'][$sOxId]['baseId'];
        $this->_aLangData['urls'][$iBaseId] = $aParams['baseurl'];
        $this->_aLangData['sslUrls'][$iBaseId] = $aParams['basesslurl'];

        //sort parameters, urls and languages arrays by language base id
        $this->_sortLangArraysByBaseId();

        $this->_aViewData["updatelist"] = "1";

        //saving languages info
        $this->getConfig()->saveShopConfVar( 'aarr', 'aLanguageParams', $this->_aLangData['params'] );
        $this->getConfig()->saveShopConfVar( 'aarr', 'aLanguages', $this->_aLangData['lang'] );
        $this->getConfig()->saveShopConfVar( 'arr', 'aLanguageURLs', $this->_aLangData['urls'] );
        $this->getConfig()->saveShopConfVar( 'arr', 'aLanguageSSLURLs', $this->_aLangData['sslUrls'] );

        //checking if added language already has created multilang fields
        //with new base ID - if not, creating new fields
        if ($blNewLanguage) {
            if (!$this->_checkMultilangFieldsExistsInDb( $sOxId ) ) {
                $this->_addNewMultilangFieldsToDb();
            } else {
                $blViewError = true;
            }
        }

        // show message for user to generate views
        if ($blViewError) {
            $oEx = oxNew( 'oxExceptionToDisplay' );
            $oEx->setMessage( 'LANGUAGE_ERRORGENERATEVIEWS' );
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
        }
    }

    /**
     * Get selected language info
     *
     * @param string $sOxId language abbervation
     *
     * @return array
     */
    protected function _getLanguageInfo( $sOxId )
    {
        $sDefaultLang = $this->getConfig()->getConfigParam( 'sDefaultLang' );

        $aLangData               = $this->_aLangData['params'][$sOxId];
        $aLangData['abbr']       = $sOxId;
        $aLangData['desc']       = $this->_aLangData['lang'][$sOxId];
        $aLangData['baseurl']    = $this->_aLangData['urls'][$aLangData['baseId']];
        $aLangData['basesslurl'] = $this->_aLangData['sslUrls'][$aLangData['baseId']];
        $aLangData['default']    = ($this->_aLangData['params'][$sOxId]["baseId"] == $sDefaultLang) ? true : false;

        return $aLangData;
    }

    /**
     * Languages array setter
     *
     * @param array $aLangData languages parameters array
     *
     * @return array
     */
    protected function _setLanguages( $aLangData )
    {
        $this->_aLangData = $aLangData;
    }

    /**
     * Loads from config all data related with languages.
     * If no languages parameters array exists, sets default parameters values.
     * Returns collected languages parameters array.
     *
     * @return array
     */
    protected function _getLanguages()
    {
        $aLangData['params']  = $this->getConfig()->getConfigParam( 'aLanguageParams' );
        $aLangData['lang']    = $this->getConfig()->getConfigParam( 'aLanguages' );
        $aLangData['urls']    = $this->getConfig()->getConfigParam( 'aLanguageURLs' );
        $aLangData['sslUrls'] = $this->getConfig()->getConfigParam( 'aLanguageSSLURLs' );

        // empty languages parameters array - creating new one with default values
        if ( !is_array( $aLangData['params']) ) {
            $aLangData['params'] = $this->_assignDefaultLangParams( $aLangData['lang'] );
        }

        return $aLangData;
    }

    /**
     * Replaces languages arrays keys by new value.
     *
     * @param string $sOldId old ID
     * @param string $sNewId new ID
     *
     * @return null
     */
    protected function _updateAbbervation( $sOldId, $sNewId )
    {
        foreach ( array_keys($this->_aLangData) as $sTypeKey ) {

            if ( is_array($this->_aLangData[$sTypeKey]) && count($this->_aLangData[$sTypeKey]) > 0 ) {

                if ( $sTypeKey == 'urls' || $sTypeKey == 'sslUrls' ) {
                    continue;
                }

                $aKeys   = array_keys( $this->_aLangData[$sTypeKey] );
                $aValues = array_values( $this->_aLangData[$sTypeKey] );
                //find and replace key
                $iReplaceId = array_search( $sOldId, $aKeys );
                $aKeys[$iReplaceId] = $sNewId;

                $this->_aLangData[$sTypeKey] = array_combine( $aKeys, $aValues );
            }
        }
    }

    /**
     * Sort languages, languages parameters, urls, ssl urls arrays according
     * base land ID
     *
     * @return null
     */
    protected function _sortLangArraysByBaseId()
    {
        $aUrls      = array();
        $aSslUrls   = array();
        $aLanguages = array();

        uasort( $this->_aLangData['params'], array($this, '_sortLangParamsByBaseIdCallback') );

        foreach ( $this->_aLangData['params'] as  $sAbbr => $aParams ) {
            $iId = (int)$aParams['baseId'];
            $aUrls[$iId]        = $this->_aLangData['urls'][$iId];
            $aSslUrls[$iId]     = $this->_aLangData['sslUrls'][$iId];
            $aLanguages[$sAbbr] = $this->_aLangData['lang'][$sAbbr];
        }

        $this->_aLangData['lang']    = $aLanguages;
        $this->_aLangData['urls']    = $aUrls;
        $this->_aLangData['sslUrls'] = $aSslUrls;
    }

    /**
     * Assign default values for eache language
     *
     * @param array $aLanguages language array
     *
     * @return array
     */
    protected function _assignDefaultLangParams( $aLanguages )
    {
        $aParams = array();
        $iBaseId = 0;

        foreach ( array_keys($aLanguages) as $sOxId ) {
            $aParams[$sOxId]['baseId']  = $iBaseId;
            $aParams[$sOxId]['active']  = 1;
            $aParams[$sOxId]['sort']   = $iBaseId + 1;

            $iBaseId++;
        }

        return $aParams;
    }

    /**
     * Sets default language base ID to config var 'sDefaultLang'
     *
     * @param string $sOxId language abbervation
     *
     * @return null
     */
    protected function _setDefaultLang( $sOxId )
    {
        $sDefaultId = $this->_aLangData['params'][$sOxId]['baseId'];
        $this->getConfig()->saveShopConfVar( 'str', 'sDefaultLang', $sDefaultId );
    }

    /**
     * Get availabale language base ID
     *
     * @return int
     */
    protected function _getAvailableLangBaseId()
    {
        $aBaseId = array();
        foreach ( $this->_aLangData['params'] as $aLang ) {
            $aBaseId[] = $aLang['baseId'];
        }

        $iNewId = 0;
        sort( $aBaseId );
        $iTotal = count($aBaseId);

        //getting first available id
        while ( $iNewId <= $iTotal ) {
            if ( $iNewId !== $aBaseId[$iNewId] ) {
                break;
            }
            $iNewId++;
        }

        return $iNewId;
    }

    /**
     * Check selected language has translation file lang.php
     * If not - displays warning
     *
     * @param string $sOxId language abbervation
     *
     * @return null
     */
    protected function _checkLangTranslations( $sOxId )
    {
        $myConfig = $this->getConfig();

        $sDir = dirname( $myConfig->getTranslationsDir( 'lang.php', $sOxId ) );

        if ( empty($sDir) ) {
            $oEx = oxNew( "oxExceptionToDisplay" );
            $oEx->setMessage( 'LANGUAGE_NOTRANSLATIONS_WARNING' );
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
        }
    }

    /**
     * Check if selected language already has multilanguage fields in DB
     *
     * @param string $sOxId language abbervation
     *
     * @return bool
     */
    protected function _checkMultilangFieldsExistsInDb( $sOxId )
    {
        $iBaseId = $this->_aLangData['params'][$sOxId]['baseId'];
        $sTable  = getLangTableName('oxarticles', $iBaseId );
        $sColumn = 'oxtitle' . oxRegistry::getLang()->getLanguageTag( $iBaseId );

        $oDbMetadata = oxNew('oxDbMetaDataHandler');
        return $oDbMetadata->tableExists( $sTable ) && $oDbMetadata->fieldExists( $sColumn, $sTable );
    }

    /**
     * Adding new language to DB - creating new multilangue fields with new
     * language ID (e.g. oxtitle_4)
     *
     * @return null
     */
    protected function _addNewMultilangFieldsToDb()
    {
        //creating new multilanguage fields with new id over whole DB
        oxDb::getDb()->startTransaction();

        $oDbMeta = oxNew( "oxDbMetaDataHandler" );

        try {
             $oDbMeta->addNewLangToDb();
        } catch( Exception $oEx ) {
             // if exception, rollBack everything
             oxDb::getDb()->rollbackTransaction();

             //show warning
             echo $oEx->getMessage();
             $oEx = oxNew( "oxExceptionToDisplay" );
             $oEx->setMessage( 'LANGUAGE_ERROR_ADDING_MULTILANG_FIELDS' );
             oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );

             return;
        }

        oxDb::getDb()->commitTransaction();
    }

    /**
     * Check if language already exists
     *
     * @param string $sAbbr language abbervation
     *
     * @return bool
     */
    protected function _checkLangExists( $sAbbr )
    {
        $myConfig = $this->getConfig();
        $aAbbrs = array_keys($this->_aLangData['lang']);

        if ( in_array( $sAbbr, $aAbbrs ) ) {
            return true;
        }

        return false;
    }

    /**
     * Callback function for sorting languages arraty. Sorts array according
     * 'baseId' parameter
     *
     * @param object $oLang1 language array
     * @param object $oLang2 language array
     *
     * @return bool
     */
    protected function _sortLangParamsByBaseIdCallback( $oLang1, $oLang2 )
    {
        return ($oLang1['baseId'] < $oLang2['baseId']) ? -1 : 1;
    }

    /**
     * Check language input errors
     *
     * @return bool
     */
    protected function _validateInput()
    {
        $blResult = true;

        $sOxId = $this->getEditObjectId();
        $aParams = oxConfig::getParameter( "editval" );

        // if creating new language, checking if language already exists with
        // entered language abbervation
        if ( $sOxId == -1 ) {
            if ( $this->_checkLangExists( $aParams['abbr'] ) ) {
                $oEx = oxNew( 'oxExceptionToDisplay' );
                $oEx->setMessage( 'LANGUAGE_ALREADYEXISTS_ERROR' );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
                $blResult = false;
            }
        }

        // checking if language name is not empty
        if ( empty($aParams['desc']) ) {
            $oEx = oxNew( 'oxExceptionToDisplay' );
            $oEx->setMessage( 'LANGUAGE_EMPTYLANGUAGENAME_ERROR' );
            oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx );
            $blResult = false;
        }

        return $blResult;
    }
} 