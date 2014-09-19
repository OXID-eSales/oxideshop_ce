<?php
/**
 * Price enter mode: bruto 
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 25%
 * Currency rate: 0.50 
 * Discounts: -
 * Vouchers: -
 * Trusted Shop:
 *  1. TS080501_5000_30_EUR, 
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
            'oxvat'                    => 25,
            'amount'                   => 250,
        ),
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_5000_30_EUR',           // trusted shop product id
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
             111 => array ( '12,48', '3.120,00' ),
        ),
        'totals' => array (
            'totalBrutto' => '3.120,00',
            'totalNetto'  => '2.496,00',
            'vats' => array (
                25 => '624,00'
            ),
            'trustedshop' => array(	//trusted shop fee doesn't depend on currency rate for this moment 
                'brutto' => '10,30',
                'netto' => '8,24',
                'vat' => '2,06'
            ),
            'grandTotal'  => '3.130,30'
        ),
    ),
    'options' => array (
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
				'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
        'activeCurrencyRate' => 0.50,
    ),
);