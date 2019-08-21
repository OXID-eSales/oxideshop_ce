<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;

/**
 * Payment list manager.
 *
 */
class PaymentList extends \OxidEsales\Eshop\Core\Model\ListModel
{
    /**
     * Home country id
     *
     * @var string
     */
    protected $_sHomeCountry = null;

    /**
     * Class Constructor
     */
    public function __construct()
    {
        $this->setHomeCountry($this->getConfig()->getConfigParam('aHomeCountry'));
        parent::__construct('oxpayment');
    }

    /**
     * Home country setter
     *
     * @param string $sHomeCountry country id
     */
    public function setHomeCountry($sHomeCountry)
    {
        if (is_array($sHomeCountry)) {
            $this->_sHomeCountry = current($sHomeCountry);
        } else {
            $this->_sHomeCountry = $sHomeCountry;
        }
    }

    /**
     * Creates payment list filter SQL to load current state payment list
     *
     * @param string                                   $sShipSetId user chosen delivery set
     * @param double                                   $dPrice     basket products price
     * @param \OxidEsales\Eshop\Application\Model\User $oUser      session user object
     *
     * @return string
     */
    protected function _getFilterSelect($sShipSetId, $dPrice, $oUser)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $sBoni = ($oUser && $oUser->oxuser__oxboni->value) ? $oUser->oxuser__oxboni->value : 0;

        $sTable = getViewName('oxpayments');
        $sQ = "select {$sTable}.* from ( select distinct {$sTable}.* from {$sTable} ";
        $sQ .= "left join oxobject2group ON oxobject2group.oxobjectid = {$sTable}.oxid ";
        $sQ .= "inner join oxobject2payment ON oxobject2payment.oxobjectid = " . $oDb->quote($sShipSetId) . " and oxobject2payment.oxpaymentid = {$sTable}.oxid ";
        $sQ .= "where {$sTable}.oxactive='1' ";
        $sQ .= " and {$sTable}.oxfromboni <= " . $oDb->quote($sBoni) . " and {$sTable}.oxfromamount <= " . $oDb->quote($dPrice) . " and {$sTable}.oxtoamount >= " . $oDb->quote($dPrice);

        // defining initial filter parameters
        $sGroupIds = '';
        $sCountryId = $this->getCountryId($oUser);

        // checking for current session user which gives additional restrictions for user itself, users group and country
        if ($oUser) {
            // user groups ( maybe would be better to fetch by function \OxidEsales\Eshop\Application\Model\User::getUserGroups() ? )
            foreach ($oUser->getUserGroups() as $oGroup) {
                if ($sGroupIds) {
                    $sGroupIds .= ', ';
                }
                $sGroupIds .= "'" . $oGroup->getId() . "'";
            }
        }

        $sGroupTable = getViewName('oxgroups');
        $sCountryTable = getViewName('oxcountry');

        $sCountrySql = $sCountryId ? "exists( select 1 from oxobject2payment as s1 where s1.oxpaymentid={$sTable}.OXID and s1.oxtype='oxcountry' and s1.OXOBJECTID=" . $oDb->quote($sCountryId) . " limit 1 )" : '0';
        $sGroupSql = $sGroupIds ? "exists( select 1 from oxobject2group as s3 where s3.OXOBJECTID={$sTable}.OXID and s3.OXGROUPSID in ( {$sGroupIds} ) limit 1 )" : '0';

        $sQ .= "  order by {$sTable}.oxsort asc ) as $sTable where (
            select
                if( exists( select 1 from oxobject2payment as ss1, $sCountryTable where $sCountryTable.oxid=ss1.oxobjectid and ss1.oxpaymentid={$sTable}.OXID and ss1.oxtype='oxcountry' limit 1 ),
                    {$sCountrySql},
                    1) &&
                if( exists( select 1 from oxobject2group as ss3, $sGroupTable where $sGroupTable.oxid=ss3.oxgroupsid and ss3.OXOBJECTID={$sTable}.OXID limit 1 ),
                    {$sGroupSql},
                    1)
                )  order by {$sTable}.oxsort asc ";

        return $sQ;
    }

    /**
     * Returns user country id for for payment selection
     *
     * @param \OxidEsales\Eshop\Application\Model\User $oUser oxuser object
     *
     * @return string
     */
    public function getCountryId($oUser)
    {
        $sCountryId = null;
        if ($oUser) {
            $sCountryId = $oUser->getActiveCountry();
        }

        if (!$sCountryId) {
            $sCountryId = $this->_sHomeCountry;
        }

        return $sCountryId;
    }

    /**
     * Loads and returns list of user payments.
     *
     * @param string                                   $sShipSetId user chosen delivery set
     * @param double                                   $dPrice     basket product price excl. discount
     * @param \OxidEsales\Eshop\Application\Model\User $oUser      session user object
     *
     * @return array
     */
    public function getPaymentList($sShipSetId, $dPrice, $oUser = null)
    {
        $this->selectString($this->_getFilterSelect($sShipSetId, $dPrice, $oUser));

        return $this->_aArray;
    }

    /**
     * Loads an object including all payments which are not mapped to a
     * predefined GoodRelations payment method.
     */
    public function loadNonRDFaPaymentList()
    {
        $sTable = getViewName('oxpayments');
        $sSubSql = "SELECT * FROM oxobject2payment WHERE oxobject2payment.OXPAYMENTID = $sTable.OXID AND oxobject2payment.OXTYPE = 'rdfapayment'";
        $this->selectString("SELECT $sTable.* FROM $sTable WHERE NOT EXISTS($sSubSql) AND $sTable.OXACTIVE = 1");
    }

    /**
     * Loads payments mapped to a
     * predefined GoodRelations payment method.
     *
     * @param double $dPrice product price
     */
    public function loadRDFaPaymentList($dPrice = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $sTable = getViewName('oxpayments');
        $sQ = "select $sTable.*, oxobject2payment.oxobjectid from $sTable left join (select oxobject2payment.* from oxobject2payment where oxobject2payment.oxtype = 'rdfapayment') as oxobject2payment on oxobject2payment.oxpaymentid=$sTable.oxid ";
        $sQ .= "where $sTable.oxactive = 1 ";
        if ($dPrice !== null) {
            $sQ .= "and $sTable.oxfromamount <= :amount and $sTable.oxtoamount >= :amount";
        }
        $rs = $oDb->select($sQ, [
            ':amount' => $dPrice
        ]);
        if ($rs != false && $rs->count() > 0) {
            $oSaved = clone $this->getBaseObject();
            while (!$rs->EOF) {
                $oListObject = clone $oSaved;
                $this->_assignElement($oListObject, $rs->fields);
                $this->_aArray[] = $oListObject;
                $rs->fetchRow();
            }
        }
    }
}
