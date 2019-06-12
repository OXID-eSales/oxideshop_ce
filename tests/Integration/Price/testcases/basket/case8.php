<?php
/**
 * Price enter mode:  brutto
 * Price view mode: brutto
 * Product count: count of used products
 * VAT info: 17%
 * Currency rate: 1.47
 * Discounts: -;
 * Vouchers: -;
 * Wrapping: -
 * Gift cart: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery  -
 *  3. TS -
 * Actions with basket or order: -;
 * Short description: From articlePrice.csv: article final price calculations. 9200 - 3rd
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 87,
                'oxvat'                    => 17,
                'amount'                   => 1,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9200 => array( '127,89', '127,89' ),
        ),
        'totals' => array(
                'totalBrutto' => '127,89',
                'totalNetto'  => '109,31',
                'vats' => array(
                        17 => '18,58',
                ),
                'grandTotal'  => '127,89'
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
