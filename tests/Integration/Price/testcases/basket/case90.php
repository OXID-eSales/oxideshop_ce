<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 5
 * VAT info: count of used vat =2(19% and 55%)
 * Currency rate: 1.00
 * Discounts: 3
 *  1.  10% discount for basket
 *  2. -10% discount for product 111, 1114
 *  3. -5.2% discount for product 1112
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 * Vat and rounding issue. 5 products. two different VAT(for one the product is set spec.
 * VAT=55%, for all other products VAT=19%),  two discount for product, one discount for basket.  Neto-Neto mode.
 */

$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 0.5,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 3,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 100.55,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),

        2 => array(
         // oxarticles db fields-5.2
            'oxid'                     => 1113,
            'oxprice'                  => 0.9,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 3,
        ),
        3 => array(
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 5.02,
            'oxvat'                    => 55,
            // Amount in basket
            'amount'                   => 1,
        ),
        4 => array(
         // oxarticles db fields
            'oxid'                     => 1115,
            'oxprice'                  => 0.50,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // 10% discount for basket
            'oxid'         => 'discountforbasket10%',
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
         1 => array(
            // -10% discount for product 111, 1114
            'oxid'         => 'procdiscountfor111',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 111, 1114 ),
            'oxsort' => 20,
        ),


        2 => array(
            //-5.2% discount for product 1112
            'oxid'         => 'discountforbasket1112',
            'oxaddsum'     => -5.2,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1112),
        ),
    ),
    // Additional costs
    'costs' => array(

        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 50,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                 'oxparam'=> 0.1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 50,
                'oxaddsumtype' => 'abs',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
            ),
        ),

    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             111 => array( '0,55', '1,65' ),
             1112 => array( '105,78', '105,78' ),
             1113 => array( '0,90', '2,70' ),
             1114 => array( '5,52', '5,52' ),
             1115 => array( '0,50', '0,50' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '126,18',
            // Total NETTO
            'totalNetto'  => '116,15',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '18,92',
                55 => '2,73',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '11,62',

            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '69,12',
                'netto' => '58,08',
                'vat' => '11,04'

            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '59,50',
                'netto' => '50,00',
                'vat' => '9,50'
            ),
            // GRAND TOTAL
            'grandTotal'  => '254,80'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => true,
            'blShowNetPrice' => true,
            'blShowVATForPayCharge' => true,
            'blShowVATForDelivery' => true,
            'blPaymentVatOnTop'=>true,
            'blDeliveryVatOnTop'=>true,

        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
