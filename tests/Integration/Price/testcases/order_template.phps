<?php
// Simple order saving, add article, updating
$aData = array (
     // parameter for skipping testCase
     'skipped' => 1,
     // data arrays
     'articles' => array (
             0 => array (
                     'oxid'       => '111',
                     'oxtitle'    => '111',
                     'oxprice'    => 1,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
             1 => array (
                     'oxid'       => '222',
                     'oxtitle'    => '222',
                     'oxprice'    => 5.56,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 3,
             ),
     ),
    'discounts' => array (
            0 => array (
                    'oxid'         => 'discount10for111',
                    'oxaddsum'     => 10,
                    'oxaddsumtype' => '%',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
            ),
    ),
    'costs' => array (
        'delivery' => array (
                    0 => array (
                            'oxactive' => 1,
                            'oxtitle' => 'Shipping costs for Example Set2: UPS 24 hrs Express: $12.90',
                            'oxaddsum' => 12.9,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                            'oxsort' => '5000',
                            'oxfixed' => 0,
                            'oxdeliveryset' => array (
                                    'oxactive' => 1,
                                    'oxpos' => 30,
                                    'oxtitle' => 'Example Set2: UPS Express 24 hours',
                            )
                    ),
            ),
            'payment' => array (
                    0 => array (
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
    'expected' => array (
        1 => array (
            'articles' => array (
                    '111' => array ( '1,00', '1,00' ),
                    '222' => array ( '5,56', '16,68' ),
            ),
            'totals' => array (
                    'totalBrutto' => '18,93',
                    'discount' => '1,77',
                    'totalNetto'  => '17,68',
                    'vats' => array (
                            19 => '3,02'
                    ),
                    'delivery' => array(
                            'brutto' => '4,64',
                    ),
                    'payment' => array(
                            'brutto' => '59,50',
                    ),
                    'grandTotal'  => '83,07',
            ),
        ),
        2 => array (
            'articles' => array (
                    '11121' => array ( '4,17', '4,17' ),
                    '222'   => array ( '6,62', '6,62' ),
            ),
            'totals' => array (
                    'totalBrutto' => '9,99',
                    'discount' => '0,91',
                    'totalNetto'  => '9,06',
                    'vats' => array (
                            19 => '1,84'
                    ),
                    'delivery' => array(
                            'brutto' => '4,64',
                    ),
                    'payment' => array(
                            'brutto' => '59,50',
                    ),
                    'grandTotal'  => '74,13',
            ),
        )
    ),
    'options' => array (
            'config' => array (
                'blEnterNetPrice' => true,
                'blShowNetPrice' => true,
            ),
    ),
    'actions' => array (
            '_changeConfigs' => array (
                    'blShowNetPrice' => false,
            ),
            '_addArticles' => array (
                    0 => array(
                            'oxid'       => '11121',
                            'oxtitle'    => '11121',
                            'oxprice'    => 3.50,
                            'oxvat'      => 19,
                            'oxstock'    => 999,
                            'amount' => 1,
                    ),
            ),
            '_removeArticles' => array ( '111' ),
            '_changeArticles' => array (
                    0 => array(
                            'oxid'       => '222',
                            'amount'     => 1
                    ),
            ),
    ),
);