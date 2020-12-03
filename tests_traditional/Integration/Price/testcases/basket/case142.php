<?php
/*
 * Price enter mode: brutto;
 * Price view mode: brutto;
 * Product count: 4;
 * VAT info:  count of used vat =2(19% and additional Vat for product 17%);
 * Currency rate:1-;
 * Discounts: -;
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Wrapping: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery- ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * From basketCalc.csv: I order
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 87.00,
                'oxvat'                    => 17,
                'amount'                   => 63,
        ),
        1 => array(
                'oxid'                     => 9201,
                'oxprice'                  => 72.85,
                'oxvat'                    => 17,
                'amount'                   => 48,
        ),
        2 => array(
                'oxid'                     => 9206,
                'oxprice'                  => 103.00,
                'oxvat'                    => 19,
                'amount'                   => 99,
        ),
        3 => array(
                'oxid'                     => 9216,
                'oxprice'                  => 56.45,
                'oxvat'                    => 17,
                'amount'                   => 22,
        ),
    ),
    'expected' => array(
        'articles' => array(
                // article id => [ unit price, total price = unit * amount - discounts ]
                9200 => array( '87,00', '5.481,00' ),
                9201 => array( '72,85', '3.496,80' ),
                9206 => array( '103,00', '10.197,00' ),
                9216 => array( '56,45', '1.241,90' )
        ),
        'totals' => array(
                'totalBrutto' => '20.416,70',
                'totalNetto'  => '17.303,70',
                'vats' => array(
                        '17' => '1.484,91',// vat abs sum 3113,00
                        '19' => '1.628,09'
                ),
                'grandTotal'  => '20.416,70'
        ),
    ),
    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false
        ),
    )
);
