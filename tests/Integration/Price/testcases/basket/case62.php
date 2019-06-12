<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 17%
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
 * Actions with basket or order:
 *   change config
 */
$aData = array(
    'skipped' => 1,
    'articles' => array(
        0 => array(
                'oxid'                     => 111,
                'oxprice'                  => 159.00,
                'oxvat'                    => 19,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_111',
                'oxaddsum'     => 20.00,
                'oxaddsumtype' => 'abs',
                'oxamount' => 2,
                'oxamountto' => 999,
                'oxprice' => 1,
                'oxpriceto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 111 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 111 => array( '159,00', '159,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '159,00',
                'totalNetto'  => '133,61',
                'vats' => array(
                    19 => '25,39',
                ),
                'grandTotal'  => '159,00'
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
