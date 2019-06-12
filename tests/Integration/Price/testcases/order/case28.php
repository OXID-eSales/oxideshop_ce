<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  netto
 * Product count: 5
 * VAT info: 19% Default VAT for all Products , additional vat for product (33% and 50%)
 * Currency rate: 1.0
 * Discounts: 1
 *  1. discount for basket 10%
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: biggest_net
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * Netto - Netto start case, after order saving, added one product's,
 * updating, Netto - Netto start case, after order saving,  switching mode to Netto - Brutto, add additional article(11121) updating
*/
// Simple order saving, add article, updating
$aData = array(
     // data arrays
     'articles' => array(
            0 => array(
                     'oxid'       => '111',
                     'oxtitle'    => '111',
                     'oxprice'    => 0.55,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
            1 => array(
                     'oxid'       => '1112',
                     'oxtitle'    => '1112',
                     'oxprice'    => 1101.10,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
            2 => array(
                     'oxid'       => '1113',
                     'oxtitle'    => '1113',
                     'oxprice'    => 110.00,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
            3 => array(
                     'oxid'       => '1114',
                     'oxtitle'    => '1114',
                     'oxprice'    => 1.00,
                     'oxvat'      => 33,
                     'oxstock'    => 999,
                     'amount'     => 1,
             ),
            4 => array(
                     'oxid'       => '1115',
                     'oxtitle'    => '1115',
                     'oxprice'    => 945.95,
                     'oxvat'      => 50,
                     'oxstock'    => 999,
                     'amount'     => 2,
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
                        'oxtitle' => 'Shipping costs for Example Set2:55.00%',
                        'oxaddsum' => 55.00,
                        'oxaddsumtype' => '%',
                        'oxdeltype' => 'p',
                        'oxfinalize' => 1,
                        'oxparamend' => 99999,
                ),
        ),
        'payment' => array(
                0 => array(
                        'oxid' => 'oxidpayadvance',
                        'oxdesc' => 'Cash in advance',
                        'oxaddsum' => 55,
                        'oxaddsumtype' => 'abs',
                        'oxfromamount' => 0,
                        'oxtoamount' => 1000000,
                        'oxchecked' => 1,
                        'oxactive' => 1,
                ),
        ),
    ),
    'expected' => array(
        1 => array(
            'articles' => array(
                    '111' => array( '0,55', '0,55' ),
                    '1112' => array( '1.101,10', '1.101,10' ),
                    '1113' => array( '110,00', '110,00' ),
                    '1114' => array( '1,00', '1,00' ),
                    '1115' => array( '945,95', '1.891,90' ),
            ),
            'totals' => array(
                    'totalBrutto' => '4.005,61',
                    'discount' => '310,46',
                    'totalNetto'  => '3.104,55',
                    'vats' => array(
                            33 => '360,16',
                            50 => '851,36',
                    ),
                    'delivery' => array(
                            'brutto' => '2.561,25',
                            'netto' => '1.707,50',
                            'vat' => '853,75'
                    ),
                    'payment' => array(
                           'brutto' => '82,50',
                           'netto' => '55,00',
                           'vat' => '27,50'
                    ),
                    'grandTotal'  => '6.649,36',
            ),
        ),
        2 => array(
                'articles' => array(
                    '111' => array( '0,55', '0,55' ),
                    '1112' => array( '1.101,10', '1.101,10' ),
                    '1113' => array( '110,00', '110,00' ),
                    '1114' => array( '1,00', '1,00' ),
                    '1115' => array( '945,95', '1.891,90' ),
                    '11121' => array( '3,50', '3,50' ),
            ),
            'totals' => array(
                    'totalBrutto' => '4.009,36',
                    'discount' => '310,81',
                    'totalNetto'  => '3.108,05',
                    'vats' => array(
                            33 => '360,16',
                            50 => '851,36',
                            19 => '0,60',
                    ),
                    'delivery' => array(
                            'brutto' => '2.564,15',
                            'netto' => '1.709,43',
                            'vat' => '854,72'
                    ),
                    'payment' => array(
                           'brutto' => '82,50',
                           'netto' => '55,00',
                           'vat' => '27,50'
                    ),
                    'grandTotal'  => '6.656,01',
            ),
        ),
    ),
    'options' => array(
                'config' => array(
                   'blEnterNetPrice' => true,
                   'blShowNetPrice' => true,
                   'blShowVATForPayCharge' => true,
                   'blShowVATForDelivery' => true,
                   'sAdditionalServVATCalcMethod' => 'biggest_net',
                   'blPaymentVatOnTop'=>true,
                   'blDeliveryVatOnTop'=>true,
                   'blPaymentVatOnTop'=>true,
                ),
    ),
    'actions' => array(
            '_changeConfigs' => array(
                    'blShowNetPrice' => false,
            ),
            '_addArticles' => array(
                    0 => array(
                            'oxid'       => '11121',
                           'oxtitle'    => '11121',
                           'oxprice'    => 3.50,
                           'oxvat'      => 19,
                           'oxstock'    => 999,
                            'amount' => 1,
                    ),
            ),

    ),
);
