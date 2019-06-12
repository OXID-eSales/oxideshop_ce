<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 0.8
 * Discounts: count
 *  1. shop  15.00 abs for 111
 * Vouchers: -;
 * Wrapping: -;
 * Gift cart: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery  -
 *  3. TS -
 *
 * Case: 0004680: Discount recalculation fails on basket refresh
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => '_testProduct',
                'oxprice'                  => 10.00,
                'oxvat'                    => 19,
                'amount'                   => 31,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'basket_0',
                'oxaddsum'     => 6.00,
                'oxaddsumtype' => '%',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxprice' => 100,
                'oxpriceto' => 199,
                'oxactive' => 1,
                'oxarticles' => array( '_testProduct' ),
                'oxsort' => 10,
        ),
        1 => array(
                'oxid'         => 'basket_1',
                'oxaddsum'     => 9.00,
                'oxaddsumtype' => '%',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxprice' => 200,
                'oxpriceto' => 299,
                'oxactive' => 1,
                'oxarticles' => array( '_testProduct' ),
                'oxsort' => 20,
        ),
        2 => array(
                'oxid'         => 'basket_2',
                'oxaddsum'     => 12.00,
                'oxaddsumtype' => '%',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxprice' => 300,
                'oxpriceto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( '_testProduct' ),
                'oxsort' => 30,
        ),

    ),

    'expected' => array(
        'articles' => array(
                 '_testProduct' => array( '8,80', '272,80' ),
        ),
        'totals' => array(
                'totalBrutto' => '272,80',
                'totalNetto'  => '229,24',
                'vats' => array(
                    19 => '43,56'
                ),
                'grandTotal'  => '272,80'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
        ),
        'activeCurrencyRate' => 1,
    ),
);
