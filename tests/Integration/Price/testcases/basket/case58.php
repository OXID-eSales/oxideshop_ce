<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 5
 * VAT info: 19% for all products
 * Currency rate: 1.0
 * Discounts: 1
 * 1. 10% discount for basket
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Scale price: for product (1001), product amount(3-5), then product price for product is 2.00eur.
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Short description:
 * Calculate VAT according to the max value  .
 * Netto-Netto mode. Additiona products Neto-Neto.
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
                // oxarticles db fields
                'oxid'                     => 1001,
                'oxprice'                  => 9.00,
                'oxvat'                    => 19,
                // Amount in basket
                'amount'                   => 4,
                    'scaleprices' => array(
                        'oxaddabs'     => 2.00,
                        'oxamount'     => 3,
                        'oxamountto'   => 5,
                        'oxartid'      => 1001,
                    //	'oxaddperc'    => 10,

                ),
        ),
        1 => array(
         // oxarticles db fields
                'oxid'                  => 1002,
                'oxprice'               => 5.52,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 1,
        ),
        2 => array(
         // oxarticles db fields
                'oxid'                  => 1003,
                'oxprice'               => 945.95,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 1,
        ),
        3 => array(
         // oxarticles db fields
                'oxid'                  => 1004,
                'oxprice'               => 4.74,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 1,
        ),
        4 => array(
         // oxarticles db fields
                'oxid'                  => 1005,
                'oxprice'               => 1.00,
                'oxvat'                 => 19,
                // Amount in basket
                'amount'                => 5,
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
                'oxaddsum' => 7.50,
                'oxaddsumtype' => 'abs',
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
             1001 => array( '2,00', '8,00' ),
             1002 => array( '5,52', '5,52' ),
             1003 => array( '945,95', '945,95' ),
             1004 => array( '4,74', '4,74' ),
             1005 => array( '1,00', '5,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.038,02',
            // Total NETTO

            'totalNetto'  => '969,21',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '165,73',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                '%discount' => '96,92',
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '115,33',
                'netto' => '96,92',
                'vat' => '18,41'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '8,93',
                'netto' => '7,50',
                'vat' => '1,43'
            ),

            // GRAND TOTAL
            'grandTotal'  => '1.162,28'
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
