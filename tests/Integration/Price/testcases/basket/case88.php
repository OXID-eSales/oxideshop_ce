<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 3
 * VAT info: count of used vat =20%
 * Currency rate: - 1.00
 * Discounts: 1
 *  1. 10% discount for basket
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -

 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description: Vat and rounding issue. 3 articles. 1 same vat
 */


$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 1.00,
            'oxvat'                    => 20,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1111,
            'oxprice'                  => 95.02,
            'oxvat'                    => 20,
            // Amount in basket
            'amount'                   => 6,
        ),
        2 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 105.78,
            'oxvat'                    => 20,
            // Amount in basket
            'amount'                   => 7,
        ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => 'procdiscountforbasket',
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
                'oxaddsum' => 1,
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
             111 => array( '1,00', '1,00' ),
             1111 => array( '95,02', '570,12' ),
             1112 => array( '105,78', '740,46' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.416,50',
            // Total NETTO
            'totalNetto'  => '1.311,58',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                20 => '236,08'
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'procdiscountforbasket' => '131,16',
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '786,95',
                'netto' => '655,79',
                'vat' => '131,16'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '1,20',
                'netto' => '1,00',
                'vat' => '0,20'
            ),
            // GRAND TOTAL
            'grandTotal'  => '2.204,65'
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
            'blPaymentVatOnTop'=>true,

        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
