<?php
	class oxcmp_payment extends oxView {
		// TODO
		public function breakExternalPayment() {
			// Error-Callback
		} // function

		public function cancelExternalPayment() {
			// controlled way of cancelling the order.
		} // function

		protected function checkSessionForOrder() {

		} // function

		public function finishExternalPayment() {
			// finish the order correctly.
		} // function

		protected function getOrder() {

		} // function

		public function render() {
			parent::render();

			if ($this->checkSessionForOrder() && (@$this->getOrder()->oxorder__oxtransstatus->value === 'NOT_FINISHED')) {
				$this->breakExternalPayment();

				oxRegistry::getUtils()->redirect($this->getViewConfig()->getPaymentLink(), true, 307);
			} // if
		} // if

		public function setOrder(oxOrder $oOrder) {
			return $this;
		} // function
	} // class