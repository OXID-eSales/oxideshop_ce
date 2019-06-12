<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  bruto
 * Product count: 1
 * VAT info: 19% Default VAT for all Products ,
 * Currency rate: 1.0
 * Discounts: 1
 *  1. discount for basket
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: biggest_net
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * Netto - Netto start case, after order saving, added one product's,
 * updating, Netto - Brutto start case, after order saving, changed delivery price from 10 to 12eur. switching to Netto - Netto, updating
*/
# need to prepare integration test when after order is changed shipping method price from 10eur to 12 eur.
$aData = array(
'skipped' => 1,
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
                    'oxaddsum' => 10.00,
                    'oxaddsumtype' => 'abs',
                    'oxdeltype' => 'p',
                    'oxfinalize' => 1,
                    'oxparamend' => 99999,
                ),
        ),
        'payment' => array(
                0 => array(
                    'oxaddsum' => 10.00,
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
                            'brutto' => '10,00',
                    ),
                    'payment' => array(
                            'brutto' => '10,00',
                    ),
                    'grandTotal'  => '21,07',
            ),
        ),
        2 => array(
            'articles' => array(
                    // brut total
                    '111' => array( '1,19', '1,19' ),
                    '1111' => array( '4,17', '4,17' ),

            ),
            'totals' => array(
                    'totalBrutto' => '5,36',
                    'discount' => '0,54',
                    'totalNetto'  => '4,05',
                    'vats' => array(
                            19 => '0,77'
                    ),
              //      'delivery' => array(
              //              'brutto' => '12,00',
                //    ),
                    'payment' => array(
                            'brutto' => '10,00',
                    ),
                    'grandTotal'  => '26,82',
            ),
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => false,
                'blShowVATForDelivery'=> false,
                'blShowVATForPayCharge'=> false,
                'blShowVATForWrapping'=> false,
                'sAdditionalServVATCalcMethod' => 'biggest_net',
                'blDeliveryVatOnTop' => false,
                'blPaymentVatOnTop' => false,
                'blWrappingVatOnTop' => false,
            ),
    ),
    'actions' => array(
        '_changeConfigs' => array(
            'blShowNetPrice' => true,
        ),
        //  oxdelcost=>12
            '_addArticles' => array(
                    0 => array(
                            'oxid'       => '1111',
                            'oxtitle'    => '1111',
                            'oxprice'    => 3.50,
                            'oxvat'      => 19,
                            'oxstock'    => 999,
                            'amount' => 1,
                    ),
            ),


    ),
);
