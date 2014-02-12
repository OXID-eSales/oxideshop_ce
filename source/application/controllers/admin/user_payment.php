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
 * Admin user payment settings manager.
 * Collects user payment settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Payment.
 * @package admin
 */
class User_Payment extends oxAdminDetails
{
    /**
     * (default false).
     * @var bool
     */
    protected $_blDelete = false;

    /**
     * Selected user
     *
     * @var object
     */
    protected $_oActiveUser = null;

    /**
     * Selected user payment
     *
     * @var string
     */
    protected $_sPaymentId = null;

    /**
     * List of all payments
     *
     * @var object
     */
    protected $_oPaymentTypes = null;

    /**
     * Selected user payment
     *
     * @var object
     */
    protected $_oUserPayment = null;

    /**
     * List of all user payments
     *
     * @var object
     */
    protected $_oUserPayments = null;

    /**
     * Executes parent method parent::render(), creates oxlist and oxuser objects,
     * passes data to Smarty engine and returns name of template file "user_payment.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData["edit"]         = $this->getSelUserPayment();
        $this->_aViewData["oxpaymentid"]  = $this->getPaymentId();
        $this->_aViewData["paymenttypes"] = $this->getPaymentTypes();
        $this->_aViewData["edituser"]     = $this->getUser();
        $this->_aViewData["userpayments"] = $this->getUserPayments();

        if (!$this->_allowAdminEdit($soxId))
            $this->_aViewData['readonly'] = true;


        return "user_payment.tpl";
    }

    /**
     * Saves user payment settings.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        if ( $this->_allowAdminEdit( $soxId ) ) {

            $aParams    = oxConfig::getParameter( "editval");
            $aDynvalues = oxConfig::getParameter( "dynvalue");

            if ( isset( $aDynvalues ) ) {
                // store the dynvalues
                $aParams['oxuserpayments__oxvalue'] = oxRegistry::getUtils()->assignValuesToText( $aDynvalues );
            }

            if ( $aParams['oxuserpayments__oxid'] == "-1" ) {
                $aParams['oxuserpayments__oxid'] = null;
            }

            $oAdress = oxNew( "oxuserpayment" );
            $oAdress->assign( $aParams );
            $oAdress->save();
        }
    }

    /**
     * Deletes selected user payment information.
     *
     * @return null
     */
    public function delPayment()
    {
        $aParams = oxConfig::getParameter( "editval" );
        $soxId = $this->getEditObjectId();
        if ( $this->_allowAdminEdit( $soxId )) {
            if ( $aParams['oxuserpayments__oxid'] != "-1") {
                $oAdress = oxNew( "oxuserpayment" );
                if ( $oAdress->load( $aParams['oxuserpayments__oxid'] ) ) {
                    $this->_blDelete = ( bool ) $oAdress->delete();
                }
            }
        }
    }

    /**
     * Returns selected user
     *
     * @return object
     */
    public function getUser()
    {
        if ( $this->_oActiveUser == null ) {
            $this->_oActiveUser = false;
            $sOxId = $this->getEditObjectId();
            if ( $sOxId != "-1" && isset( $sOxId)) {
                // load object
                $this->_oActiveUser = oxNew( "oxuser" );
                $this->_oActiveUser->load( $sOxId);
            }
        }
        return $this->_oActiveUser;
    }

    /**
     * Returns selected Payment Id
     *
     * @return object
     */
    public function getPaymentId()
    {
        if ( $this->_sPaymentId == null ) {
            $this->_sPaymentId = oxConfig::getParameter( "oxpaymentid");
            if ( !$this->_sPaymentId || $this->_blDelete ) {
                if ( $oUser = $this->getUser() ) {
                   $oUserPayments = $oUser->getUserPayments();
                   if ( isset( $oUserPayments[0]) ) {
                       $this->_sPaymentId = $oUserPayments[0]->oxuserpayments__oxid->value;
                   }
                }
            }
            if ( !$this->_sPaymentId ) {
                $this->_sPaymentId = "-1";
            }
        }
        return $this->_sPaymentId;
    }

    /**
     * Returns selected Payment Id
     *
     * @return object
     */
    public function getPaymentTypes()
    {
        if ( $this->_oPaymentTypes == null ) {

            // all paymenttypes
            $this->_oPaymentTypes = oxNew( "oxlist" );
            $this->_oPaymentTypes->init( "oxpayment");
            $oListObject = $this->_oPaymentTypes->getBaseObject();
            $oListObject->setLanguage( oxRegistry::getLang()->getObjectTplLanguage() );
            $this->_oPaymentTypes->getList();
        }
        return $this->_oPaymentTypes;
    }

    /**
     * Returns selected Payment
     *
     * @return object
     */
    public function getSelUserPayment()
    {
        if ( $this->_oUserPayment == null ) {
            $this->_oUserPayment = false;
            $sPaymentId = $this->getPaymentId();
            if ( $sPaymentId != "-1" && isset( $sPaymentId ) ) {
                $this->_oUserPayment = oxNew( "oxuserpayment" );
                $this->_oUserPayment->load( $sPaymentId );
                $sTemplate = $this->_oUserPayment->oxuserpayments__oxvalue->value;

                // generate selected paymenttype
                $oPaymentTypes = $this->getPaymentTypes();
                foreach ( $oPaymentTypes as $oPayment ) {
                    if ( $oPayment->oxpayments__oxid->value == $this->_oUserPayment->oxuserpayments__oxpaymentsid->value) {
                        $oPayment->selected = 1;
                        // if there are no values assigned we set default from paymenttype
                        if ( !$sTemplate )
                            $sTemplate = $oPayment->oxpayments__oxvaldesc->value;
                        break;
                    }
                }
                $this->_oUserPayment->setDynValues( oxRegistry::getUtils()->assignValuesFromText( $sTemplate ) );
            }
        }
        return $this->_oUserPayment;
    }

    /**
     * Returns selected Payment Id
     *
     * @return object
     */
    public function getUserPayments()
    {
        if ( $this->_oUserPayments == null ) {
            $this->_oUserPayments = false;
            if ( $oUser = $this->getUser() ) {
                $sTplLang = oxRegistry::getLang()->getObjectTplLanguage();
                $sPaymentId = $this->getPaymentId();
                $this->_oUserPayments = $oUser->getUserPayments();
                // generate selected
                foreach ( $this->_oUserPayments as $oUserPayment ) {
                    $oPayment = oxNew( 'oxpayment' );
                    $oPayment->setLanguage( $sTplLang );
                    $oPayment->load( $oUserPayment->oxuserpayments__oxpaymentsid->value );
                    $oUserPayment->oxpayments__oxdesc = clone $oPayment->oxpayments__oxdesc;
                    if ( $oUserPayment->oxuserpayments__oxid->value == $sPaymentId ) {
                        $oUserPayment->selected = 1;
                        break;
                    }
                }
            }
        }
        return $this->_oUserPayments;
    }

}
