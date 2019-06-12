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
 * Costs VAT caclulation rule: proportional
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Short description:
 * Calculate VAT according to the proportional value  .
 * Neto-Neto mode. Additiona products Neto-Neto.
 * Scale price:2
 * 1. if product amountis from 1 to 3 then is set 10% discount
 * For all product (1001, 1002) is set parameter "free shipping" . Then of delivery price should be =0.00 ;
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
                'oxfreeshipping'           => 1,
                'scaleprices' => array(
                        'oxamount'     => 1,
                        'oxamountto'   => 3,
                        'oxartid'      => 1001,
                        'oxaddperc'    => 10,

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
             1001 => array( '18,00', '36,00' ),
             1002 => array( '200,00', '200,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '250,16',
            // Total NETTO
            'totalNetto'  => '236,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '34,20',
                11 => '3,56',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                '%discount' => '23,60',
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '0,00',
            //    'netto' => '0,00',
            //    'vat' => '0,00'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '27,80',
                'netto' => '23,60',
                'vat' => '4,20'
            ),

            // GRAND TOTAL
            'grandTotal'  => '277,96'
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
