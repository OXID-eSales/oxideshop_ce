<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 1
 * VAT info: count of used vat's =2 (19% and 11%)
 * Currency rate: 1.0
 * Discounts: 1
 * 1. 10% discount for basket
 *  ...
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: +
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Short description:
 * Calculate VAT according to the max value  .
 * Neto-Neto mode. Additiona products Neto-Neto.
 * Scale price:2
 * 1. amount (2-3)
 * 2. amount (3-4)
 * For product (1002) is set parameter "free shipping" ;
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
                'amount'                   => 1,
                'scaleprices' => array(
                   0 => array(
                        'oxamount'     => 2,
                        'oxamountto'   => 3,
                        'oxartid'      => 1001,
                        'oxaddperc'    => 10,
                    ),
                    1 => array(
                        'oxamount'     => 3,
                        'oxamountto'   => 4,
                        'oxartid'      => 1001,
                        'oxaddperc'    => 20,
                    ),
                ),
        ),
        1 => array(
         // oxarticles db fields
                'oxid'                  => 1002,
                'oxprice'               => 200.00,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 1,
            'oxfreeshipping'        => 1,


        ),
     ),

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
      ),

    // Additional costs
    'costs' => array(

        // Delivery
        'delivery' => array(
              0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 10.00,
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
                'oxaddsum' => 10.00,
                'oxaddsumtype' => '%',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
        'oxaddsumrules'=>1,
            ),
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1001 => array( '20,00', '20,00' ),
             1002 => array( '200,00', '200,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '234,18',
            // Total NETTO
            'totalNetto'  => '220,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '34,20',
            11 => '1,98',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                '%discount' => '22,00',
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '2,38',
                'netto' => '2,00',
                'vat' => '0,38'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '26,18',
                'netto' => '22,00',
                'vat' => '4,18'
            ),

            // GRAND TOTAL
            'grandTotal'  => '262,74'
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
            'sAdditionalServVATCalcMethod' => 'biggest_net',
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
