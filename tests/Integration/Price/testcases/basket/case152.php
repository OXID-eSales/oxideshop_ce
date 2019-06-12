<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =1(17%, 18%,19%);
 * Currency rate: 1;
 * Discounts: -;
 * Wrapping:  1;
 *  1.  0.57 wrapping for product (9216)
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery +;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * From basketCalc.csv: Complex order calculation order I.
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 87,
                'oxvat'                    => 17,
                'amount'                   => 63,
        ),
        1 => array(
                'oxid'                     => 9206,
                'oxprice'                  => 103,
                'oxvat'                    => 19,
                'amount'                   => 125,
        ),
        3 => array(
                'oxid'                     => 9216,
                'oxprice'                  => 56.45,
                'oxvat'                    => 17,
                'amount'                   => 14,
        ),
        4 => array(
                'oxid'                     => 9218,
                'oxprice'                  => 59.60,
                'oxvat'                    => 18,
                'amount'                   => 39,
        ),
    ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'discount2for9200and9206',
            'oxaddsum'     => 2,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 9200, 9206 ),
            'oxsort' => 10,
        ),
    ),
    'costs' => array(
            'wrapping' => array(
                0 => array(
                    'oxtype' => 'WRAP',
                    'oxname' => 'wrapFor9216',
                    'oxprice' => 0.57,
                    'oxactive' => 1,
                    'oxarticles' => array( 9216 )
                ),
            ),
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxaddsum' => 15,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                    ),
            ),
    ),
    'expected' => array(
        'articles' => array(
                 9200 => array( '85,26', '5.371,38' ),
                 9206 => array( '100,94', '12.617,50' ),
                 9216 => array( '56,45', '790,30' ),
                 9218 => array( '59,60', '2.324,40' ),
        ),
        'totals' => array(
                'totalBrutto' => '21.103,58',
                'totalNetto'  => '17.839,16',
                'vats' => array(
                        17 => '895,29',
                        18 => '354,57',
                        19 => '2.014,56',
                ),
                'wrapping' => array(
                        'brutto' => '7,98',
                        'netto' => '6,82',
                        'vat' => '1,16'
                ),
                'delivery' => array(
                        'brutto' => '15,00',
                        'netto' => '12,61',
                        'vat' => '2,39'
                ),
                'grandTotal'  => '21.126,56'
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
