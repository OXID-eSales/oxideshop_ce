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
 * Including AJAX wrapper class
 */
//require_once "oxajax.php";

/**
 * Admin selectlist list manager.
 * @package admin
 */
class oxAdminDetails extends oxAdminView
{
    /**
     * Global editor object
     *
     * @var object
     */
    protected $_oEditor = null;

    /**
     * Calls parent::render, sets admin help url
     *
     * @return string
     */
    public function render()
    {
        $sReturn = parent::render();
        $oLang = oxRegistry::getLang();

        // generate help link
        $myConfig = $this->getConfig();
        $sDir = $myConfig->getConfigParam( 'sShopDir' ) . '/documentation/admin';
        $iLang = 1;
        $sAbbr = $oLang->getLanguageAbbr($oLang->getTplLanguage());
        if ( $sAbbr == "de" ) {
            $iLang = 0;
        }
        if ( is_dir( $sDir ) ) {
            $sDir = $myConfig->getConfigParam( 'sShopURL' ) . 'documentation/admin';
        } else {

                $oShop = $this->_getEditShop( oxSession::getVar( 'actshop' ) );
                //$sDir = "http://docu.oxid-esales.com/PE/{$oShop->oxshops__oxversion->value}/" . $myConfig->getConfigParam( 'iAdminLanguage' ) . '/admin';
                $sDir = "http://docu.oxid-esales.com/PE/{$oShop->oxshops__oxversion->value}/" . $iLang . '/admin';
        }

        $this->_aViewData['sHelpURL'] = $sDir;

        return $sReturn;
    }

    /**
     * Initiates Text editor
     *
     * @param int    $iWidth      editor width
     * @param int    $iHeight     editor height
     * @param object $oObject     object passed to editor
     * @param string $sField      object field which content is passed to editor
     * @param string $sStylesheet stylesheet to use in editor
     *
     * @return wysiwygPro
     */
    protected function _getTextEditor( $iWidth, $iHeight, $oObject, $sField, $sStylesheet = null )
    {
        if ( $this->_oEditor === null ) {
            $myConfig = $this->getConfig();

            // include the config file and editor class:
            $sEditorPath = 'wysiwigpro';
            $sEditorFile = getShopBasePath()."core/".$sEditorPath . '/wysiwygPro.class.php';


            // setting loaded state
            $this->_oEditor = false;

            if ( $sEditorFile && file_exists( $sEditorFile ) ) {
                include_once $sEditorFile;

                // create a new instance of the wysiwygPro class:
                $this->_oEditor = new wysiwygPro();

                // set language file name
                $sEditorUrl = oxConfig::getInstance()->getConfigParam('sShopURL')."core/{$sEditorPath}/";
                if ( $sAdminSSLURL = $myConfig->getConfigParam( 'sAdminSSLURL' ) ) {
                    $sEditorUrl = "{$sAdminSSLURL}/{$sEditorPath}/";
                }

                $this->_oEditor->editorURL = $sEditorUrl;
                $this->_oEditor->urlFormat = 'preserve';

                // document & image directory:
                $this->_oEditor->documentDir = $this->_oEditor->imageDir = $myConfig->getPictureDir( false ).'wysiwigpro/';
                $this->_oEditor->documentURL = $this->_oEditor->imageURL = $myConfig->getPictureUrl( null, false ).'wysiwigpro/';

                // enabling upload
                $this->_oEditor->upload = true;

                // setting empty value
                $this->_oEditor->emptyValue = "<p>&nbsp;</p>";

                //#M432 enabling deleting files and folders
                $this->_oEditor->deleteFiles = true;
                $this->_oEditor->deleteFolders = true;

                // allowed image extensions
                $this->_oEditor->allowedImageExtensions = '.jpg, .jpeg, .gif, .png';

                // allowed document extensions
                $this->_oEditor->allowedDocExtensions   = '.html, .htm, .pdf, .doc, .rtf, .txt, .xl, .xls, .ppt, .pps, .zip, .tar, .swf, .wmv, .rm, .mov, .jpg, .jpeg, .gif, .png';

                // set name
                $this->_oEditor->name = $sField;

                // set language file name
                $oLang = oxRegistry::getLang();
                $this->_oEditor->lang = $oLang->translateString( 'editor_language', $oLang->getTplLanguage() );

                // set contents
                if ( $sEditObjectValue = $this->_getEditValue( $oObject, $sField ) ) {
                    $this->_oEditor->value = $sEditObjectValue;
                    $this->_oEditor->encoding = $this->getConfig()->isUtf() ? 'UTF-8': 'ISO-8859-15';
                }

                // parse for styles and add them
                $this->setAdminMode( false );
                $sCSSPath = $myConfig->getResourcePath("{$sStylesheet}", false );
                $sCSSUrl  = $myConfig->getResourceUrl("{$sStylesheet}", false );

                $aCSSPaths = array();
                $this->setAdminMode( true );

                if (is_file($sCSSPath)) {

                    $aCSSPaths[] = $sCSSUrl;

                    if (is_readable($sCSSPath)) {
                        $aCSS = @file( $sCSSPath);
                        if ( isset( $aCSS) && $aCSS) {
                            $aClasses = array();
                            $oStr = getStr();
                            foreach ( $aCSS as $key => $sLine ) {
                                $sLine = trim($sLine);

                                if ( $sLine[0] == '.' && !$oStr->strstr( $sLine, 'default' ) ) {
                                    // found one tag
                                    $sTag = $oStr->substr( $sLine, 1);
                                    $iEnd = $oStr->strpos( $sTag, ' ' );
                                    if ( !isset( $iEnd ) || !$iEnd ) {
                                        $iEnd = $oStr->strpos( $sTag, '\n' );
                                    }

                                    if ( $sTag = $oStr->substr( $sTag, 0, $iEnd ) ) {
                                        $aClasses["span class='{$sTag}'"] = $sTag;
                                    }
                                }
                            }
                            $this->_oEditor->stylesMenu = $aClasses;
                        }
                    }
                }

                foreach ( $aCSSPaths as $sCssPath ) {
                    $this->_oEditor->addStylesheet( $sCssPath );
                }

                //while there is a bug in editor template filter we cannot use this feature
                // loading template filter plugin
                $this->_oEditor->loadPlugin( 'templateFilter' );
                $this->_oEditor->plugins['templateFilter']->protect( '[{', '}]' );
                if ( $myConfig->getConfigParam( 'bl_perfParseLongDescinSmarty' ) ) {
                    $this->_oEditor->plugins['templateFilter']->assign( '[{$oViewConf->getCurrentHomeDir()}]', $myConfig->getShopURL() );
                    // note: in "[{ $" the space is needed for this parameter not to override previous call. see assign fnc of templateFilter
                    $this->_oEditor->plugins['templateFilter']->assign( '[{ $oViewConf->getCurrentHomeDir()}]', $myConfig->getSSLShopURL() );
                }
            }

            return $this->_oEditor;
        }
    }

    /**
     * Returns string which must be edited by editor
     *
     * @param oxbase $oObject object whifh field will be used for editing
     * @param string $sField  name of editable field
     *
     * @return string
     */
    protected function _getEditValue( $oObject, $sField )
    {
        $sEditObjectValue = '';
        if ( $oObject && $sField && isset( $oObject->$sField ) ) {

            if ( $oObject->$sField instanceof oxField ) {
                $sEditObjectValue = $oObject->$sField->getRawValue();
            } else {
                $sEditObjectValue = $oObject->$sField->value;
            }

            $sEditObjectValue = $this->_processEditValue( $sEditObjectValue );
            $oObject->$sField = new oxField( $sEditObjectValue, oxField::T_RAW );
        }

        return $sEditObjectValue;
    }

    /**
     * Processes edit value
     *
     * @param string $sValue string to process
     *
     * @return string
     */
    protected function _processEditValue( $sValue )
    {
        // A. replace ONLY if long description is not processed by smarty, or users will not be able to
        // store smarty tags ([{$shop->currenthomedir}]/[{$oViewConf->getCurrentHomeDir()}]) in long
        // descriptions, which are filled dynamically
        if ( !$this->getConfig()->getConfigParam( 'bl_perfParseLongDescinSmarty' ) ) {
            $aReplace = array( '[{$shop->currenthomedir}]', '[{$oViewConf->getCurrentHomeDir()}]' );
            $sValue = str_replace( $aReplace, $this->getConfig()->getCurrentShopURL(false), $sValue );
        }
        return $sValue;
    }

    /**
     * Returns textarea filled with text to edit
     *
     * @param int    $iWidth  editor width
     * @param int    $iHeight editor height
     * @param object $oObject object passed to editor
     * @param string $sField  object field which content is passed to editor
     *
     * @return string
     */
    protected function _getPlainEditor( $iWidth, $iHeight, $oObject, $sField )
    {
        $sEditObjectValue = $this->_getEditValue( $oObject, $sField );

        if ( strpos( $iWidth, '%' ) === false ) {
            $iWidth .= 'px';
        }
        if ( strpos( $iHeight, '%' ) === false ) {
            $iHeight .= 'px';
        }
        return "<textarea id='editor_{$sField}' style='width:{$iWidth}; height:{$iHeight};'>{$sEditObjectValue}</textarea>";
    }

    /**
     * Generates Text editor html code
     *
     * @param int    $iWidth      editor width
     * @param int    $iHeight     editor height
     * @param object $oObject     object passed to editor
     * @param string $sField      object field which content is passed to editor
     * @param string $sStylesheet stylesheet to use in editor
     *
     * @return string Editor output
     */
    protected function _generateTextEditor( $iWidth, $iHeight, $oObject, $sField, $sStylesheet = null )
    {
        // setup editor
        if ( $oEditor = $this->_getTextEditor( $iWidth, $iHeight, $oObject, $sField, $sStylesheet ) ) {
            // generate and return editor code
            $sEditorHtml = $oEditor->fetch( $iWidth, $iHeight );
        } else {
            $sEditorHtml = $this->_getPlainEditor( $iWidth, $iHeight, $oObject, $sField );
        }

        return $sEditorHtml;
    }

    /**
     * Resets number of articles in current shop categories
     *
     * @return null
     */
    public function resetNrOfCatArticles()
    {
        // resetting categories article count cache
        $this->resetContentCache();
    }

    /**
     * Resets number of articles in current shop vendors
     *
     * @return null
     */
    public function resetNrOfVendorArticles()
    {
        // resetting vendors cache
        $this->resetContentCache();
    }

    /**
     * Resets number of articles in current shop manufacturers
     *
     * @return null
     */
    public function resetNrOfManufacturerArticles()
    {
        // resetting manufacturers cache
        $this->resetContentCache();
    }

    /**
     * Function creates category tree for select list used in "Category main", "Article extend" etc.
     *
     * @param string $sTplVarName     name of template variable where is stored category tree
     * @param string $sEditCatId      ID of category witch we are editing
     * @param bool   $blForceNonCache Set to true to disable caching
     * @param int    $iTreeShopId     tree shop id
     *
     * @return string
     */
    protected function _createCategoryTree( $sTplVarName, $sEditCatId = '', $blForceNonCache = false, $iTreeShopId = null )
    {
        // caching category tree, to load it once, not many times
        if ( !isset( $this->oCatTree ) || $blForceNonCache ) {
            $this->oCatTree = oxNew( 'oxCategoryList' );
            $this->oCatTree->setShopID( $iTreeShopId );

            // setting language
            $oBase = $this->oCatTree->getBaseObject();
            $oBase->setLanguage( $this->_iEditLang );

            $this->oCatTree->loadList();
        }

        // copying tree
        $oCatTree = $this->oCatTree;
        //removing current category
        if ( $sEditCatId && isset( $oCatTree[$sEditCatId] ) ) {
            unset( $oCatTree[$sEditCatId] );
        }

        // add first fake category for not assigned articles
        $oRoot = oxNew( 'oxcategory' );
        $oRoot->oxcategories__oxtitle = new oxField('--');

        $oCatTree->assign( array_merge( array( '' => $oRoot ), $oCatTree->getArray() ) );

        // passing to view
        $this->_aViewData[$sTplVarName] = $oCatTree;

        return $oCatTree;
    }

    /**
    * Function creates category tree for select list used in "Category main", "Article extend" etc.
    * Returns ID of selected category if available
    *
    * @param string $sTplVarName     name of template variable where is stored category tree
    * @param string $sSelectedCatId  ID of category witch was selected in select list
    * @param string $sEditCatId      ID of category witch we are editing
    * @param bool   $blForceNonCache Set to true to disable caching
    * @param int    $iTreeShopId     tree shop id
    *
    * @return string
    */
    protected function _getCategoryTree( $sTplVarName, $sSelectedCatId, $sEditCatId = '', $blForceNonCache = false, $iTreeShopId = null )
    {
        $oCatTree = $this->_createCategoryTree($sTplVarName, $sEditCatId, $blForceNonCache, $iTreeShopId);

        // mark selected
        if ( $sSelectedCatId ) {
            // fixed parent category in select list
            foreach ($oCatTree as $oCategory) {
                if ($oCategory->getId() == $sSelectedCatId ) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        } else {
            // no category selected - opening first available
            $oCatTree->rewind();
            if ( $oCat = $oCatTree->current() ) {
                $oCat->selected = 1;
                $sSelectedCatId = $oCat->getId();
            }
        }

        // passing to view
        $this->_aViewData[$sTplVarName] =  $oCatTree;

        return $sSelectedCatId;
    }

    /**
     * Updates object folder parameters
     *
     * @return null
     */
    public function changeFolder()
    {
        $sFolder = oxConfig::getParameter( 'setfolder' );
        $sFolderClass = oxConfig::getParameter( 'folderclass' );

        if ( $sFolderClass == 'oxcontent' && $sFolder == 'CMSFOLDER_NONE' ) {
            $sFolder = '';
        }

        $oObject = oxNew( $sFolderClass );
        if ( $oObject->load( $this->getEditObjectId() ) ) {
            $oObject->{$oObject->getCoreTableName() . '__oxfolder'} = new oxField($sFolder);
            $oObject->save();
        }
    }

    /**
     * Sets-up navigation parameters
     *
     * @param string $sNode active view id
     *
     * @return null
     */
    protected function _setupNavigation( $sNode )
    {
        // navigation according to class
        if ( $sNode ) {

            $myAdminNavig = $this->getNavigation();

            // default tab
            $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab( $sNode, $this->_iDefEdit );

            // buttons
            $this->_aViewData['bottom_buttons'] = $myAdminNavig->getBtn( $sNode );
        }
    }

    /**
     * Resets count of vendor/manufacturer category items
     *
     * @param string $aIds array to reset type => id
     *
     * @return null
     */
    protected function _resetCounts( $aIds )
    {
        $oUtils = oxRegistry::get("oxUtilsCount");
        foreach ( $aIds as $sType => $aResetInfo ) {
            foreach ( $aResetInfo as $sResetId => $iPos ) {
                switch ( $sType ) {
                    case 'vendor':
                        $this->resetCounter( "vendorArticle", $sResetId );
                        break;
                    case 'manufacturer':
                        $this->resetCounter( "manufacturerArticle", $sResetId );
                        break;
                }
            }
        }
    }
}
