<?php
/**
 * Price enter mode: bruto 
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 1.0 
 * Discounts: -
 * Vouchers: -
 * Trusted Shop:
 *  1. TS080501_500_30_EUR, netto 0.82
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max 
 * Costs:
 *  1. Payment - 
 *  2. Delivery - 
 *  3. TS +
 */
$aData = array(
    'articles' => array (
        0 => array (
            'oxid'                     => 111,
            'oxprice'                  => 24.95,
            'oxvat'                    => 19,
            'amount'                   => 150,
        ),
		1 => array (
            'oxid'                     => 222,
            'oxprice'                  => 7.99,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_2500_30_EUR',           // trusted shop product id
        'payments'    => array(                              // paymentids
            'oxidcashondel'  => 'DIRECT_DEBIT',
            'oxidcreditcard' => 'DIRECT_DEBIT',
            'oxiddebitnote'  => 'DIRECT_DEBIT',
            'oxidpayadvance' => 'DIRECT_DEBIT',
            'oxidinvoice'    => 'DIRECT_DEBIT',
            'oxempty'        => 'DIRECT_DEBIT',
        )
    ),
    'expected' => array (
        'articles' => array (
             111 => array ( '10,49', '1.573,50' ),
			 222 => array ( '3,36', '3,36' ),
        ),
        'totals' => array (
            'totalBrutto' => '1.876,46',
            'totalNetto'  => '1.576,86',
            'vats' => array (
                19 => '299,60'
            ),
            'trustedshop' => array(
                'brutto' => '4,90',
                'netto' => '4,12',
                'vat' => '0,78'
            ),
            'grandTotal'  => '1.881,36'
        ),
    ),
    'options' => array (
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
				'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
        'activeCurrencyRate' => 0.50,
    ),
);