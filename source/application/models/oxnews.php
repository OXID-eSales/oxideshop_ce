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
 * News manager.
 * Performs news text collection. News may be sorted by user categories (only
 * these user may read news), etc.
 *
 * @package model
 */
class oxNews extends oxI18n
{
    /**
     * User group object (default null).
     *
     * @var object
     */
    protected $_oGroups  = null;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxnews';

    /**
     * Class constructor, initiates parent constructor (parent::oxI18n()).
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxnews');
    }

    /**
     * Assigns object data.
     *
     * @param string $dbRecord database record to be assigned
     *
     * @return null
     */
    public function assign( $dbRecord )
    {

        parent::assign( $dbRecord );

        // convert date's to international format
        if ($this->oxnews__oxdate) {
            $this->oxnews__oxdate->setValue( oxRegistry::get("oxUtilsDate")->formatDBDate( $this->oxnews__oxdate->value ) );
        }
    }

    /**
     * Returns list of user groups assigned to current news object
     *
     * @return oxlist
     */
    public function getGroups()
    {
        if ( $this->_oGroups == null && $sOxid = $this->getId() ) {
            // usergroups
            $this->_oGroups = oxNew( 'oxlist', 'oxgroups' );
            $sViewName = getViewName( "oxgroups", $this->getLanguage() );
            $sSelect  = "select {$sViewName}.* from {$sViewName}, oxobject2group ";
            $sSelect .= "where oxobject2group.oxobjectid='$sOxid' ";
            $sSelect .= "and oxobject2group.oxgroupsid={$sViewName}.oxid ";
            $this->_oGroups->selectString( $sSelect );
        }

        return $this->_oGroups;
    }

    /**
     * Checks if this object is in group, returns true on success.
     *
     * @param string $sGroupID user group ID
     *
     * @return bool
     */
    public function inGroup( $sGroupID )
    {
        $blResult = false;
        $aGroups  = $this->getGroups();
        foreach ( $aGroups as $oObject ) {
            if ( $oObject->_sOXID == $sGroupID ) {
                $blResult = true;
                break;
            }
        }
        return $blResult;
    }

    /**
     * Deletes object information from DB, returns true on success.
     *
     * @param string $sOxid Object ID (default null)
     *
     * @return bool
     */
    public function delete( $sOxid = null )
    {
        if ( !$sOxid ) {
            $sOxid = $this->getId();
        }
        if ( !$sOxid ) {
            return false;
        }

        if ( $blDelete = parent::delete( $sOxid ) ) {
            $oDb = oxDb::getDb();
            $oDb->execute( "delete from oxobject2group where oxobject2group.oxobjectid = ".$oDb->quote( $sOxid ) );
        }


        return $blDelete;
    }

    /**
     * Updates object information in DB.
     *
     * @return null
     */
    protected function _update()
    {
        $this->oxnews__oxdate->setValue( oxRegistry::get("oxUtilsDate")->formatDBDate( $this->oxnews__oxdate->value, true ) );


        parent::_update();
    }

    /**
     * Inserts object details to DB, returns true on success.
     *
     * @return bool
     */
    protected function _insert()
    {
        if ( !$this->oxnews__oxdate || oxRegistry::get("oxUtilsDate")->isEmptyDate( $this->oxnews__oxdate->value ) ) {
            // if date field is empty, assigning current date
            $this->oxnews__oxdate = new oxField( date( 'Y-m-d' ) );
        } else {
            $this->oxnews__oxdate = new oxField( oxRegistry::get("oxUtilsDate")->formatDBDate( $this->oxnews__oxdate->value, true ) );
        }


        return parent::_insert();
    }

    /**
     * Sets data field value
     *
     * @param string $sFieldName index OR name (eg. 'oxarticles__oxtitle') of a data field to set
     * @param string $sValue     value of data field
     * @param int    $iDataType  field type
     *
     * @return null
     */
    protected function _setFieldData( $sFieldName, $sValue, $iDataType = oxField::T_TEXT)
    {
        switch (strtolower($sFieldName)) {
            case 'oxlongdesc':
            case 'oxnews__oxlongdesc':
                $iDataType = oxField::T_RAW;
                break;
        }
        return parent::_setFieldData($sFieldName, $sValue, $iDataType);
    }

    /**
     * get long description, parsed through smarty
     *
     * @return string
     */
    public function getLongDesc()
    {
        return oxRegistry::get("oxUtilsView")->parseThroughSmarty( $this->oxnews__oxlongdesc->getRawValue(), $this->getId().$this->getLanguage() );
    }

}
