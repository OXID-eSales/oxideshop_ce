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
 * Admin article list manager.
 * Collects base article information (according to filtering rules), performs sorting,
 * deletion of articles, etc.
 * Admin Menu: Manage Products -> Articles.
 * @package admin
 */
class Article_List extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxarticle';

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxarticlelist';

    /**
     * Collects articles base data and passes them according to filtering rules,
     * returns name of template file "article_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();
        $sPwrSearchFld = oxConfig::getParameter( "pwrsearchfld" );
        $sPwrSearchFld = $sPwrSearchFld ? strtolower( $sPwrSearchFld ) : "oxtitle";

        $oArticle = null;
        $oList = $this->getItemList();
        if ( $oList) {
            foreach ( $oList as $key => $oArticle ) {
                $sFieldName = "oxarticles__{$sPwrSearchFld}";

                // formatting view
                if ( !$myConfig->getConfigParam( 'blSkipFormatConversion' ) ) {
                    if ( $oArticle->$sFieldName->fldtype == "datetime" )
                        oxRegistry::get("oxUtilsDate")->convertDBDateTime( $oArticle->$sFieldName );
                    elseif ( $oArticle->$sFieldName->fldtype == "timestamp" )
                        oxRegistry::get("oxUtilsDate")->convertDBTimestamp( $oArticle->$sFieldName );
                    elseif ( $oArticle->$sFieldName->fldtype == "date" )
                        oxRegistry::get("oxUtilsDate")->convertDBDate( $oArticle->$sFieldName );
                }

                $oArticle->pwrsearchval = $oArticle->$sFieldName->value;
                $oList[$key] = $oArticle;
            }
        }

        parent::render();

        // load fields
        if ( !$oArticle && $oList ) {
            $oArticle = $oList->getBaseObject();
        }
        $this->_aViewData["pwrsearchfields"] = $oArticle ? $this->getSearchFields() : null;
        $this->_aViewData["pwrsearchfld"]    = strtoupper( $sPwrSearchFld );

        $aFilter = $this->getListFilter();
        if ( isset( $aFilter["oxarticles"][$sPwrSearchFld] ) ) {
            $this->_aViewData["pwrsearchinput"] = $aFilter["oxarticles"][$sPwrSearchFld];
        }

        $sType  = '';
        $sValue = '';

        $sArtCat= oxConfig::getParameter( "art_category" );
        if ( $sArtCat && strstr( $sArtCat, "@@" ) !== false ) {
            list( $sType, $sValue ) = explode( "@@", $sArtCat );
        }
        $this->_aViewData["art_category"] = $sArtCat;

        // parent categorie tree
        $this->_aViewData["cattree"] = $this->getCategoryList($sType, $sValue);

        // manufacturer list
        $this->_aViewData["mnftree"] = $this->getManufacturerlist($sType, $sValue);

        // vendor list
        $this->_aViewData["vndtree"] = $this->getVendorList($sType, $sValue);

        return "article_list.tpl";
    }

    /**
     * Returns array of fields which may be used for product data search
     *
     * @return array
     */
    public function getSearchFields()
    {
        $aSkipFields = array("oxblfixedprice", "oxvarselect", "oxamitemid", "oxamtaskid", "oxpixiexport", "oxpixiexported") ;
        $oArticle = oxNew( "oxarticle" );
        return array_diff( $oArticle->getFieldNames(), $aSkipFields );
    }

    /**
     * Load category list, mark active category;
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return oxCategoryList
     */
    public function getCategoryList($sType, $sValue)
    {
        $myConfig = $this->getConfig();

        // parent categorie tree
        $oCatTree = oxNew( "oxCategoryList" );
        $oCatTree->loadList();
        if ( $sType === 'cat' ) {
            foreach ($oCatTree as $oCategory ) {
                if ( $oCategory->oxcategories__oxid->value == $sValue ) {
                    $oCategory->selected = 1;
                    break;
                }
            }
        }

        return $oCatTree;
    }

    /**
     * Load manufacturer list, mark active category;
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return oxManufacturerList
     */
    public function getManufacturerList($sType, $sValue)
    {
        $oMnfTree = oxNew( "oxManufacturerList");
        $oMnfTree->loadManufacturerList();
        if ( $sType === 'mnf' ) {
            foreach ($oMnfTree as $oManufacturer ) {
                if ( $oManufacturer->oxmanufacturers__oxid->value == $sValue ) {
                    $oManufacturer->selected = 1;
                    break;
                }
            }
        }

        return $oMnfTree;
    }

    /**
     * Load vendor list, mark active category;
     *
     * @param string $sType  active list type
     * @param string $sValue active list item id
     *
     * @return oxVendorList
     */
    public function getVendorList($sType, $sValue)
    {
        $oVndTree = oxNew( "oxVendorList");
        $oVndTree->loadVendorList();
        if ( $sType === 'vnd' ) {
            foreach ($oVndTree as $oVendor ) {
                if ( $oVendor->oxvendor__oxid->value == $sValue ) {
                    $oVendor->selected = 1;
                    break;
                }
            }
        }

        return $oVndTree;
    }

    /**
     * Builds and returns SQL query string.
     *
     * @param object $oListObject list main object
     *
     * @return string
     */
    protected function _buildSelectString( $oListObject = null )
    {
        $sQ = parent::_buildSelectString( $oListObject );
        if ( $sQ ) {
            $sTable = getViewName( "oxarticles" );
            $sQ .= " and $sTable.oxparentid = '' ";

            $sType   = false;
            $sArtCat = oxConfig::getParameter( "art_category" );
            if ( $sArtCat && strstr( $sArtCat, "@@" ) !== false ) {
                list( $sType, $sValue ) = explode("@@", $sArtCat );
            }

            switch ( $sType ) {
                // add category
                case 'cat':
                    $oStr = getStr();
                    $sO2CView = getViewName( "oxobject2category" );
                    $sInsert  = "from $sTable left join $sO2CView on $sTable.oxid = $sO2CView.oxobjectid where $sO2CView.oxcatnid = ".oxDb::getDb()->quote($sValue)." and ";
                    $sQ = $oStr->preg_replace( "/from\s+$sTable\s+where/i", $sInsert, $sQ);
                    break;
                // add category
                case 'mnf':
                    $sQ.= " and $sTable.oxmanufacturerid = ".oxDb::getDb()->quote($sValue);
                    break;
                // add vendor
                case 'vnd':
                    $sQ.= " and $sTable.oxvendorid = ".oxDb::getDb()->quote($sValue);
                    break;
            }
        }

        return $sQ;
    }

    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        // we override this to select only parent articles
        $this->_aWhere = parent::buildWhere();

        // adding folder check
        $sFolder = oxConfig::getParameter( 'folder' );
        if ( $sFolder && $sFolder != '-1' ) {
            $this->_aWhere[getViewName( "oxarticles" ).".oxfolder"] = $sFolder;
        }

        return $this->_aWhere;
    }

    /**
     * Deletes entry from the database
     *
     * @return null
     */
    public function deleteEntry()
    {
        $sOxId = $this->getEditObjectId();
        $oArticle = oxNew( "oxarticle");
        if ( $sOxId && $oArticle->load( $sOxId ) ) {
            parent::deleteEntry();
        }
    }

}
