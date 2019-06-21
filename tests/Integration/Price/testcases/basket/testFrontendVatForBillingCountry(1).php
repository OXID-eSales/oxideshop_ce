<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 5;
 * VAT info:  count of used vat =3(10%, 5% and 19%);
 * Currency rate:1;
 * Discounts: -;
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: biggest_net;
 * Gift cart:  -;
 * Vouchers: +;
 * Costs:
 *  1. Payment +;
 *  2. Delivery + ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * Short description: test added from selenium test (testFrontendVatForBillingCountry) ; Checking VAT functionality, when it is calculated for Billing country
 */
$aData = array(
    'articles' => array(
            0 => array(
                    'oxid'                     => 10011,
                    'oxprice'                  => 101,
                    'oxvat'                    => 10,
                    'amount'                   => 1,
                    'oxpricea'       		   => 0,
                    'oxpriceb' 			       => 0,
                    'oxpricec' 			       => 0,
            ),
       
            1 => array(
                    'oxid'                     => 1003,
                    'oxprice'                  => 75.00,
                    'oxvat'                    => 19,
                    'amount'                   => 1,
                    'oxpricea'       		   => 70,
                    'oxpriceb' 			       => 85,
                    'oxpricec' 			       => 0,
                    /*'scaleprices' => array(
                        'oxamount'     => 6,
                        'oxamountto'   => 999999,
                        'oxartid'      => 1003,
                        'oxaddperc'    => 20,
                    ),
                    */
            ),
            2 => array(
                    'oxid'                     => 1000,
                    'oxprice'                  => 50.00,
                    'oxvat'                    => 5,
                    'amount'                   => 1,
                    'oxpricea'       		   => 35,
                    'oxpriceb' 			       => 45,
                    'oxpricec' 			       => 55,
                    'oxunitname'               => 'kg',
                    'oxunitquantity'           => 2,
                    'oxweight'                 => 2
            ),

    ),
    
        // User
    'user' => array(
            'oxactive' => 1,
            'oxusername' => 'basketUser',
            // country id, for example this is Deutschland, make sure country with specified ID is active
            'oxcountryid' => 'a7c40f631fc920687.20179984',
    ),
    /*
    'discounts' => array (

            0 => array (
                    'oxid'         => 'discount2',
                    'oxaddsum'     => 5,
                    'oxaddsumtype' => 'abs',
                    'oxamount'     => 1,
                    'oxamountto'   => 99999,
                    'oxactive'     => 1,
                    'oxarticles'   => array ( 10011, 1000 ),
                //	'oxcountryid' => 'a7c40f631fc920687.20179984',
            ),
    ),
    */
    

    
    
    
    'costs' => array(
        'delivery' => array(
            0 => array(
                'oxactive' => 1,
                'oxaddsum' => 6.90,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            //	'oxcountryid' => 'a7c40f6321c6f6109.43859248',
            ),
        ),
                // Payment
        'payment' => array(
             0 => array(
                // oxpayments DB fields
                'oxaddsum' => 0.00,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
                'oxaddsumrules'=>0,
                'oxcountryid' => 'a7c40f631fc920687.20179984',
                
            ),
        ),
    ),
    'expected' => array(
        'articles' => array(
                10011 => array( '101,00', '101,00' ),
                1003 => array( '75,00', '75,00' ),
                1000 => array( '50,00', '50,00' ),

        ),
        'totals' => array(
                'totalBrutto' => '226,00',
                'totalNetto'  => '202,47',
                'vats' => array(
                        10 => '9,18',
                        19 => '11,97',
                        5 => '2,38'
                ),
                'delivery' => array(
                        'brutto' => '6,90',
                ),
                'grandTotal'  => '232,90'
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
