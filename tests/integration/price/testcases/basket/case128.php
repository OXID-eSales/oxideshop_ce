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
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_500_30_EUR',           // trusted shop product id
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
        ),
        'totals' => array (
            'totalBrutto' => '1.872,47',
            'totalNetto'  => '1.573,50',
            'vats' => array (
                19 => '298,97'
            ),
            'trustedshop' => array(
                'brutto' => '0,98',
                'netto' => '0,82',
                'vat' => '0,16'
            ),
            'grandTotal'  => '1.873,45'
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