<?php
/*
/**
 * Price enter mode: netto 
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products 
 * Currency rate: 1.0 
 * Discounts: 5
 *  1. shop discount 50% for product 9005 (amound =3)
 *  2. shop discount -10% for product 9006 
 * Vouchers: -;
 * Trusted Shop:
 *  1. "TS080501_2500_30_EUR"  "netto" => "4.12", "amount" => "2500" ,
 * Wrapping: -;
 * Costs VAT caclulation rule: max 
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 * Short description:
 * Trusted shop calculation in Neto-brutto mode, Trusted shop price is displayed in netto mode
 */
$aData = array(
    'articles' => array (
        0 => array (
            'oxid'                     => 9005,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            'amount'                   => 3,
        ),
        1 => array (
            'oxid'                     => 9006,
            'oxprice'                  => 0.5,
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
             9005 => array ( '595,60', '1.786,80' ),
             9006 => array ( '0,66', '0,66' ),
        ),
        'totals' => array (
            'totalBrutto' => '1.787,46',
            'totalNetto'  => '1.502,07',
            'vats' => array (
                19 => '285,39'
            ),
            'delivery' => array(
                'brutto' => '178,75',
                'netto' => '150,21',
                'vat' => '28,54'
            ),
            'payment' => array(
                'brutto' => '10,00',
                'netto' => '8,40',
                 'vat' => '1,60'
            ),

			'trustedshop' => array(
                'brutto' => '4,90',
                'netto' => '4,12',
                'vat' => '0,78',
            ),
            'grandTotal'  => '1.981,11'
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