<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxDb;
use oxField;

/**
 * Class manages payment countries
 */
class PaymentCountryAjax extends \OxidEsales\Eshop\Application\Controller\Admin\ListComponentAjax
{
    /**
     * Columns array
     *
     * @var array
     */
    protected $_aColumns = ['container1' => [ // field , table,         visible, multilanguage, ident
        ['oxtitle', 'oxcountry', 1, 1, 0],
        ['oxisoalpha2', 'oxcountry', 1, 0, 0],
        ['oxisoalpha3', 'oxcountry', 0, 0, 0],
        ['oxunnum3', 'oxcountry', 0, 0, 0],
        ['oxid', 'oxcountry', 0, 0, 1]
    ],
                                 'container2' => [
                                     ['oxtitle', 'oxcountry', 1, 1, 0],
                                     ['oxisoalpha2', 'oxcountry', 1, 0, 0],
                                     ['oxisoalpha3', 'oxcountry', 0, 0, 0],
                                     ['oxunnum3', 'oxcountry', 0, 0, 0],
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
        // looking for table/view
        $sCountryTable = $this->_getViewName('oxcountry');
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sCountryId = $this->getConfig()->getRequestParameter('oxid');
        $sSynchCountryId = $this->getConfig()->getRequestParameter('synchoxid');

        // category selected or not ?
        if (!$sCountryId) {
            // which fields to load ?
            $sQAdd = " from $sCountryTable where $sCountryTable.oxactive = '1' ";
        } else {
            $sQAdd = " from oxobject2payment left join $sCountryTable on $sCountryTable.oxid=oxobject2payment.oxobjectid ";
            $sQAdd .= "where $sCountryTable.oxactive = '1' and oxobject2payment.oxpaymentid = " . $oDb->quote($sCountryId) . " and oxobject2payment.oxtype = 'oxcountry' ";
        }

        if ($sSynchCountryId && $sSynchCountryId != $sCountryId) {
            $sQAdd .= "and $sCountryTable.oxid not in ( ";
            $sQAdd .= "select $sCountryTable.oxid from oxobject2payment left join $sCountryTable on $sCountryTable.oxid=oxobject2payment.oxobjectid ";
            $sQAdd .= "where oxobject2payment.oxpaymentid = " . $oDb->quote($sSynchCountryId) . " and oxobject2payment.oxtype = 'oxcountry' ) ";
        }

        return $sQAdd;
    }

    /**
     * Adds chosen user group (groups) to delivery list
     */
    public function addPayCountry()
    {
        $aChosenCntr = $this->_getActionIds('oxcountry.oxid');
        $soxId = $this->getConfig()->getRequestParameter('synchoxid');

        if ($this->getConfig()->getRequestParameter('all')) {
            $sCountryTable = $this->_getViewName('oxcountry');
            $aChosenCntr = $this->_getAll($this->_addFilter("select $sCountryTable.oxid " . $this->_getQuery()));
        }
        if ($soxId && $soxId != "-1" && is_array($aChosenCntr)) {
            foreach ($aChosenCntr as $sChosenCntr) {
                $oObject2Payment = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
                $oObject2Payment->init('oxobject2payment');
                $oObject2Payment->oxobject2payment__oxpaymentid = new \OxidEsales\Eshop\Core\Field($soxId);
                $oObject2Payment->oxobject2payment__oxobjectid = new \OxidEsales\Eshop\Core\Field($sChosenCntr);
                $oObject2Payment->oxobject2payment__oxtype = new \OxidEsales\Eshop\Core\Field("oxcountry");
                $oObject2Payment->save();
            }
        }
    }

    /**
     * Removes chosen user group (groups) from delivery list
     */
    public function removePayCountry()
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
}
