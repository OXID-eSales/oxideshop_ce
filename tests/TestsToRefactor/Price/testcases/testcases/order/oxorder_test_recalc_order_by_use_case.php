<?php
// Simple order saving, add article, updating
$aData = array(
     'articles' => array(
             0 => array(
                     'oxid'       => '1126',
                     'oxtitle'    => 'Bar-Set ABSINTH',
                     'oxprice'    => 34,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
             1 => array(
                     'oxid'       => '1127',
                     'oxtitle'    => 'Ice Cubes FLASH',
                     'oxprice'    => 8,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
     ),
    'categories' => array(
            0 =>  array(
                    'oxid'     => '30e44ab8593023055.23928895',
                    'oxactive' => 1,
                    'oxtitle'  => 'Bar-Equipment',
                    'oxarticles' => array( 1126 )
            ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => '_testDiscountForArticle',
                    'oxaddsum'     => 50,
                    'oxaddsumtype' => '%',
                    'oxamount'     => 1,
                    'oxamountto'   => 9999,
                    'oxactive'     => 1,
                    'oxarticles'   => array( 1126, 1127 ),
                    'oxsort'       => 10,
            ),
            1 => array(
                    'oxid'         => '_testDiscountForCategory',
                    'oxaddsum'     => 50,
                    'oxaddsumtype' => '%',
                    'oxamount'     => 1,
                    'oxamountto'   => 9999,
                    'oxactive'     => 1,
                    'oxcategories' => array( '30e44ab8593023055.23928895' ),
                    'oxsort'       => 20,
            ),
    ),
    'costs' => array(
            'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxtitle' => 'Shipping costs for Example Set2: UPS 24 hrs Express: $12.90',
                            'oxaddsum' => 12.9,
                            'oxaddsumtype' => 'abs',
                            'oxdeltype' => 'p',
                            'oxfinalize' => 1,
                            'oxparamend' => 99999,
                            'shippingSetId' => 'oxidstandard',
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
                    '1126' => array( '17,00', '17,00' ),
                    '1127' => array( '4,00', '4,00' ),
            ),
            'totals' => array(
                    'totalBrutto' => '21,00',
                    'discount' => '0,00',
                    'totalNetto'  => '17,65',
                    'vats' => array(
                            19 => '3,35'
                    ),
                    'delivery' => array(
                            'brutto' => '12,90',
                    ),
                    'payment' => array(
                            'brutto' => '0,00',
                    ),
                    'grandTotal'  => '33,90',
            ),
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
            ),
    ),
);
