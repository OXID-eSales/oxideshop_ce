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
 * Voucher serie manager.
 * Manages list of available Vouchers (fetches, deletes, etc.).
 *
 * @package model
 */
class oxVoucherSerie extends oxBase
{

    /**
     * User groups array (default null).
     * @var object
     */
    protected $_oGroups = null;

    /**
     * @var string name of current class
     */
    protected $_sClassName = 'oxvoucherserie';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxvoucherseries');
    }

    /**
     * Override delete function so we can delete user group and article or category relations first.
     *
     * @param string $sOxId object ID (default null)
     *
     * @return null
     */
    public function delete( $sOxId = null )
    {
        if ( !$sOxId ) {
            $sOxId = $this->getId();
        }


        $this->unsetDiscountRelations();
        $this->unsetUserGroups();
        $this->deleteVoucherList();
        return parent::delete( $sOxId );
    }

    /**
     * Collects and returns user group list.
     *
     * @return object
     */
    public function setUserGroups()
    {
        if ( $this->_oGroups === null ) {
            $this->_oGroups = oxNew( 'oxlist' );
            $this->_oGroups->init( 'oxgroups' );
            $sViewName = getViewName( "oxgroups" );
            $sSelect  = "select gr.* from {$sViewName} as gr, oxobject2group as o2g where
                         o2g.oxobjectid = ". oxDb::getDb()->quote( $this->getId() ) ." and gr.oxid = o2g.oxgroupsid ";
            $this->_oGroups->selectString( $sSelect );
        }

        return $this->_oGroups;
    }

    /**
     * Removes user groups relations.
     *
     * @return null
     */
    public function unsetUserGroups()
    {
        $oDb = oxDb::getDb();
        $sDelete = 'delete from oxobject2group where oxobjectid = ' . $oDb->quote( $this->getId() );
        $oDb->execute( $sDelete );
    }

    /**
     * Removes product or dategory relations.
     *
     * @return null
     */
    public function unsetDiscountRelations()
    {
        $oDb = oxDb::getDb();
        $sDelete = 'delete from oxobject2discount where oxobject2discount.oxdiscountid = ' . $oDb->quote( $this->getId() );
        $oDb->execute( $sDelete );
    }

    /**
     * Returns array of a vouchers assigned to this serie.
     *
     * @return array
     */
    public function getVoucherList()
    {
        $oVoucherList = oxNew( 'oxvoucherlist' );
        $sSelect = 'select * from oxvouchers where oxvoucherserieid = ' . oxDb::getDb()->quote( $this->getId() );
        $oVoucherList->selectString( $sSelect );
        return $oVoucherList;
    }

    /**
     * Deletes assigned voucher list.
     *
     * @return null
     */
    public function deleteVoucherList()
    {
        $oDb = oxDb::getDb();
        $sDelete = 'delete from oxvouchers where oxvoucherserieid = ' . $oDb->quote( $this->getId() );
        $oDb->execute( $sDelete );
    }

    /**
     * Returns array of vouchers counts.
     *
     * @return array
     */
    public function countVouchers()
    {
        $aStatus = array();

        $oDb = oxDb::getDb();
        $sQuery = 'select count(*) as total from oxvouchers where oxvoucherserieid = ' .$oDb->quote( $this->getId() );
        $aStatus['total'] = $oDb->getOne( $sQuery );

        $sQuery = 'select count(*) as used from oxvouchers where oxvoucherserieid = ' . $oDb->quote( $this->getId() ) . ' and ((oxorderid is not NULL and oxorderid != "") or (oxdateused is not NULL and oxdateused != 0))';
        $aStatus['used'] = $oDb->getOne( $sQuery );

        $aStatus['available'] = $aStatus['total'] - $aStatus['used'];

        return $aStatus;
    }
}
