<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 4;
 * VAT info:  count of used vat =1(19%);
 * Currency rate:1;
 * Discounts: 4
 *  1.  1% discount for product (9202)
 *  2.  2% discount for product (9210)
 * Wrapping:  1;
 *  1.  1.5 wrapping for product's (9215)
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
 * From basketCalc.csv: Complex order calculation order III.
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 9202,
                    'oxprice'                  => 16.48,
                    'oxvat'                    => 19,
                    'amount'                   => 190,
            ),
            1 => array(
                    'oxid'                     => 9210,
                    'oxprice'                  => 27.35,
                    'oxvat'                    => 19,
                    'amount'                   => 255,
            ),
            2 => array(
                    'oxid'                     => 9213,
                    'oxprice'                  => 30.77,
                    'oxvat'                    => 19,
                    'amount'                   => 14,
            ),
            3 => array(
                    'oxid'                     => 9215,
                    'oxprice'                  => 69.13,
                    'oxvat'                    => 19,
                    'amount'                   => 10,
            ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount1for9202',
                    'oxaddsum'     => 1,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9202 ),
                    'oxsort' => 10,
            ),
            1 => array(
                    'oxid'         => 'discount2for9210',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9210 ),
                    'oxsort' => 20,
            ),
    ),
    'costs' => array(
            'wrapping' => array(
                    0 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9215',
                            'oxprice' => 1.5,
                            'oxactive' => 1,
                            'oxarticles' => array( 9215 )
                    ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 58.49,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                9202 => array( '16,32', '3.100,80' ),
                9210 => array( '26,80', '6.834,00' ),
                9213 => array( '30,77', '430,78' ),
                9215 => array( '69,13', '691,30' ),
        ),
        'totals' => array(
                'totalBrutto' => '11.056,88',
                'totalNetto'  => '9.291,50',
                'vats' => array(
                        19 => '1.765,38',
                ),
                'wrapping' => array(
                        'brutto' => '15,00',
                        'netto' => '12,61',
                        'vat' => '2,39'
                ),
                'delivery' => array(
                        'brutto' => '58,49',
                        'netto' => '49,15',
                        'vat' => '9,34'
                ),
                'grandTotal'  => '11.130,37'
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
