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
 * Case: 0004462: Incorrect calculation when entered basket item and basket discounts
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 111,
                'oxprice'                  => 120.00,
                'oxvat'                    => 20,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'discount_for_111',
                'oxaddsum'     => 50.00,
                'oxaddsumtype' => '%',
                'oxamount' => 1,
                'oxamountto' => 99999,
                'oxprice' => 0,
                'oxpriceto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 111 ),
                'oxsort' => 10,
        ),
        1 => array(
                'oxid'         => 'basket',
                'oxaddsum'     => 50.00,
                'oxaddsumtype' => '%',
                'oxamount' => 1,
                'oxamountto' => 99999,
                'oxprice' => 0,
                'oxpriceto' => 99999,
                'oxactive' => 1,
                'oxsort' => 20,
        ),

    ),

    'expected' => array(
        'articles' => array(
                 111 => array( '60,00', '60,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '60,00',
                'totalNetto'  => '25,00',
                'vats' => array(
                    20 => '5,00',
                ),
                'discounts' => array(
                    'basket' => '30,00',
                ),
                'grandTotal'  => '30,00'
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
