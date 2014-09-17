<?php
/*
/**
 * Price enter mode: netto 
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products 
 * Currency rate: 1.0 
 * Discounts: 5
 *  1. shop discount 50% for product 9005 
 *  2. shop discount -10% for product 9006 
 * Vouchers: -;
 * Trusted Shop:
 *  1. "TS080501_1500_30_EUR"  "netto" => "2.47", "amount" => "1500" ,
 * Wrapping: -;
 * Costs VAT caclulation rule: max 
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 * Short description:
 * Trusted shop calculation in Neto-brutto mode,
 */
$aData = array(
    'articles' => array (
        0 => array (
            'oxid'                     => 9005,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
        1 => array (
            'oxid'                     => 9006,
            'oxprice'                  => 0.5,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
    ),
    'trustedshop' => array (
        'product_id'     => 'TS080501_1500_30_EUR',           // trusted shop product id
        'payments'    => array(                              // paymentids
            'oxidcashondel'  => 'DIRECT_DEBIT',
            'oxidcreditcard' => 'DIRECT_DEBIT',
            'oxiddebitnote'  => 'DIRECT_DEBIT',
            'oxidpayadvance' => 'DIRECT_DEBIT',
            'oxidinvoice'    => 'DIRECT_DEBIT',
            'oxempty'        => 'DIRECT_DEBIT',
        )
    ),
    'discounts' => array (
        0 => array (
            'oxid'         => 'shopdiscount50for9005',
            'oxaddsum'     => 50,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 9005 ),
        ),
        1 => array (
            'oxid'         => 'shopdiscount-10for9006',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 9006 ),
        ),


    ),
    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxtitle' => '6_abs_del',
                'oxactive' => 1,
                'oxaddsum' => 10,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 2,
                'oxparamend' => 99999
            ),
        ),
        'payment' => array(
            0 => array(
                'oxtitle' => '1 abs payment',
                'oxaddsum' => 10,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
		),
    ),
    'expected' => array (
        'articles' => array (
             9005 => array ( '595,60', '595,60' ),
             9006 => array ( '0,66', '0,66' ),
        ),
        'totals' => array (
            'totalBrutto' => '596,26',
            'totalNetto'  => '501,06',
            'vats' => array (
                19 => '95,20'
            ),
            'delivery' => array(
                'brutto' => '59,63',
                'netto' => '50,11',
                'vat' => '9,52'
            ),
            'payment' => array(
                'brutto' => '10,00',
                'netto' => '8,40',
                 'vat' => '1,60'
            ),

			'trustedshop' => array(
                'brutto' => '2,94',
                'netto' => '2,47',
                'vat' => '0,47',
            ),
            'grandTotal'  => '668,83'
        ),
    ),
    'options' => array (
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
				'sAdditionalServVATCalcMethod' => 'biggest_net', 
                'blShowVATForPayCharge' => true,
                'blShowVATForDelivery' => true,
        ),
        'activeCurrencyRate' => 1.00,
    ),
);