<?php
/**
 * Price enter mode: netto
 * Price view mode:  netto
 * Product count: 9
 * VAT info: 19%
 * Currency rate: 1
 * Discounts: 0
 * Vouchers: 0
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: -
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Actions with basket or order:-
 * Short description: bodymed neto mode case;
 */
$aData = array(
    'articles' => array(
        0 => array(
            'oxid'                     => 1,
            'oxprice'                  => 24.72,
            'oxvat'                    => 7,
            'amount'                   => 2,
        ),
        1 => array(
            'oxid'                     => 2,
            'oxprice'                  => 14.57,
            'oxvat'                    => 7,
            'amount'                   => 1,
        ),
        2 => array(
            'oxid'                     => 3,
            'oxprice'                  => 1.49,
            'oxvat'                    => 7,
            'amount'                   => 5,
        ),
        3 => array(
            'oxid'                     => 4,
            'oxprice'                  => 1.65,
            'oxvat'                    => 7,
            'amount'                   => 5,
        ),
        4 => array(
            'oxid'                     => 5,
            'oxprice'                  => 17.06,
            'oxvat'                    => 7,
            'amount'                   => 1,
        ),
        5 => array(
            'oxid'                     => 6,
            'oxprice'                  => 1.63,
            'oxvat'                    => 7,
            'amount'                   => 6,
        ),
        6 => array(
            'oxid'                     => 7,
            'oxprice'                  => 21.57,
            'oxvat'                    => 7,
            'amount'                   => 1,
        ),
        7 => array(
            'oxid'                     => 8,
            'oxprice'                  => 21.57,
            'oxvat'                    => 7,
            'amount'                   => 1,
        ),
        8 => array(
            'oxid'                     => 9,
            'oxprice'                  => 24.44,
            'oxvat'                    => 7,
            'amount'                   => 1,
        ),
    ),

    'expected' => array(
        'articles' => array(
             1 => array( '24,72', '49,44' ),
             2 => array( '14,57', '14,57' ),
             3 => array( '1,49', '7,45' ),
             4 => array( '1,65', '8,25' ),
             5 => array( '17,06', '17,06' ),
             6 => array( '1,63', '9,78' ),
             7 => array( '21,57', '21,57' ),
             8 => array( '21,57', '21,57' ),
             9 => array( '24,44', '24,44' ),
        ),
        'totals' => array(
            'totalBrutto' => '186,32',
            'totalNetto'  => '174,13',
            'vats' => array(
                7 => '12,19'
            ),
            'grandTotal'  => '186,32'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => true,
        ),
        'activeCurrencyRate' => 1,
    ),
);
