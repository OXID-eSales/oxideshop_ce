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
                'amount'                   => 36,
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
                //'oxarticles' => array( 111 ),
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
                'oxsort' => 30,
        ),

    ),

    'expected' => array(
        'articles' => array(
                 '_testProduct' => array( '10,00', '360,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '360,00',
                'totalNetto'  => '266,22',
                'vats' => array(
                    19 => '50,58',
                ),
                'discounts' => array(
                    'basket_2' => '43,20',
                ),
                'grandTotal'  => '316,80'
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
