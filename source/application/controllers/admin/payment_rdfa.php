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
 * Admin article RDFa payment manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Shop Settings -> Payment Methods -> RDFa.
 * @package admin
 */
class payment_rdfa extends oxAdminDetails
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "payment_rdfa.tpl";

    /**
     * Predefined RDFa payment methods
     * 0 value have general payments, 1 have creditcar payments
     *
     * @var array
     */
    protected $_aRDFaPayments = array("ByBankTransferInAdvance" => 0,
                                      "ByInvoice" => 0,
                                      "Cash" => 0,
                                      "CheckInAdvance" => 0,
                                      "COD" => 0,
                                      "DirectDebit" => 0,
                                      "GoogleCheckout" => 0,
                                      "PayPal" => 0,
                                      "PaySwarm" => 0,
                                      "AmericanExpress" => 1,
                                      "DinersClub" => 1,
                                      "Discover" => 1,
                                      "JCB" => 1,
                                      "MasterCard" => 1,
                                      "VISA" => 1);

    /**
     * Saves changed mapping configurations
     *
     * @return null
     */
    public function save()
    {
        $aParams = oxConfig::getParameter("editval");
        $aRDFaPayments = (array) oxConfig::getParameter("ardfapayments");

        // Delete old mappings
        $oDb = oxDb::getDb();
        $oDb->execute("DELETE FROM oxobject2payment WHERE oxpaymentid = '".oxConfig::getParameter("oxid")."' AND OXTYPE = 'rdfapayment'");

        // Save new mappings
        foreach ( $aRDFaPayments as $sPayment ) {
            $oMapping = oxNew("oxbase");
            $oMapping->init("oxobject2payment");
            $oMapping->assign($aParams);
            $oMapping->oxobject2payment__oxobjectid = new oxField($sPayment);
            $oMapping->save();
        }
    }

    /**
     * Returns an array including all available RDFa payments.
     *
     * @return array
     */
    public function getAllRDFaPayments()
    {
        $aRDFaPayments = array();
        $aAssignedRDFaPayments = $this->getAssignedRDFaPayments();
        foreach ($this->_aRDFaPayments as $sName => $iType) {
            $oPayment = new stdClass();
            $oPayment->name = $sName;
            $oPayment->type = $iType;
            $oPayment->checked = in_array($sName, $aAssignedRDFaPayments);
            $aRDFaPayments[] = $oPayment;
        }
        return $aRDFaPayments;
    }

    /**
     * Returns array of RDFa payments which are assigned to current payment
     *
     * @return array
     */
    public function getAssignedRDFaPayments()
    {
        $oDb = oxDb::getDb();
        $aRDFaPayments = array();
        $sSelect = 'select oxobjectid from oxobject2payment where oxpaymentid='.$oDb->quote( oxConfig::getParameter("oxid") ).' and oxtype = "rdfapayment" ';
        $rs = $oDb->execute( $sSelect );
        if ( $rs && $rs->recordCount()) {
            while ( !$rs->EOF ) {
                $aRDFaPayments[] = $rs->fields[0];
                $rs->moveNext();
            }
        }
        return $aRDFaPayments;
    }

}