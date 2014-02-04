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
 * Article review manager.
 * Performs loading, updating, inserting of article review.
 *
 * @package model
 */
class oxReview extends oxBase
{
    /**
     * Shop control variable
     *
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxreview';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxreviews' );
    }

    /**
     * Calls parent::assign and assigns review writer data
     *
     * @param array $dbRecord database record
     *
     * @return bool
     */
    public function assign( $dbRecord )
    {
        $blRet = parent::assign( $dbRecord );

        if ( isset( $this->oxreviews__oxuserid ) && $this->oxreviews__oxuserid->value ) {
            $oDb = oxDb::getDb();
            $this->oxuser__oxfname = new oxField( $oDb->getOne( "select oxfname from oxuser where oxid=".$oDb->quote( $this->oxreviews__oxuserid->value ) ));
        }

        return $blRet;
    }

    /**
     * Loads object review information. Returns true on success.
     *
     * @param string $oxId ID of object to load
     *
     * @return bool
     */
    public function load( $oxId )
    {
        if ( $blRet = parent::load( $oxId ) ) {
            // convert date's to international format
            $this->oxreviews__oxcreate->setValue(oxRegistry::get("oxUtilsDate")->formatDBDate( $this->oxreviews__oxcreate->value ));
        }

        return $blRet;
    }

    /**
     * Inserts object data fiels in DB. Returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        // set oxcreate
        $this->oxreviews__oxcreate = new oxField(date( 'Y-m-d H:i:s', oxRegistry::get("oxUtilsDate")->getTime() ));
        return parent::_insert();
    }


    /**
     * get oxList of reviews for given object ids and type
     *
     * @param string  $sType       type of given ids
     * @param mixed   $aIds        given object ids to load, can be array or just one id, given as string
     * @param boolean $blLoadEmpty true if want to load empty text reviews
     * @param int     $iLoadInLang language to select for loading
     *
     * @return oxList
     */
    public function loadList($sType, $aIds, $blLoadEmpty = false, $iLoadInLang = null)
    {
        $oDb = oxDb::getDb();

        $oRevs = oxNew( 'oxlist' );
        $oRevs->init( 'oxreview' );

        $sObjectIdWhere = '';
        if ( is_array( $aIds ) && count( $aIds ) ) {
            $sObjectIdWhere = "oxreviews.oxobjectid in ( ".implode(", ", oxDb::getInstance()->quoteArray( $aIds ))." )";
        } elseif ( is_string( $aIds ) && $aIds ) {
            $sObjectIdWhere = "oxreviews.oxobjectid = ".$oDb->quote( $aIds );
        } else {
            return $oRevs;
        }

        $iLoadInLang = is_null( $iLoadInLang ) ? (int) oxRegistry::getLang()->getBaseLanguage() : (int) $iLoadInLang;

        $sSelect = "select oxreviews.* from oxreviews where oxreviews.oxtype = ".$oDb->quote( $sType )." and $sObjectIdWhere and oxreviews.oxlang = '$iLoadInLang'";

        if ( !$blLoadEmpty ) {
            $sSelect .= ' and oxreviews.oxtext != "" ';
        }

        if ( $this->getConfig()->getConfigParam( 'blGBModerate' ) ) {
            $sSelect .= ' and ( oxreviews.oxactive = "1" ';
            $sSelect .= ( $oUser = $this->getUser() ) ? 'or  oxreviews.oxuserid = ' . $oDb->quote( $oUser->getId() ) . ' )' :  ')';
        }

        $sSelect .= ' order by oxreviews.oxcreate desc ';

        $oRevs->selectString( $sSelect );

        // change date
        foreach ( $oRevs as $oItem ) {
            $oItem->oxreviews__oxcreate->convertToFormattedDbDate();
            $oItem->oxreviews__oxtext->convertToPseudoHtml();
        }

        return $oRevs;
    }

    /**
     * Retuns review object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->oxreviews__oxtype->value;
    }

    /**
     * Retuns review object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->oxreviews__oxobjectid->value;
    }



}
