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
 * Content manager.
 * Base object for content pages
 *
 * @package model
 */
class oxContent extends oxI18n implements oxIUrl
{
    /**
     * Current class name.
     *
     * @var string
     */
    protected $_sClassName = 'oxcontent';

    /**
     * Seo article urls for languages.
     *
     * @var array
     */
    protected $_aSeoUrls = array();

    /**
     * Content parent category id
     *
     * @var string
     */
    protected $_sParentCatId = null;

    /**
     * Expanded state of a content category.
     *
     * @var bool
     */
    protected $_blExpanded = null;

    /**
     * Marks that current object is managed by SEO.
     *
     * @var bool
     */
    protected $_blIsSeoObject = true;

    /**
     * Category id.
     *
     * @var string
     */
    protected $_sCategoryId;

    /**
     * Extra getter to guarantee compatibility with templates.
     *
     * @param string $sName parameter name
     *
     * @return mixed
     */
    public function __get( $sName )
    {
        switch ( $sName ) {
            case 'expanded':
                return $this->getExpanded();
                break;
        }
        return parent::__get( $sName );
    }

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxcontents' );
    }

    /**
     * Returns the expanded state of the content category.
     *
     * @return bool
     */
    public function getExpanded()
    {
        if ( !isset( $this->_blExpanded ) ) {
            $this->_blExpanded = ( $this->getId() == oxConfig::getParameter( 'oxcid' ) );
        }
        return $this->_blExpanded;
    }

    /**
     * Sets category id.
     *
     * @param string $sCategoryId
     */
    public function setCategoryId( $sCategoryId )
    {
        $this->oxcontents__oxcatid = new oxField( $sCategoryId );
    }

    /**
     * Returns category id.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->oxcontents__oxcatid->value;
    }


    /**
     * Get data from db.
     *
     * @param string $sLoadId id
     *
     * @return array
     */
    protected function _loadFromDb( $sLoadId )
    {
        $sTable = $this->getViewName();
        $sShopId = $this->getShopId();
        $aParams = array( $sTable . '.oxloadid' => $sLoadId, $sTable . '.oxshopid' => $sShopId );

        $sSelect = $this->buildSelectString( $aParams );

        //Loads "credits" content object and its text (first available)
        if ( $sLoadId == 'oxcredits' ) {

            // fetching column names
            $sColQ = "SHOW COLUMNS FROM oxcontents WHERE field LIKE  'oxcontent%'";
            $aCols = oxDb::getDb()->getAll( $sColQ );

            // building subquery
            $sPattern = "IF ( %s != '', %s, %s ) ";
            $iCount = count( $aCols ) - 1;

            $sContQ = "SELECT {$sPattern}";
            foreach ( $aCols as $iKey => $aCol ) {
                $sContQ = sprintf( $sContQ, $aCol[0], $aCol[0], $iCount != $iKey ? $sPattern : "''" );
            }
            $sContQ .= " FROM oxcontents WHERE oxloadid = '{$sLoadId}' AND oxshopid = '{$sShopId}'";

            $sSelect = $this->buildSelectString( $aParams );
            $sSelect = str_replace( "`{$sTable}`.`oxcontent`", "( $sContQ ) as oxcontent", $sSelect );
        }

        $aData = oxDb::getDb( oxDb::FETCH_MODE_ASSOC )->getRow( $sSelect );

        return $aData;
    }

    /**
     * Loads Content by using field oxloadid instead of oxid.
     *
     * @param string $sLoadId content load ID
     *
     * @return bool
     */
    public function loadByIdent( $sLoadId )
    {

            $aData = $this->_loadFromDb( $sLoadId );

        if ( $aData ) {
            $this->assign( $aData );
            return true;
        }

        return false;
    }


    /**
     * Returns unique object id.
     *
     * @return string
     */
    public function getLoadId()
    {
        return $this->oxcontents__oxloadid->value;
    }

    /**
     * Returns unique object id.
     *
     * @return string
     */
    public function isActive()
    {
        return $this->oxcontents__oxactive->value;
    }

    /**
     * Replace the "&amp;" into "&" and call base class.
     *
     * @param array $dbRecord database record
     *
     * @return null
     */
    public function assign( $dbRecord )
    {

        parent::assign( $dbRecord );
        // workaround for firefox showing &lang= as &9001;= entity, mantis#0001272

        if ( $this->oxcontents__oxcontent ) {
            $this->oxcontents__oxcontent->setValue( str_replace( '&lang=', '&amp;lang=', $this->oxcontents__oxcontent->value ), oxField::T_RAW );
        }
    }

    /**
     * Returns raw content seo url
     *
     * @param int $iLang language id
     *
     * @return string
     */
    public function getBaseSeoLink( $iLang )
    {
        return oxRegistry::get( "oxSeoEncoderContent" )->getContentUrl( $this, $iLang );
    }

    /**
     * getLink returns link for this content in the frontend.
     *
     * @param int $iLang language id [optional]
     *
     * @return string
     */
    public function getLink( $iLang = null )
    {
        if ( !oxRegistry::getUtils()->seoIsActive() ) {
            return $this->getStdLink( $iLang );
        }

        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        if ( !isset( $this->_aSeoUrls[$iLang] ) ) {
            $this->_aSeoUrls[$iLang] = $this->getBaseSeoLink( $iLang );
        }

        return $this->_aSeoUrls[$iLang];
    }

    /**
     * Returns base dynamic url: shopurl/index.php?cl=details
     *
     * @param int $iLang language id
     * @param bool $blAddId add current object id to url or not
     * @param bool $blFull return full including domain name [optional]
     *
     * @return string
     */
    public function getBaseStdLink( $iLang, $blAddId = true, $blFull = true )
    {
        $sUrl = '';
        if ( $blFull ) {
            //always returns shop url, not admin
            $sUrl = $this->getConfig()->getShopUrl( $iLang, false );
        }

        if ( $this->oxcontents__oxloadid->value === 'oxcredits' ) {
            $sUrl .= "index.php?cl=credits";
        } else {
            $sUrl .= "index.php?cl=content";
        }
        $sUrl .= '&amp;oxloadid=' . $this->getLoadId();

        if ( $blAddId ) {
            $sUrl .= "&amp;oxcid=" . $this->getId();
            // adding parent category if if available
            if ( $this->_sParentCatId !== false && $this->oxcontents__oxcatid->value && $this->oxcontents__oxcatid->value != 'oxrootid' ) {

                if ( $this->_sParentCatId === null ) {
                    $this->_sParentCatId = false;
                    $oDb = oxDb::getDb();
                    $sParentId = $oDb->getOne( "select oxparentid from oxcategories where oxid = " . $oDb->quote( $this->oxcontents__oxcatid->value ) );
                    if ( $sParentId && 'oxrootid' != $sParentId ) {
                        $this->_sParentCatId = $sParentId;
                    }
                }

                if ( $this->_sParentCatId ) {
                    $sUrl .= "&amp;cnid=" . $this->_sParentCatId;
                }
            }
        }

        //always returns shop url, not admin
        return $sUrl;
    }

    /**
     * Returns standard URL to product.
     *
     * @param integer $iLang language
     * @param array $aParams additional params to use [optional]
     *
     * @return string
     */
    public function getStdLink( $iLang = null, $aParams = array() )
    {
        if ( $iLang === null ) {
            $iLang = $this->getLanguage();
        }

        return oxRegistry::get( "oxUtilsUrl" )->processUrl( $this->getBaseStdLink( $iLang ), true, $aParams, $iLang );
    }

    /**
     * Sets data field value.
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue value of data field
     * @param int $iDataType field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT )
    {
        if ( 'oxcontent' === strtolower( $sFieldName ) || 'oxcontents__oxcontent' === strtolower( $sFieldName ) ) {
            $iDataType = oxField::T_RAW;
        }

        return parent::_setFieldData( $sFieldName, $sValue, $iDataType );
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOXID Object ID(default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }

        if ( parent::delete( $sOXID ) ) {
            oxRegistry::get( "oxSeoEncoderContent" )->onDeleteContent( $sOXID );
            return true;
        }
        return false;
    }

    /**
     * Save this Object to database, insert or update as needed.
     *
     * @return mixed
     */
    public function save()
    {
        $blSaved = parent::save();
        if ( $blSaved && $this->oxcontents__oxloadid->value === 'oxagb' ) {
            $sShopId = $this->getConfig()->getShopId();
            $sVersion = $this->oxcontents__oxtermversion->value;

            $oDb = oxDb::getDb();
            // dropping expired..
            $oDb->execute( "delete from oxacceptedterms where oxshopid='{$sShopId}' and oxtermversion != " . $oDb->quote( $sVersion ) );
        }
        return $blSaved;
    }

    /**
     * Returns latest terms version id.
     *
     * @return string
     */
    public function getTermsVersion()
    {
        if ( $this->loadByIdent( 'oxagb' ) ) {
            return $this->oxcontents__oxtermversion->value;
        }
    }

    /**
     * Returns type of content.
     *
     * @return integer
     */
    public function getType()
    {
        return $this->oxcontents__oxtype->value;
    }

}
