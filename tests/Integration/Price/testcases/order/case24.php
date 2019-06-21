<?php
/**
 * Price enter mode: bruto
 * Price view mode:  neto
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
                     'oxid'       => '666',
                     'oxtitle'    => '666',
                     'oxprice'    => 1002.55,
                     'oxvat'      => 21,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
             1 => array(
                     'oxid'       => '777',
                     'oxtitle'    => '777',
                     'oxprice'    => 11.56,
                     'oxvat'      => 17,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
            2 => array(
                     'oxid'       => '888',
                     'oxtitle'    => '888',
                     'oxprice'    => 1326.89,
                     'oxvat'      => 14,
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
            1 => array(
                    'oxid'         => 'discount15fo678',
                    'oxaddsum'     => 15,
                    'oxaddsumtype' => '%',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 666, 777, 888 ),
                    'oxsort' => 20,
            ),
    ),
    'costs' => array(
        'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxtitle' => 'Shipping costs for Example Set2: UPS 24 hrs Express: $12.90',
                            'oxaddsum' => 5.0,
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
                    'oxaddsum' => 5.00,
                    'oxaddsumtype' => 'abs',
                    'oxfromamount' => 0,
                    'oxtoamount' => 1000000,
                    'oxchecked' => 1,
                ),
        ),
    ),
    'expected' => array(
        1 => array(
            'articles' => array(
                    '666' => array( '852,17', '5.113,02' ),
                    '777' => array( '9,83', '58,98' ),
                    '888' => array( '1.127,86', '6.767,16' ),
                    '444' => array( '6,66', '39,96' ),
                    '555' => array( '0,66', '3,96' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '11.983,08',
                    'discount' => '1.198,31',
                    'totalNetto'  => '9.224,35',
                    'vats' => array(
                            21 => '798,65',
                            17 => '12,94',
                            14 => '747,95',
                            33 => '0,88',
                    ),
                    'delivery' => array(
                            'brutto' => '5,00',
                    ),
                    'payment' => array(
                            'brutto' => '5,00',
                    ),
                    'grandTotal'  => '10.794,77',
            ),
        ),
        2 => array(
            'articles' => array(
                    '111' => array( '1.002,55', '4.010,20' ),
                    '222' => array( '11,56', '46,24' ),
                    '333' => array( '1.326,89', '5.307,56' ),
                    '444' => array( '6,66', '26,64' ),
                    '555' => array( '0,66', '2,64' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '9.393,28',
                    'discount' => '939,33',
                    'totalNetto'  => '7.729,70',
                    'vats' => array(
                            19 => '576,26',
                            13 => '4,79',
                            3 => '139,13',
                            17 => '3,48',
                            33 => '0,59',
                    ),
                    'delivery' => array(
                            'brutto' => '5,00',
                    ),
                    'payment' => array(
                            'brutto' => '5,00',
                    ),
                    'grandTotal'  => '8.463,95',
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
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
            ),
             '_removeArticles' => array( '666', '777', '888' ),
             '_changeArticles' => array(
             0 => array(
                      'oxid'       => '444',
                      'amount'     => 4,
            ),
             1 => array(
                      'oxid'       => '555',
                      'amount'     => 4,
            ),
            ),
             '_addArticles' => array(
            0 => array(
                     'oxid'       => '111',
                     'oxtitle'    => '111',
                     'oxprice'    => 1002.55,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 4,
             ),
             1 => array(
                     'oxid'       => '222',
                     'oxtitle'    => '222',
                     'oxprice'    => 11.56,
                     'oxvat'      => 13,
                     'oxstock'    => 999,
                     'amount'     => 4,
             ),
             2 => array(
                     'oxid'       => '333',
                     'oxtitle'    => '333',
                     'oxprice'    => 1326.89,
                     'oxvat'      => 3,
                     'oxstock'    => 999,
                     'amount'     => 4,
             ),
            ),

    ),

);
