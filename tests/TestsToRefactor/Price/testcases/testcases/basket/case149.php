<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =3(16%, 17%, 19%);
 * Currency rate: -;
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
 * From basketCalc.csv: V order.
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9201,
                'oxprice'                  => 72.85,
                'oxvat'                    => 17,
                'amount'                   => 1012,
        ),
        1 => array(
                'oxid'                     => 9203,
                'oxprice'                  => 33.30,
                'oxvat'                    => 19,
                'amount'                   => 453,
        ),
        2 => array(
                'oxid'                     => 9211,
                'oxprice'                  => 5.86,
                'oxvat'                    => 16,
                'amount'                   => 88,
        ),
        3 => array(
                'oxid'                     => 9216,
                'oxprice'                  => 56.45,
                'oxvat'                    => 17,
                'amount'                   => 56,
        ),
        4 => array(
                'oxid'                     => 9219,
                'oxprice'                  => 24.33,
                'oxvat'                    => 19,
                'amount'                   => 74,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9201 => array( '72,85', '73.724,20' ),
                 9203 => array( '33,30', '15.084,90' ),
                 9211 => array( '5,86', '515,68' ),
                 9216 => array( '56,45', '3.161,20' ),
                 9219 => array( '24,33', '1.800,42' ),
        ),
        'totals' => array(
                'totalBrutto' => '94.286,40',
                'totalNetto'  => '80.347,91',
                'vats' => array(
                        16 => '71,13',
                        17 => '11.171,38',
                        19 => '2.695,98',
                ),
                'grandTotal'  => '94.286,40'
        ),
    ),
    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false
        ),
    ),
);
