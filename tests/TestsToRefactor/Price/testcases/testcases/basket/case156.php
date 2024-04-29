<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =3(16%, 17% and 19%);
 * Currency rate:1;
 * Discounts: 4
 *  1.  2% discount for product (9201)
 *  2.  4% discount for product (9211)
 *  3.  2% discount for product (9216)
 * Wrapping:  1;
 *  1.  0.48 wrapping for product's (9219)
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
 * From basketCalc.csv: Complex order calculation order V.
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 9201,
                    'oxprice'                  => 72.85,
                    'oxvat'                    => 17,
                    'amount'                   => 175,
            ),
            1 => array(
                    'oxid'                     => 9203,
                    'oxprice'                  => 33.30,
                    'oxvat'                    => 19,
                    'amount'                   => 12,
            ),
            2 => array(
                    'oxid'                     => 9211,
                    'oxprice'                  => 5.86,
                    'oxvat'                    => 16,
                    'amount'                   => 5874,
            ),
            3 => array(
                    'oxid'                     => 9216,
                    'oxprice'                  => 56.45,
                    'oxvat'                    => 17,
                    'amount'                   => 225,
            ),
            4 => array(
                    'oxid'                     => 9219,
                    'oxprice'                  => 24.33,
                    'oxvat'                    => 19,
                    'amount'                   => 31,
            ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount2for9201',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9201 ),
                    'oxsort' => 10,
            ),
            1 => array(
                    'oxid'         => 'discount4for9211',
                    'oxaddsum'     => 4,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9211 ),
                    'oxsort' => 20,
            ),
            2 => array(
                    'oxid'         => 'discount2for9216',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 9216 ),
                    'oxsort' => 30,
            ),
    ),
    'costs' => array(
            'wrapping' => array(
                    0 => array(
                            'oxtype' => 'WRAP',
                            'oxname' => 'wrapFor9219',
                            'oxprice' => 0.48,
                            'oxactive' => 1,
                            'oxarticles' => array( 9219 )
                    ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 15.03,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                9201 => array( '71,39', '12.493,25' ),
                9203 => array( '33,30', '399,60' ),
                9211 => array( '5,63', '33.070,62' ),
                9216 => array( '55,32', '12.447,00' ),
                9219 => array( '24,33', '754,23' ),
        ),
        'totals' => array(
                'totalBrutto' => '59.164,70',
                'totalNetto'  => '50.795,22',
                'vats' => array(
                        16 => '4.561,46',
                        17 => '3.623,80',
                        19 => '184,22'
                ),
                'wrapping' => array(
                        'brutto' => '14,88',
                        'netto' => '12,50',
                        'vat' => '2,38'
                ),
                'delivery' => array(
                        'brutto' => '15,03',
                        'netto' => '12,96',
                        'vat' => '2,07'
                ),
                'grandTotal'  => '59.194,61'
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
