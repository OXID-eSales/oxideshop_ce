<?php
/*
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 17%
 * Currency rate: 1.47
 * Discounts: count
 *  1. shop  5.05 abs for 9201
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
 * Short description: From articlePrice.csv: article final price calculations. 9201 - 3rd
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9201,
                'oxprice'                  => 77.9,
                'oxvat'                    => 17,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_9201',
                'oxaddsum'     => 5.05,
                'oxaddsumtype' => 'abs',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 9201 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9201 => array( '107,09', '107,09' ),
        ),
        'totals' => array(
                'totalBrutto' => '107,09',
                'totalNetto'  => '91,53',
                'vats' => array(
                        17 => '15,56',
                ),
                'grandTotal'  => '107,09'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
        ),
        'activeCurrencyRate' => 1.47,
    ),
);
