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
 *  1. "TS080501_500_30_EUR"  "netto" => "0.82", "amount" => "500" ,
 * Wrapping: -;
 * Costs VAT caclulation rule: proportional
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 * Short description:
 * Trusted shop calculation in Neto-brutto mode, Trusted shop price is displayed in brutto mode 
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
            'oxvat'                    => 18,
            'amount'                   => 1,
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
             9006 => array ( '0,65', '0,65' ),
        ),
        'totals' => array (
            'totalBrutto' => '1.787,45',
            'totalNetto'  => '1.502,06',
            'vats' => array (
                19 => '285,29',
			    18 => '0,10',
            ),
            'delivery' => array(
                'brutto' => '178,75',
                'netto' => '150,21',
                'vat' => '28,54'
            ),
            'payment' => array(
                'brutto' => '10,00',
             //   'netto' => '8,40',
             //   'vat' => '1,60'
            ),

			'trustedshop' => array(
                'brutto' => '0,98',
             //   'netto' => '4,12',
             //   'vat' => '0,78',
            ),
            'grandTotal'  => '1.977,18'
        ),
    ),
    'options' => array (
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
				'sAdditionalServVATCalcMethod' => 'proportional', 
                'blShowVATForPayCharge' => false,
                'blShowVATForDelivery' => true,
        ),
        'activeCurrencyRate' => 1.00,
    ),
);