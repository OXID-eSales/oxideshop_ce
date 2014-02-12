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
 * Admin article extended parameters manager.
 * Collects and updates (on user submit) extended article properties ( such as
 * weight, dimensions, purchase Price and etc.). There is ability to assign article
 * to any chosen article group.
 * Admin Menu: Manage Products -> Articles -> Extended.
 * @package admin
 */
class Article_Extend extends oxAdminDetails
{
    /**
     * Unit array
     * @var array
     */
    protected $_aUnitsArray = null;

    /**
     * Collects available article extended parameters, passes them to
     * Smarty engine and returns template file name "article_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew( 'oxArticle' );

        $soxId = $this->getEditObjectId();

        $this->_createCategoryTree( "artcattree");

        // all categories
        if ( $soxId != "-1" && isset( $soxId ) ) {
            // load object
            $oArticle->loadInLang( $this->_iEditLang, $soxId );


            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang( key($oOtherLang), $soxId );
            }

            foreach ( $oOtherLang as $id => $language) {
                $oLang= new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] =  clone $oLang;
            }

            // variant handling
            if ( $oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew( 'oxArticle' );
                $oParentArticle->load( $oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $oParentArticle;
                $this->_aViewData["oxparentid"]    = $oArticle->oxarticles__oxparentid->value;
            }
        }


            $oDB = oxDb::getDB();
            $myConfig = $this->getConfig();

            $sArticleTable = getViewName( 'oxarticles', $this->_iEditLang );
            $sSelect  = "select $sArticleTable.oxtitle, $sArticleTable.oxartnum, $sArticleTable.oxvarselect from $sArticleTable where 1 ";
            // #546
            $sSelect .= $myConfig->getConfigParam( 'blVariantsSelection' )?'':" and $sArticleTable.oxparentid = '' ";
            $sSelect .= " and $sArticleTable.oxid = ".$oDB->quote( $oArticle->oxarticles__oxbundleid->value );

            $rs = $oDB->Execute( $sSelect);
            if ($rs != false && $rs->RecordCount() > 0) {
                while (!$rs->EOF) {
                    $sArtNum = new oxField($rs->fields[1]);
                    $sArtTitle = new oxField($rs->fields[0]." ".$rs->fields[2]);
                    $rs->MoveNext();
                }
            }
            $this->_aViewData['bundle_artnum'] = $sArtNum;
            $this->_aViewData['bundle_title'] = $sArtTitle;


        $iAoc = $this->getConfig()->getRequestParameter("aoc");
        if ( $iAoc == 1 ) {
            $oArticleExtendAjax = oxNew( 'article_extend_ajax' );
            $this->_aViewData['oxajax'] = $oArticleExtendAjax->getColumns();

            return "popups/article_extend.tpl";
        } elseif ( $iAoc == 2 ) {
            $oArticleBundleAjax = oxNew( 'article_bundle_ajax' );
            $this->_aViewData['oxajax'] = $oArticleBundleAjax->getColumns();

            return "popups/article_bundle.tpl";
        }

        //load media files
        $this->_aViewData['aMediaUrls'] = $oArticle->getMediaUrls();

        return "article_extend.tpl";
    }

    /**
     * Saves modified extended article parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $aMyFile = $this->getConfig()->getUploadedFile( "myfile" );
        $aMediaFile = $this->getConfig()->getUploadedFile( "mediaFile" );
        if ( is_array( $aMyFile['name'] ) && reset( $aMyFile['name'] ) || $aMediaFile['name'] ) {
            $myConfig = $this->getConfig();
            if ( $myConfig->isDemoShop() ) {
                $oEx = oxNew( "oxExceptionToDisplay" );
                $oEx->setMessage( 'ARTICLE_EXTEND_UPLOADISDISABLED' );
                oxRegistry::get("oxUtilsView")->addErrorToDisplay( $oEx, false );

                return;
            }
        }

        $soxId = $this->getEditObjectId();
        $aParams = oxRegistry::getConfig()->getRequestParameter( "editval");
        // checkbox handling
        if ( !isset( $aParams['oxarticles__oxissearch'])) {
            $aParams['oxarticles__oxissearch'] = 0;
        }
        if ( !isset( $aParams['oxarticles__oxblfixedprice'])) {
            $aParams['oxarticles__oxblfixedprice'] = 0;
        }

        // new way of handling bundled articles
        //#1517C - remove possibility to add Bundled Product
        //$this->setBundleId($aParams, $soxId);

        // default values
        $aParams = $this->addDefaultValues( $aParams);

        $oArticle = oxNew( "oxarticle" );
        $oArticle->loadInLang( $this->_iEditLang, $soxId);

        if ( $aParams['oxarticles__oxtprice'] != $oArticle->oxarticles__oxtprice->value &&  $aParams['oxarticles__oxtprice'] && $aParams['oxarticles__oxtprice'] <= $oArticle->oxarticles__oxprice->value) {
            $this->_aViewData["errorsavingtprice"] = 1;
        }

        $oArticle->setLanguage(0);
        $oArticle->assign( $aParams);
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle = oxRegistry::get("oxUtilsFile")->processFiles( $oArticle );
        $oArticle->save();

        //saving media file
        $sMediaUrl  = $this->getConfig()->getRequestParameter( "mediaUrl" );
        $sMediaDesc = $this->getConfig()->getRequestParameter( "mediaDesc");

        if ( ( $sMediaUrl && $sMediaUrl != 'http://' ) || $aMediaFile['name'] || $sMediaDesc ) {

            if ( !$sMediaDesc ) {
                return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_NODESCRIPTIONADDED' );
            }

            if ( ( !$sMediaUrl || $sMediaUrl == 'http://' ) && !$aMediaFile['name'] ) {
                return oxRegistry::get("oxUtilsView")->addErrorToDisplay( 'EXCEPTION_NOMEDIAADDED' );
            }

            $oMediaUrl = oxNew( "oxMediaUrl" );
            $oMediaUrl->setLanguage( $this->_iEditLang );
            $oMediaUrl->oxmediaurls__oxisuploaded = new oxField( 0, oxField::T_RAW );

            //handle uploaded file
            if ($aMediaFile['name']) {
                try {
                    $sMediaUrl = oxRegistry::get("oxUtilsFile")->processFile( 'mediaFile', 'out/media/' );
                    $oMediaUrl->oxmediaurls__oxisuploaded = new oxField(1, oxField::T_RAW);
                } catch (Exception $e) {
                    return oxRegistry::get("oxUtilsView")->addErrorToDisplay( $e->getMessage() );
                }
            }

            //save media url
            $oMediaUrl->oxmediaurls__oxobjectid = new oxField($soxId, oxField::T_RAW);
            $oMediaUrl->oxmediaurls__oxurl      = new oxField($sMediaUrl, oxField::T_RAW);
            $oMediaUrl->oxmediaurls__oxdesc     = new oxField($sMediaDesc, oxField::T_RAW);
            $oMediaUrl->save();
        }

        // renew price update time
        oxNew( "oxArticleList" )->renewPriceUpdateTime();
    }

    /**
     * Deletes media url (with possible linked files)
     *
     * @return bool
     */
    public function deletemedia()
    {
        $soxId = $this->getEditObjectId();
        $sMediaId = $this->getConfig()->getRequestParameter( "mediaid" );
        if ($sMediaId && $soxId) {
            $oMediaUrl = oxNew("oxMediaUrl");
            $oMediaUrl->load($sMediaId);
            $oMediaUrl->delete();
        }
    }

    /**
     * Adds default values for extended article parameters. Returns modified
     * parameters array.
     *
     * @param array $aParams Article parameters array
     *
     * @return array
     */
    public function addDefaultValues( $aParams)
    {
        $aParams['oxarticles__oxexturl'] = str_replace( "http://", "", $aParams['oxarticles__oxexturl']);

        return $aParams;
    }

    /**
     * Updates existing media descriptions
     *
     * @return null
     */
    public function updateMedia()
    {
        $aMediaUrls = $this->getConfig()->getRequestParameter( 'aMediaUrls' );
        if ( is_array( $aMediaUrls ) ) {
            foreach ( $aMediaUrls as $sMediaId => $aMediaParams ) {
                $oMedia = oxNew("oxMediaUrl");
                if ( $oMedia->load( $sMediaId ) ) {
                    $oMedia->setLanguage(0);
                    $oMedia->assign( $aMediaParams );
                    $oMedia->setLanguage( $this->_iEditLang );
                    $oMedia->save();
                }
            }
        }
    }

    /**
     * Returns array of possible unit combination and its translation for edit language
     *
     * @return array
     */
    public function getUnitsArray()
    {
        if ( $this->_aUnitsArray === null ) {
           $this->_aUnitsArray = oxRegistry::getLang()->getSimilarByKey( "_UNIT_", $this->_iEditLang, false );
        }
        return $this->_aUnitsArray;
    }
}
