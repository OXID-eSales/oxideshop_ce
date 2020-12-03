<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 4;
 * VAT info:  count of used vat =1(17%);
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
 * From basketCalc.csv: Complex order calculation order II.
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 59.16,
                'oxvat'                    => 17,
                'amount'                   => 1002,
        ),
        1 => array(
                'oxid'                     => 9201,
                'oxprice'                  => 49.54,
                'oxvat'                    => 17,
                'amount'                   => 1,
        ),
        3 => array(
                'oxid'                     => 9202,
                'oxprice'                  => 11.02,
                'oxvat'                    => 17,
                'amount'                   => 5,
        ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount5for9200',
                    'oxaddsum'     => 5,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9200 ),
                    'oxsort' => 10,
            ),
    ),
    'costs' => array(
            'wrapping' => array(
                    0 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9201',
                            'oxprice' => 8,
                            'oxactive' => 1,
                            'oxarticles' => array( 9201 )
                    ),
                    1 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9202',
                            'oxprice' => 0.7,
                            'oxactive' => 1,
                            'oxarticles' => array( 9202 )
                    ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 14.75,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                 9200 => array( '38,22', '38.296,44' ),
                 9201 => array( '33,69', '33,69' ),
                 9202 => array( '7,49', '37,45' ),
        ),
        'totals' => array(
                'totalBrutto' => '38.367,58',
                'totalNetto'  => '32.792,80',
                'vats' => array(
                        17 => '5.574,78',
                ),
                'wrapping' => array(
                        'brutto' => '7,84',
                        'netto' => '6,70',
                        'vat' => '1,14'
                ),
                'delivery' => array(
                        'brutto' => '10,03',
                        'netto' => '8,57',
                        'vat' => '1,46'
                ),
                'grandTotal'  => '38.385,45'
        ),
    ),
    'options' => array(
        'activeCurrencyRate' => 0.68,
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => true,
                'blShowVATForDelivery' => true,
        ),
    ),
);
