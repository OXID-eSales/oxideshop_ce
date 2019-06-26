<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 3
 * VAT info: vat =19%
 * Currency rate: 1.00
 * Discounts: 1
 *  1. 10% discount for basket
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description: Vat and rounding issue. 4 articles. 1 same vat =19%, two discount for product, one discount for basket.Mode Neto-Neto
 */

$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 0.50,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 5.02,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        2 => array(
         // oxarticles db fields
            'oxid'                     => 1113,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        3 => array(
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 5.02,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
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
         1 => array(
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => 'procdiscountfor111',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            // If for article, specify here
            'oxarticles' => array( 111, 1112 ),
            'oxsort' => 20,
        ),
        2 => array(
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => 'procdiscountfor1113',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            // If for article, specify here
            'oxarticles' => array( 1113, 1114 ),
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
             111 => array( '0,55', '0,55' ),
             1112 => array( '5,52', '5,52' ),
             1113 => array( '945,95', '945,95' ),
             1114 => array( '4,74', '4,74' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.024,69',
            // Total NETTO
            'totalNetto'  => '956,76',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '163,61'
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'procdiscountforbasket' => '95,68',
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '569,27',
                'netto' => '478,38',
                'vat' => '90,89'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '59,50',
                'netto' => '50,00',
                'vat' => '9,50'
            ),
            // GRAND TOTAL
            'grandTotal'  => '1.653,46'
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
