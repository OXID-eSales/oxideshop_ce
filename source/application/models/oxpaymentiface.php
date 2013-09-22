<?php
	/**
	 * Interface to get special payment actions needed in example for paypal and co.
	 * @author blange <code@wbl-konzept.de>
	 * @category core
	 * @package application
	 * @subpackage models
	 * @version $id$
	 */
	interface oxPaymentIface {
		/**
		 * Returns true if the gateway is used.
		 * @author blange <code@wbl-konzept.de>
		 * @return bool
		 */
		public function withGateway(); // function
	} // interface