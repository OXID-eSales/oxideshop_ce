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
                     'oxid'       => '11',
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
           1 => array(
                'oxid'         => 'discount_for_prod',
                'oxaddsum'     => 15,
                'oxaddsumtype' => '%',
                'oxamount' => 0,
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
                    'oxaddsum' => 5.00,
                    'oxaddsumtype' => 'abs',
                    'oxdeltype' => 'p',
                    'oxfinalize' => 1,
                    'oxparamend' => 99999,
                    'shippingSetId' => 'oxidstandard'
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
                    '11' => array( '1.002,55', '2.005,10' ),
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
                            'brutto' => '5,00',
                    ),
                    'payment' => array(
                            'brutto' => '5,00',
                    ),
                    'grandTotal'  => '9.040,13',
            ),
        ),
        2 => array(
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
                            21 => '845,62',
                            17 => '12,94',
                            14 => '791,94',
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
             '_removeArticles' => array( '11', '222', '333' ),

             '_addArticles' => array(
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
            ),
    ),
);
