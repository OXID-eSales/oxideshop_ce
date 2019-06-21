<?php
// Netto - Brutto start case, after order saving, switching to Netto - Netto, updating
$aData = array(
     'articles' => array(
         0 => array(
             'oxid'       => '111',
             'oxtitle'    => '111',
             'oxprice'    => 1,
             'oxvat'      => 19,
             'oxstock'    => 999,
             'amount'     => 1,
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
                    'oxaddsum' => 4.64,
                    'oxaddsumtype' => 'abs',
                    'oxdeltype' => 'p',
                    'oxfinalize' => 1,
                    'oxparamend' => 99999,
                ),
        ),
        'payment' => array(
                0 => array(
                    'oxaddsum' => 59.50,
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
                    // brut total
                    '111' => array( '1,19', '1,19' ),
            ),
            'totals' => array(
                    'totalBrutto' => '1,19',
                    'discount' => '0,12',
                    'totalNetto'  => '0,90',
                    'vats' => array(
                            19 => '0,17'
                    ),
                    'delivery' => array(
                            'brutto' => '4,64',
                    ),
                    'payment' => array(
                            'brutto' => '59,50',
                    ),
                    'grandTotal'  => '65,21',
            ),
        ),
        2 => array(
            'articles' => array(
                    // brut total
                    '111' => array( '1,19', '1,19' ),
            ),
            'totals' => array(
                    'totalBrutto' => '1,19',
                    'discount' => '0,12',
                    'totalNetto'  => '0,90',
                    'vats' => array(
                            19 => '0,17'
                    ),
                    'delivery' => array(
                            'brutto' => '4,64',
                    ),
                    'payment' => array(
                            'brutto' => '59,50',
                    ),
                    'grandTotal'  => '65,21',
            ),
        )
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
            ),
    ),
    'actions' => array(
        '_changeConfigs' => array(
            'blShowNetPrice' => true,
        ),
    ),
);
