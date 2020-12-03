<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts: 5
 *  1. shop discount 5.5% for product 9005
 *  2. shop discount -10% for product 9006
 * Vouchers: -;
 * Wrapping: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 * Short description:
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 9005,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
        1 => array(
            'oxid'                     => 9006,
            'oxprice'                  => 0.5,
            'oxvat'                    => 19,
            'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'shopdiscount5.5for9005',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9005 ),
            'oxsort' => 10,
        ),
        1 => array(
            'oxid'         => 'shopdiscount-10for9006',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9006 ),
            'oxsort' => 20,
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
    'expected' => array(
        'articles' => array(
             9005 => array( '1.125,67', '1.125,67' ),
             9006 => array( '0,66', '0,66' ),
        ),
        'totals' => array(
            'totalBrutto' => '1.126,33',
            'totalNetto'  => '946,50',
            'vats' => array(
                19 => '179,83'
            ),
            'delivery' => array(
                'brutto' => '112,63',
                'netto' => '94,65',
                'vat' => '17,98'
            ),
            'payment' => array(
                'brutto' => '10,00',
                'netto' => '8,40',
                 'vat' => '1,60'
            ),
            'grandTotal'  => '1.248,96'
        ),
    ),
    'options' => array(
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
