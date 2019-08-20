<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use Exception;

/**
 * Class manages deliveryset and delivery configuration
 */
class DeliverySetMainAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxdelivery', 1, 1, 0],
        ['oxaddsum', 'oxdelivery', 1, 0, 0],
        ['oxaddsumtype', 'oxdelivery', 1, 0, 0],
        ['oxid', 'oxdelivery', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxdelivery', 1, 1, 0],
                                     ['oxaddsum', 'oxdelivery', 1, 0, 0],
                                     ['oxaddsumtype', 'oxdelivery', 1, 0, 0],
                                     ['oxid', 'oxdel2delset', 0, 0, 1]
                                 ]
    ];

    /**
     * Returns SQL query for data to fetc
     *
     * @return string
     */
    protected function _getQuery()
    {
        $sId = $this->getConfig()->getRequestParameter('oxid');
        $sSynchId = $this->getConfig()->getRequestParameter('synchoxid');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sDeliveryViewName = $this->_getViewName('oxdelivery');

        // category selected or not ?
        if (!$sId) {
            $sQAdd = " from $sDeliveryViewName where 1 ";
        } else {
            $sQAdd = " from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = " . $oDb->quote($sId);
        }

        if ($sSynchId && $sSynchId != $sId) {
            $sQAdd .= "and $sDeliveryViewName.oxid not in ( select $sDeliveryViewName.oxid from $sDeliveryViewName left join oxdel2delset on oxdel2delset.oxdelid=$sDeliveryViewName.oxid ";
            $sQAdd .= "where oxdel2delset.oxdelsetid = " . $oDb->quote($sSynchId) . " ) ";
        }

        return $sQAdd;
    }

    /**
     * Remove this delivery cost from these sets
     */
    public function removeFromSet()
    {
        $aRemoveGroups = $this->_getActionIds('oxdel2delset.oxid');
        if ($this->getConfig()->getRequestParameter('all')) {
            $sQ = $this->_addFilter("delete oxdel2delset.* " . $this->_getQuery());
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        } elseif ($aRemoveGroups && is_array($aRemoveGroups)) {
            $sQ = "delete from oxdel2delset where oxdel2delset.oxid in (" . implode(", ", \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->quoteArray($aRemoveGroups)) . ") ";
            \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->Execute($sQ);
        }
    }

    /**
     * Adds this delivery cost to these sets
     *
     * @throws Exception
     */
    public function addToSet()
    {
        $aChosenSets = $this->_getActionIds('oxdelivery.oxid');
        $soxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('synchoxid');

        // adding
        if ($this->getConfig()->getRequestParameter('all')) {
            $sDeliveryViewName = $this->_getViewName('oxdelivery');
            $aChosenSets = $this->_getAll($this->_addFilter("select $sDeliveryViewName.oxid " . $this->_getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aChosenSets)) {
            // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804 and ESDEV-3822).
            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
            foreach ($aChosenSets as $sChosenSet) {
                // check if we have this entry already in
                // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                $sID = $database->getOne("select oxid from oxdel2delset where oxdelid = :oxdelid and oxdelsetid = :oxdelsetid", [
                    ':oxdelid' => $sChosenSet,
                    ':oxdelsetid' => $soxId
                ]);
                if (!isset($sID) || !$sID) {
                    $oDel2delset = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                    $oDel2delset->init('oxdel2delset');
                    $oDel2delset->oxdel2delset__oxdelid = new \OxidEsales\Eshop\Core\Field($sChosenSet);
                    $oDel2delset->oxdel2delset__oxdelsetid = new \OxidEsales\Eshop\Core\Field($soxId);
                    $oDel2delset->save();
                }
            }
        }
    }
}
