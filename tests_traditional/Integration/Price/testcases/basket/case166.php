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
            'oxid'         => 'shopdiscount50for9005',
            'oxaddsum'     => 50,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
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
             9005 => array( '595,60', '595,60' ),
             9006 => array( '0,66', '0,66' ),
        ),
        'totals' => array(
            'totalBrutto' => '596,26',
            'totalNetto'  => '501,06',
            'vats' => array(
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
            'grandTotal'  => '665,89'
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
