<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 3;
 * VAT info:  count of used vat =1(17%);
 * Currency rate: 0.68;
 * Discounts: -;
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery -;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * From basketCalc.csv: II order. With active currency rate. Rounding issue.
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 87.00,
                'oxvat'                    => 17,
                'amount'                   => 120,
        ),
        1 => array(
                'oxid'                     => 9201,
                'oxprice'                  => 72.85,
                'oxvat'                    => 17,
                'amount'                   => 5,
        ),
        2 => array(
                'oxid'                     => 9202,
                'oxprice'                  => 16.21,
                'oxvat'                    => 17,
                'amount'                   => 39,
        ),
    ),
    'expected' => array(
        'articles' => array(
                9200 => array( '59,16', '7.099,20' ),
                9201 => array( '49,54', '247,70' ),
                9202 => array( '11,02', '429,78' ),
        ),
        'totals' => array(
                'totalBrutto' => '7.776,68',
                'totalNetto'  => '6.646,74',
                'vats' => array(
                        '17' => '1.129,94',
                ),
                'grandTotal'  => '7.776,68'
        ),
    ),
    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false
        ),
        'activeCurrencyRate' => 0.68,
    ),
);
