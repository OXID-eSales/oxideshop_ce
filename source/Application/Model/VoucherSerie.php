<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxRegistry;
use oxDb;

/**
 * Voucher serie manager.
 * Manages list of available Vouchers (fetches, deletes, etc.).
 *
 */
class VoucherSerie extends \OxidEsales\Eshop\Core\Model\BaseModel
{
    /**
     * User groups array (default null).
     *
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
    public function delete($sOxId = null)
    {
        if (!$sOxId) {
            $sOxId = $this->getId();
        }

        $this->unsetDiscountRelations();
        $this->unsetUserGroups();
        $this->deleteVoucherList();

        return parent::delete($sOxId);
    }

    /**
     * Collects and returns user group list.
     *
     * @return object
     */
    public function setUserGroups()
    {
        if ($this->_oGroups === null) {
            $this->_oGroups = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $this->_oGroups->init('oxgroups');
            $sViewName = getViewName("oxgroups");
            $sSelect = "select gr.* from {$sViewName} as gr, oxobject2group as o2g where
                         o2g.oxobjectid = :oxobjectid and gr.oxid = o2g.oxgroupsid ";
            $this->_oGroups->selectString($sSelect, [
                ':oxobjectid' => $this->getId()
            ]);
        }

        return $this->_oGroups;
    }

    /**
     * Removes user groups relations.
     */
    public function unsetUserGroups()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = 'delete from oxobject2group where oxobjectid = :oxobjectid';
        $oDb->execute($sDelete, [
            ':oxobjectid' => $this->getId()
        ]);
    }

    /**
     * Removes product or dategory relations.
     */
    public function unsetDiscountRelations()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = 'delete from oxobject2discount where oxobject2discount.oxdiscountid = :oxdiscountid';
        $oDb->execute($sDelete, [
            ':oxdiscountid' => $this->getId()
        ]);
    }

    /**
     * Returns array of a vouchers assigned to this serie.
     *
     * @return array
     */
    public function getVoucherList()
    {
        $oVoucherList = oxNew(\OxidEsales\Eshop\Application\Model\VoucherList::class);
        $sSelect = 'select * from oxvouchers 
            where oxvoucherserieid = :oxvoucherserieid';
        $oVoucherList->selectString($sSelect, [
            ':oxvoucherserieid' => $this->getId()
        ]);

        return $oVoucherList;
    }

    /**
     * Deletes assigned voucher list.
     */
    public function deleteVoucherList()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sDelete = 'delete from oxvouchers where oxvoucherserieid = :oxvoucherserieid';
        $oDb->execute($sDelete, [
            ':oxvoucherserieid' => $this->getId()
        ]);
    }

    /**
     * Returns array of vouchers counts.
     *
     * @return array
     */
    public function countVouchers()
    {
        $aStatus = [];

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sQuery = 'select count(*) as total from oxvouchers 
            where oxvoucherserieid = :oxvoucherserieid';
        $aStatus['total'] = $oDb->getOne($sQuery, [
            ':oxvoucherserieid' => $this->getId()
        ]);

        $sQuery = 'select count(*) as used from oxvouchers 
            where oxvoucherserieid = :oxvoucherserieid 
                and ((oxorderid is not NULL and oxorderid != "") or (oxdateused is not NULL and oxdateused != 0))';
        $aStatus['used'] = $oDb->getOne($sQuery, [
            ':oxvoucherserieid' => $this->getId()
        ]);

        $aStatus['available'] = $aStatus['total'] - $aStatus['used'];

        return $aStatus;
    }

    /**
     * Get voucher status base on given date (if nothing was passed, current datetime will be used as a measure).
     *
     * @param string|null $sNow Date
     *
     * @return int
     */
    public function getVoucherStatusByDatetime($sNow = null)
    {
        //return content
        $iActive = 1;
        $iInactive = 0;

        $oUtilsDate = \OxidEsales\Eshop\Core\Registry::getUtilsDate();
        //current object datetime
        $sBeginDate = $this->oxvoucherseries__oxbegindate->value;
        $sEndDate = $this->oxvoucherseries__oxenddate->value;

        //If nothing pass, use current server time
        if ($sNow == null) {
            $sNow = date('Y-m-d H:i:s', $oUtilsDate->getTime());
        }

        //Check for active status.
        if (($sBeginDate == '0000-00-00 00:00:00' && $sEndDate == '0000-00-00 00:00:00') || //If both dates are empty => treat it as always active
            ($sBeginDate == '0000-00-00 00:00:00' && $sNow <= $sEndDate) || //check for end date without start date
            ($sBeginDate <= $sNow && $sEndDate == '0000-00-00 00:00:00') || //check for start date without end date
            ($sBeginDate <= $sNow && $sNow <= $sEndDate)
        ) { //check for both start date and end date.
            return $iActive;
        }

        //If active status code was reached, return as inactive
        return $iInactive;
    }
}
