<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =2(15% and 0%);
 * Currency rate:1;
 * Discounts: 4
 *  1.  4% discount for product (9213)
 *  2.  2% discount for product (9216)
 * Wrapping:  1;
 *  1.  2.30 wrapping for product's (9218)
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery + ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * From basketCalc.csv: Complex order calculation order IV.
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 9202,
                    'oxprice'                  => 15.93,
                    'oxvat'                    => 15,
                    'amount'                   => 58,
            ),
            1 => array(
                    'oxid'                     => 9208,
                    'oxprice'                  => 70.87,
                    'oxvat'                    => 15,
                    'amount'                   => 14,
            ),
            2 => array(
                    'oxid'                     => 9213,
                    'oxprice'                  => 25.86,
                    'oxvat'                    => 0,
                    'amount'                   => 1398,
            ),
            3 => array(
                    'oxid'                     => 9216,
                    'oxprice'                  => 48.25,
                    'oxvat'                    => 0,
                    'amount'                   => 250,
            ),
            4 => array(
                    'oxid'                     => 9218,
                    'oxprice'                  => 58.09,
                    'oxvat'                    => 15,
                    'amount'                   => 12,
            ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount4for9213',
                    'oxaddsum'     => 4,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9213 ),
                    'oxsort' => 10,
            ),
            1 => array(
                    'oxid'         => 'discount2for9216',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9216 ),
                    'oxsort' => 20,
            ),
    ),
    'costs' => array(
            'wrapping' => array(
                    0 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9218',
                            'oxprice' => 2.30,
                            'oxactive' => 1,
                            'oxarticles' => array( 9218 )
                    ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 12.82,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                9202 => array( '15,93', '923,94' ),
                9208 => array( '70,87', '992,18' ),
                9213 => array( '24,83', '34.712,34' ),
                9216 => array( '47,29', '11.822,50' ),
                9218 => array( '58,09', '697,08' ),
        ),
        'totals' => array(
                'totalBrutto' => '49.148,04',
                'totalNetto'  => '48.807,19',
                'vats' => array(
                        15 => '340,85',
                        0 => '0,00'
                ),
                'wrapping' => array(
                        'brutto' => '27,60',
                        'netto' => '24,00',
                        'vat' => '3,60'
                ),
                'delivery' => array(
                        'brutto' => '12,82',
                        'netto' => '12,82',
                ),
                'grandTotal'  => '49.188,46'
        ),
    ),
    'options' => array(
        'activeCurrencyRate' => 1,
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForDelivery' => true,
        ),
    ),
);
