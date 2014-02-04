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
 * Group manager.
 * Base class for user groups. Does nothing special yet.
 *
 * @package model
 */
class oxGroups extends oxI18n
{
    /**
     * Name of current class
     * @var string
     */
    protected $_sClassName = 'oxgroups';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxgroups' );
    }


    /**
     * Deletes user group from database. Returns true/false, according to deleting status.
     *
     * @param string $sOXID Object ID (default null)
     *
     * @return bool
     */
    public function delete( $sOXID = null )
    {
        if ( !$sOXID ) {
            $sOXID = $this->getId();
        }
        if ( !$sOXID ) {
            return false;
        }



        parent::delete( $sOXID );

        $oDb = oxDb::getDb();


        // deleting related data records
        $sDelete = 'delete from oxobject2group where oxobject2group.oxgroupsid = ' . $oDb->quote( $sOXID );
        $rs = $oDb->execute( $sDelete );

        $sDelete = 'delete from oxobject2delivery where oxobject2delivery.oxobjectid = ' . $oDb->quote( $sOXID );
        $rs = $oDb->execute( $sDelete );

        $sDelete = 'delete from oxobject2discount where oxobject2discount.oxobjectid = ' . $oDb->quote( $sOXID );
        $rs = $oDb->execute( $sDelete );

        $sDelete = 'delete from oxobject2payment where oxobject2payment.oxobjectid = ' . $oDb->quote( $sOXID );
        $rs = $oDb->execute( $sDelete );

        return $rs->EOF;
    }

}
