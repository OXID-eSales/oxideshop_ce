<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 4;
 * VAT info:  count of used vat =2(19% and 17%);
 * Currency rate:1;
 * Discounts: 4
 *  1.  2% discount for product (9200)
 *  2.  3% discount for product (9201)
 *  3.  4% discount for product (9207)
 *  4.  6% discount for product (9213)
 * Wrapping:  1;
 *  1.  0.05 wrapping for product's (9200, 9207, 9213)
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
 * From basketCalc.csv: Complex order calculation order VI.
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 9200,
                    'oxprice'                  => 87,
                    'oxvat'                    => 17,
                    'amount'                   => 589,
            ),
            1 => array(
                    'oxid'                     => 9201,
                    'oxprice'                  => 72.85,
                    'oxvat'                    => 17,
                    'amount'                   => 1325,
            ),
            2 => array(
                    'oxid'                     => 9207,
                    'oxprice'                  => 45.50,
                    'oxvat'                    => 19,
                    'amount'                   => 8888,
            ),
            3 => array(
                    'oxid'                     => 9213,
                    'oxprice'                  => 30.77,
                    'oxvat'                    => 19,
                    'amount'                   => 10000,
            ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount2for9200',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9200 ),
                    'oxsort' => 10,
            ),
            1 => array(
                    'oxid'         => 'discount3for9201',
                    'oxaddsum'     => 3,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9201 ),
                    'oxsort' => 20,
            ),
            2 => array(
                    'oxid'         => 'discount4for9207',
                    'oxaddsum'     => 4,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9207 ),
                    'oxsort' => 30,
            ),
            3 => array(
                    'oxid'         => 'discount6for9213',
                    'oxaddsum'     => 6,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9213 ),
                    'oxsort' => 40,
            ),
    ),
    'costs' => array(
            'wrapping' => array(
                    0 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9200',
                            'oxprice' => 0.05,
                            'oxactive' => 1,
                            'oxarticles' => array( 9200, 9207, 9213 )
                    ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 58.14,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 9999999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                9200 => array( '85,26', '50.218,14' ),
                9201 => array( '70,66', '93.624,50' ),
                9207 => array( '43,68', '388.227,84' ),
                9213 => array( '28,92', '289.200,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '821.270,48',
                'totalNetto'  => '692.209,52',
                'vats' => array(
                        17 => '20.900,21',
                        19 => '108.160,75'
                ),
                'wrapping' => array(
                        'brutto' => '973,85',
                        'netto' => '818,79',
                        'vat' => '155,06'
                ),
                'delivery' => array(
                        'brutto' => '58,14',
                        'netto' => '48,86',
                        'vat' => '9,28'
                ),
                'grandTotal'  => '822.302,47'
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
