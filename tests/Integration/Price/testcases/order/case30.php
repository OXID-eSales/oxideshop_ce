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
     ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount15fo678',
                    'oxaddsum'     => 15,
                    'oxaddsumtype' => '%',
                    'oxamount' => 1,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxarticles' => array( 111, ),
                    'oxsort' => 10,
            ),
    ),
    'costs' => array(
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
                    'oxaddsumrules'=>7,
                ),
        ),
    ),
    'expected' => array(
        1 => array(
            'articles' => array(
                    '111' => array( '40,86', '245,16' ),

                    ),
            'totals' => array(
                    'totalBrutto' => '283,28',
                    'discount' => '0,00',
                    'totalNetto'  => '245,16',
                    'vats' => array(
                            15.55 => '38,12',
                    ),
                    'delivery' => array(
                            'brutto' => '31,87',
                    ),
                    'payment' => array(
                            'brutto' => '31,87',
                    ),
                    'grandTotal'  => '347,02',
            ),
        ),
        2 => array(
            'articles' => array(
                    '111' => array( '40,86', '40,86' ),
                    ),
            'totals' => array(
                    'totalBrutto' => '47,21',
                    'discount' => '0,00',
                    'totalNetto'  => '40,86',
                    'vats' => array(
                            15.55 => '6,35',

                    ),
                    'delivery' => array(
                            'brutto' => '5,31',
                    ),
                    'payment' => array(
                            'brutto' => '5,31',
                    ),
                    'grandTotal'  => '57,83',
            ),
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
            ),
    ),
    'actions' => array(
            '_changeConfigs' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => true,
            ),

             '_changeArticles' => array(
             0 => array(
                      'oxid'       => '111',
                      'amount'     => 1,
            ),
            ),
    ),

);
