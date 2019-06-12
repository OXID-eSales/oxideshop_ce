<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 4
 * VAT info: count of used vat's =2 (19% and 11%)
 * Currency rate: 1.0
 * Discounts: 2
 *  1. itm discount for product (1002)
 *  2. 10% discount for basket
 *  ...
 * Vouchers: 1
 *  1. 10% voucher
 * Wrapping:  -
 * Gift cart: +
 * Costs VAT caclulation rule: proportional
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Short description: Calculate VAT according to the biggest net value  .
 * Neto-Neto mode. Additiona products Neto-Neto. Also is testing item discount for basket.
 * User is assignet to user group "priceA", for user groups is created two discount (itm, 10%) ;
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
                // oxarticles db fields
                'oxid'                     => 1001,
                'oxprice'                  => 20.00,
                'oxvat'                    => 11,
                // Amount in basket
                'amount'                   => 2,
                'scaleprices' => array(
                //        'oxaddabs'     => 0.00,
                        'oxamount'     => 2,
                        'oxamountto'   => 3,
                        'oxartid'      => 1001,
                        'oxaddperc'    => 10,
                ),
        ),
        1 => array(
         // oxarticles db fields
                'oxid'                     => 1002,
                'oxprice'                  => 200.00,
                'oxvat'                    => 19,
                // Amount in basket
                'amount'                   => 1,

        ),
        2 => array(
         // oxarticles db fields
                'oxid'                     => 1004,
                'oxprice'                  => 200.00,
                'oxvat'                    => 19,
    ),
    ),


    // User
    'user' => array(
            'oxactive' => 1,
            'oxusername' => 'basketUser',
            // country id, for example this is United States, make sure country with specified ID is active
        //    'oxcountryid' => '8f241f11096877ac0.98748826',
    ),
    // Group
    'group' => array(
            0 => array(
                    'oxid' => 'oxidpricea',
                    'oxactive' => 1,
                    'oxtitle' => 'Price A',
                    'oxobject2group' => array(
                            'oxobjectid' => array( 1001, 'basketUser' ),
                            'oxobjectid' => array( 1002, 'basketUser' ),
                            'oxobjectid' => array( 'itmdiscount', 'basketUser' ),
                            'oxobjectid' => array( '%discount', 'basketUser' ),
                    ),
            ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => '%discount',
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
        1 => array(
                // item discount for basket
            'oxid'         => 'itmdiscount',
            'oxaddsum'     => 0,
            'oxaddsumtype' => 'itm',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxitmartid' => 1004,
            'oxitmamount' => 1,
            'oxitmultiple' => 1,
            'oxarticles' => array( 1002 ),
            'oxsort' => 20,
        ),
    ),
    // Additional costs
    'costs' => array(
        // oxwrapping db fields
        'wrapping' => array(
           0 => array(
                'oxtype' => 'CARD',
                'oxname' => 'testCard1001',
                'oxprice' => 2.50,
                'oxactive' => 1,
            ),
        ),
        // Delivery
        'delivery' => array(
              0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 55.00,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
             0 => array(
                // oxpayments DB fields
                'oxaddsum' => 55.00,
                'oxaddsumtype' => '%',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
                'oxaddsumrules'=>1,
            ),
        ),
        // VOUCHERS
        'voucherserie' => array(
                 0 => array(
                'oxdiscount' => 10.00,
                'oxdiscounttype' => '%',
                'oxallowsameseries' => 1,
                'oxallowotherseries' => 1,
                'oxallowuseanother' => 1,
                'voucher_count' => 1
            ),
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1001 => array( '18,00', '36,00' ),
             1002 => array( '200,00', '200,00' ),
             1004 => array( '0,00', '0,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '225,15',
            // Total NETTO
            'totalNetto'  => '236,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '30,78',
                11 => '3,21',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                '%discount' => '23,60',
            ),
            // Total giftcard amounts
           'giftcard' => array(
                'brutto' => '2,94',
                'netto' => '2,50',
                'vat' => '0,44'
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '152,88',
                'netto' => '129,80',
                'vat' => '23,08'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '152,88',
                'netto' => '129,80',
                'vat' => '23,08'
            ),
            // Total voucher amounts
            'voucher' => array(
                'brutto' => '21,24',
            ),
            // GRAND TOTAL
            'grandTotal'  => '533,85'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
           'blEnterNetPrice' => true,
            'blShowNetPrice' => true,
            'blShowVATForDelivery'=> true,
            'blShowVATForPayCharge'=> true,
            'blShowVATForWrapping'=> true,
            'sAdditionalServVATCalcMethod' => 'proportional',
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
