<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 1.0
 * Discounts: count
 *  1. shop  20.00 abs for 111
 * Vouchers: -;
 * Wrapping: -;
 * Gift cart: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery  -
 *  3. TS -
 */
$aData = array(
    'skipped' => 1,
    'skipped' => 1, // while not fixed 0004631: Discount quantity and purchase price filters are working incorrectly

    'articles' => array(
        0 => array(
                'oxid'                     => 111,
                'oxprice'                  => 159.00,
                'oxvat'                    => 19,
                'amount'                   => 2,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_111',
                'oxaddsum'     => 20.00,
                'oxaddsumtype' => 'abs',
                'oxamount' => 2,
                'oxamountto' => 99,
                'oxprice' => 1,
                'oxpriceto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 111 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 111 => array( '139,00', '278,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '278,00',
                'totalNetto'  => '233,61',
                'vats' => array(
                    19 => '44,39',
                ),
                'grandTotal'  => '278,00'
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
