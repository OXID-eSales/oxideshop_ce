<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxDb;
use oxField;
use Exception;

/**
 * Class manages deliveryset payment
 */
class DeliverySetPaymentAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxdesc', 'oxpayments', 1, 1, 0],
        ['oxaddsum', 'oxpayments', 1, 0, 0],
        ['oxaddsumtype', 'oxpayments', 0, 0, 0],
        ['oxid', 'oxpayments', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxdesc', 'oxpayments', 1, 1, 0],
                                     ['oxaddsum', 'oxpayments', 1, 0, 0],
                                     ['oxaddsumtype', 'oxpayments', 0, 0, 0],
                                     ['oxid', 'oxobject2payment', 0, 0, 1]
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sId = $this->getConfig()->getRequestParameter('oxid');
        $sSynchId = $this->getConfig()->getRequestParameter('synchoxid');

        $sPayTable = $this->_getViewName('oxpayments');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from $sPayTable where 1 ";
        } else {
            $sQAdd = " from oxobject2payment, $sPayTable where oxobject2payment.oxobjectid = " . $oDb->quote($sId);
            $sQAdd .= " and oxobject2payment.oxpaymentid = $sPayTable.oxid and oxobject2payment.oxtype = 'oxdelset' ";
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= "and $sPayTable.oxid not in ( select $sPayTable.oxid from oxobject2payment, $sPayTable where oxobject2payment.oxobjectid = " . $oDb->quote($sSynchId);
            $sQAdd .= "and oxobject2payment.oxpaymentid = $sPayTable.oxid and oxobject2payment.oxtype = 'oxdelset' ) ";
        }

        return $sQAdd;
    }

    /**
     * Remove these payments from this set
     */
    public function removePayFromSet()
    {
        $aChosenCntr = $this->_getActionIds('oxobject2payment.oxid');
        if ($this->getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxobject2payment.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif (is_array($aChosenCntr)) {
            $sQ = "delete from oxobject2payment where oxobject2payment.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aChosenCntr)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds this payments to this set
     *
     * @throws Exception
     */
    public function addPayToSet()
    {
        $aChosenSets = $this->_getActionIds('oxpayments.oxid');
        $soxId = $this->getConfig()->getRequestParameter('synchoxid');

        // adding
        if ($this->getConfig()->getRequestParameter('all')) {
            $sPayTable = $this->_getViewName('oxpayments');
            $aChosenSets = $this->_getAll($this->_addFilter("select $sPayTable.oxid " . $this->_getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aChosenSets)) {
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
            foreach ($aChosenSets as $sChosenSet) {
                // check if we have this entry already in
                // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                $sID = $database->getOne("select oxid from oxobject2payment where oxpaymentid = :oxpaymentid and oxobjectid = :oxobjectid and oxtype = 'oxdelset'", [
                    ':oxpaymentid' => $sChosenSet,
                    ':oxobjectid' => $soxId
                ]);
                if (!isset($sID) || !$sID) {
                    $oObject = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                    $oObject->init('oxobject2payment');
                    $oObject->oxobject2payment__oxpaymentid = new \OxidEsales\Eshop\Core\Field($sChosenSet);
                    $oObject->oxobject2payment__oxobjectid = new \OxidEsales\Eshop\Core\Field($soxId);
                    $oObject->oxobject2payment__oxtype = new \OxidEsales\Eshop\Core\Field("oxdelset");
                    $oObject->save();
                }
            }
        }
    }
}
