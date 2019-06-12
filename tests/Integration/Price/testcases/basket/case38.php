<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 4
 * VAT info: count of used vat's =2 (19% and 11%)
 * Currency rate: 3.0
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
 * Short description: Calculate VAT according to the proportional value  .
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
             1001 => array( '54,00', '108,00' ),
             1002 => array( '600,00', '600,00' ),
             1004 => array( '0,00', '0,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '675,44',
            // Total NETTO
            'totalNetto'  => '708,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '92,34',
                11 => '9,62',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                '%discount' => '70,80',
            ),
            // Total giftcard amounts
           'giftcard' => array(
                'brutto' => '8,83',
                'netto' => '7,50',
                'vat' => '1,33'
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '458,63',
                'netto' => '389,40',
                'vat' => '69,23'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '458,63',
                'netto' => '389,40',
                'vat' => '69,23'
            ),
            // Total voucher amounts
            'voucher' => array(
                'brutto' => '63,72',
            ),
            // GRAND TOTAL
            'grandTotal'  => '1.601,53'
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
        'activeCurrencyRate' => 3,
    ),
);
