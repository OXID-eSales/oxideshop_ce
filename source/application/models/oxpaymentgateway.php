<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id$
 */

/**
 * Payment gateway manager.
 * Checks and sets payment method data, executes payment.
 *
 * @package model
 */

class oxPaymentGateway extends oxSuperCfg
{
    /**
     * Payment status (active - true/not active - false) (default false).
     * @var bool
     */
    protected $_blActive       = false;

    /**
     * oUserpayment object (default null).
     * @var object
     */
    protected $_oPaymentInfo   = null;

    /**
     * Last error nr. For backward compatibility must be >3
     * @abstract
     * @var string
     */
    protected $_iLastErrorNo   = 4;

    /**
     * Last error text.
     * @abstract
     * @var string
     */
    protected $_sLastError     = null;

	public function callModulePayment($sMethod) {
		return false;
	} // function

	protected function getPaymentMode() {
		return '';
	} // function

	public function preparePayment($fAmount, oxOrder $oOrder) {

	} // function

    /**
     * Sets payment parameters.
     *
     * @param object $oUserpayment User payment object
     *
     * @return null
     */
    public function setPaymentParams( $oUserpayment )
    {
        // store data
        $this->_oPaymentInfo = & $oUserpayment;
	    return $this;
    }

    /**
     * Executes payment, returns true on success.
     *
     * @param double $dAmount Goods amount
     * @param object &$oOrder User ordering object
     *
     * @return bool
     */
    public function executePayment( $dAmount, & $oOrder )
    {
	    $bReturn             = true;
	    $this->_iLastErrorNo = null;
	    $this->_sLastError   = null;

        if ($this->_isActive() && ($this->_oPaymentInfo->oxuserpayments__oxpaymentsid->value !== 'oxempty')) {
			$bReturn = false;

	        if ($sMode = $this->getPaymentMode()) {
		        // TODO Fill Methods.
		        switch ($sMode) {
			        case 'authorize':
				        // "Pay" the order, but do not mark it as paid, for example in the time of pending payment.
				        $bReturn = $this->authorize();
				        break;

			        case 'pay':
				        // Pay the order and mark it finished as paid, for example if the money "flowed" already.
				        $bReturn = $this->pay();
				        break;

			        case 'reserve':
				        // Just Reserve the order without any type of fixed payment action.
				        $bReturn = $this->reserve();
				        break;

			        default:
				        $bReturn = $this->callModulePayment($sMode);
		        } // switch
	        } // if
        } // if

	    return $bReturn;
    }

    /**
     * Returns last payment processing error nr.
     *
     * @return int
     */
    public function getLastErrorNo()
    {
        return $this->_iLastErrorNo;
    }

    /**
     * Returns last payment processing error.
     *
     * @return int
     */
    public function getLastError()
    {
        return $this->_sLastError;
    }

    /**
     * Returns true is payment active.
     *
     * @return bool
     */
    protected function _isActive()
    {
        return $this->_blActive;
    }
}
