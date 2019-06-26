<?php
/**
 * Price enter mode: bruto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: 5 different VAT
 * Discounts: basket 10% discount
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Actions with order:
 *  1. update :changed products amounts
 */
$aData = array(
     'articles' => array(
             0 => array(
                     'oxid'       => '111',
                     'oxtitle'    => '111',
                     'oxprice'    => 1002.55,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 2,
             ),
             1 => array(
                     'oxid'       => '222',
                     'oxtitle'    => '222',
                     'oxprice'    => 11.56,
                     'oxvat'      => 13,
                     'oxstock'    => 999,
                     'amount'     => 2,
             ),
            2 => array(
                     'oxid'       => '333',
                     'oxtitle'    => '333',
                     'oxprice'    => 1326.89,
                     'oxvat'      => 3,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
            3 => array(
                     'oxid'       => '444',
                     'oxtitle'    => '444',
                     'oxprice'    => 6.66,
                     'oxvat'      => 17,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
            4 => array(
                     'oxid'       => '555',
                     'oxtitle'    => '555',
                     'oxprice'    => 0.66,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
     ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount10for111',
                    'oxaddsum'     => 10,
                    'oxaddsumtype' => '%',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxsort' => 10,
            ),
    ),
    'costs' => array(
        'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxtitle' => 'Shipping costs for Example Set2: UPS 24 hrs Express: $12.90',
                            'oxaddsum' => 00.0,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                            'oxsort' => '5000',
                            'oxfixed' => 0,
                            'oxdeliveryset' => array(
                                    'oxactive' => 1,
                                    'oxpos' => 30,
                                    'oxtitle' => 'Example Set2: UPS Express 24 hours',
                            )
                    ),
            ),
            'payment' => array(
                    0 => array(
                            'oxid' => 'oxidpayadvance',
                            'oxdesc' => 'Cash in advance',
                            'oxaddsum' => 0,
                            'oxaddsumtype' => 'abs',
                            'oxfromamount' => 0,
                            'oxtoamount' => 1000000,
                            'oxchecked' => 1,
                            'oxactive' => 1
                    ),
        ),
    ),
    'expected' => array(
        1 => array(
            'articles' => array(
                    '111' => array( '1.002,55', '2.005,10' ),
                    '222' => array( '11,56', '23,12' ),
                    '333' => array( '1.326,89', '7.961,34' ),
                    '444' => array( '6,66', '39,96' ),
                    '555' => array( '0,66', '3,96' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '10.033,48',
                    'discount' => '1.003,35',
                    'totalNetto'  => '8.524,80',
                    'vats' => array(
                            19 => '288,13',
                            13 => '2,39',
                            3 => '208,70',
                            17 => '5,23',
                            33 => '0,88',
                    ),
                    'delivery' => array(
                            'brutto' => '0,00',
                    ),
                    'payment' => array(
                            'brutto' => '0,00',
                    ),
                    'grandTotal'  => '9.030,13',
            ),
        ),
        2 => array(
            'articles' => array(
                    '111' => array( '1.002,55', '1.002,55' ),
                    '666' => array( '101,33', '202,66' ),
                    '777' => array( '11,66', '34,98' ),
                    '888' => array( '18,65', '74,60' ),
                    '999' => array( '0,55', '2,75' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '1.317,54',
                    'discount' => '131,75',
                    'totalNetto'  => '1.009,46',
                    'vats' => array(
                            19 => '144,06',
                            13 => '20,98',
                            3 => '0,92',
                            17 => '9,76',
                            33 => '0,61',
                    ),
                    'delivery' => array(
                            'brutto' => '0,00',
                    ),
                    'payment' => array(
                            'brutto' => '0,00',
                    ),
                    'grandTotal'  => '1.185,79',
            ),
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
            ),
    ),
    'actions' => array(
            '_changeConfigs' => array(
                    'blShowNetPrice' => false,
            ),
             '_removeArticles' => array( '222', '333', '444', '555' ),
             '_changeArticles' => array(
             0 => array(
                      'oxid'       => '111',
                      'amount'     => 1,
            ),
            ),
             '_addArticles' => array(
            0 => array(
                     'oxid'       => '666',
                     'oxtitle'    => '666',
                     'oxprice'    => 101.33,
                     'oxvat'      => 13,
                     'oxstock'    => 999,
                     'amount'     => 2,
             ),
             1 => array(
                     'oxid'       => '777',
                     'oxtitle'    => '777',
                     'oxprice'    => 11.66,
                     'oxvat'      => 3,
                     'oxstock'    => 999,
                     'amount'     => 3,
             ),
             2 => array(
                     'oxid'       => '888',
                     'oxtitle'    => '888',
                     'oxprice'    => 18.65,
                     'oxvat'      => 17,
                     'oxstock'    => 999,
                     'amount'     => 4,
             ),
             3 => array(
                     'oxid'       => '999',
                     'oxtitle'    => '999',
                     'oxprice'    => 0.55,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 5,
             ),
            ),
    ),
);
