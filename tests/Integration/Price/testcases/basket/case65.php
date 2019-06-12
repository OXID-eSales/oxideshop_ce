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
 * Case: cheking if corectrly currency rate applyed to discount
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 111,
                'oxprice'                  => 100.00,
                'oxvat'                    => 19,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_111',
                'oxaddsum'     => 15.00,
                'oxaddsumtype' => 'abs',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxprice' => 85,
                'oxpriceto' => 110,
                'oxactive' => 1,
                'oxarticles' => array( 111 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 111 => array( '68,00', '68,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '68,00',
                'totalNetto'  => '57,14',
                'vats' => array(
                    19 => '10,86',
                ),
                'grandTotal'  => '68,00'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
        ),
        'activeCurrencyRate' => 0.8,
    ),
);
