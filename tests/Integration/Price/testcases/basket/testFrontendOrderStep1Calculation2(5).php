<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =2(10%, and 19%);
 * Currency rate:1;
 * Discounts: 2
 *  1.  10% discount for product (1002, 1003)
 *  2.  5abs discount for product (1001, 1000)
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: biggest_net;
 * Gift cart:  -;
 * Vouchers: +;
 * Costs:
 *  1. Payment -;
 *  2. Delivery + ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * Short description: test added from selenium test (testFrontendOrderStep1Calculation2) ;Is testing basked Step1 Calculation
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 10016,
                    'oxprice'                  => 101,
                    'oxvat'                    => 10,
                    'amount'                   => 1,
                    'oxpricea'       		   => 0,
                    'oxpriceb' 			       => 0,
                    'oxpricec' 			       => 0,
            ),
            1 => array(
                    'oxid'                     => 1002,
                    'oxprice'                  => 67.00,
                    'oxvat'                    => 19,
                    'amount'                   => 1,
            ),

    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount1',
                    'oxaddsum'     => 10,
                    'oxaddsumtype' => '%',
                    'oxamount'     => 0,
                    'oxamountto'   => 99999,
                    'oxprice'      =>100,
                    'oxpriceto'    =>99999,
                    'oxactive'     => 1,
                    'oxarticles'   => array( 1002, 1003 ),
                    'oxsort'       => 10,
            ),
            1 => array(
                    'oxid'         => 'discount2',
                    'oxaddsum'     => 5,
                    'oxaddsumtype' => 'abs',
                    'oxamount'     => 1,
                    'oxamountto'   => 99999,
                    'oxactive'     => 1,
                    'oxarticles'   => array( 10016, 1000 ),
                    'oxsort'       => 20,
            ),
    ),

    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxactive' => 1,
                'oxaddsum' => 1.50,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
                   // VOUCHERS
        'voucherserie' => array(
            0 => array(
                // oxvoucherseries DB fields
                'oxdiscount' => 5.00,
                'oxdiscounttype' => '%',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                'oxminimumvalue' =>75,
                // voucher of this voucherserie count
                'voucher_count' => 1
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
                10016 => array( '96,00', '96,00' ),
                //Discount is not used because product price is <100
                1002 => array( '67,00', '67,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '163,00',
                'totalNetto'  => '136,40',
                'vats' => array(
                        10 => '8,29',
                        19 => '10,16',
                ),
                'delivery' => array(
                        'brutto' => '1,50',
                ),
                // Total voucher amounts
                'voucher' => array(
                'brutto' => '8,15',
                ),
                'grandTotal'  => '156,35'
        ),
    ),
    'options' => array(
        'activeCurrencyRate' => 1,
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
                'blShowVATForWrapping' => false,
                'blShowVATForDelivery' => false,
                'sAdditionalServVATCalcMethod' => 'biggest_net',
        ),
    ),
);
