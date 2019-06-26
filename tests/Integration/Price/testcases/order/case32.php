<?php
/**
 * Price enter mode: bruto
 * Price view mode:  neto
 * Product count: count of used products
 * VAT info: 15.55
 * Discounts: product 15% discount
 * Wrapping: -;
 * Gift cart:  -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment 13%
 *  2. Delivery 13%
 *  3. TS -
 * Actions with order:
 *  1. update :changed products amounts
 */
$aData = array(
     'articles' => array(
             0 => array(
                     'oxid'       => '111',
                     'oxtitle'    => '111',
                     'oxprice'    => 55.55,
                     'oxvat'      => 15.55,
                     'oxstock'    => 999,
                     'amount'     => 6,
             ),
              1 => array(
                     'oxid'       => '222',
                     'oxtitle'    => '222',
                     'oxprice'    => 12.50,
                     'oxvat'      => 19,
                     'oxstock'    => 999,
                     'amount'     => 7,
             ),
     ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount15fo678',
                    'oxaddsum'     => 15,
                    'oxaddsumtype' => '%',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 111, 222 ),
                    'oxsort' => 10,
            ),
            1 => array(
                    'oxid'         => 'discountitm',
                    'oxaddsum'     => 0,
                    'oxaddsumtype' => 'itm',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxitmartid' => 1004,
                    'oxitmamount' => 1,
                    'oxitmultiple' => 2,
                    'oxarticles' => array( 111 ),
                    'oxsort' => 20,
                ),
    ),
    'costs' => array(
        'wrapping' => array(
                    0 => array(
                        'oxtype' => 'WRAP',
                        'oxname' => 'testWrap9001',
                        'oxprice' => 2.95,
                        'oxactive' => 1,
                        'oxarticles' => array( 111 )
                    ),
                    1 => array(
                        'oxtype' => 'WRAP',
                        'oxname' => 'testWrap9002',
                        'oxprice' => 2.95,
                        'oxactive' => 1,
                        'oxarticles' => array( 222 )
                    ),
                    2 => array(
                        'oxtype' => 'CARD',
                        'oxname' => 'testCard',
                        'oxprice' => 3,
                        'oxactive' => 1,
            ),
                ),
        'delivery' => array(
                    0 => array(
                            'oxactive' => 1,
                            'oxtitle' => 'Shipping costs for Example Set2: UPS 24 hrs Express: $12.90',
                            'oxaddsum' => 13,
                            'oxaddsumtype' => '%',
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
                    'oxaddsum' => 13,
                    'oxaddsumtype' => '%',
                    'oxfromamount' => 0,
                    'oxtoamount' => 1000000,
                    'oxchecked' => 1,
                    'oxaddsumrules'=>21,
                ),
        ),
    ),
    'expected' => array(
        1 => array(
            'articles' => array(
                    '111' => array( '40,86', '245,16' ),
                    '222' => array( '8,93', '62,51' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '357,67',
                    'discount' => '0,00',
                    'totalNetto'  => '307,67',
                    'vats' => array(
                            15.55 => '38,12',
                            19 => '11,88',
                    ),
                    'delivery' => array(
                            'brutto' => '40,00',
                            'neto' => '34,62',
                            'vat' => '5,38',

                    ),
                    'payment' => array(
                            'brutto' => '44,58',
                            'neto' => '38,58',
                            'vat' => '6,00',
                    ),
                    'grandTotal'  => '483,60',
            ),
        ),
        2 => array(
            'articles' => array(
                    '111' => array( '40,86', '122,58' ),
                    '222' => array( '8,93', '62,51' ),
                    ),
            'totals' => array(
                    'totalBrutto' => '216,03',
                    'discount' => '0,00',
                    'totalNetto'  => '185,09',
                    'vats' => array(
                            15.55 => '19,06',
                            19 => '11,88',

                    ),
                    'delivery' => array(
                            'brutto' => '24,06',
                            'neto' => '20,82',
                            'vat' => '3,24',

                    ),
                    'payment' => array(
                            'brutto' => '27,65',
                            'neto' => '23,92',
                            'vat' => '3,72',
                    ),
                    'grandTotal'  => '300,24',
            ),
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
                'blShowVATForDelivery'=> true,
                'blShowVATForPayCharge'=> true,
                'blShowVATForWrapping'=> true,
                'sAdditionalServVATCalcMethod' => 'biggest_net'

            ),
    ),
    'actions' => array(
            '_changeConfigs' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
            ),
             '_changeArticles' => array(
             0 => array(
                      'oxid'       => '111',
                      'amount'     => 3,
            ),
            ),
    ),

);
