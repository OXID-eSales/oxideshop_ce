<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 4
 * VAT info: vat =19%,
 * Currency rate: 1.00
 * Discounts: 5
 *  1.  10% discount for basket
 *  2. -10% discount for products 111, 1114
 *  3.  5.5% discount for product's 1113 and 1114
 *  4.  20% discount for basket
 *  5.  35% discount for basket
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 *  Vat and rounding issue. 4 articles. 1 same vat =19%, 5 discount, Mode Neto-Neto
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
        // -10% discount for products 111, 1114
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
        //5.5% discount for product's 1113 and 1114
            'oxid'         => 'procdiscountfor1113',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            // If for article, specify here
            'oxarticles' => array( 1113, 1114 ),
            'oxsort' => 30,
        ),
        3 => array(
        // 20% discount for basket
            'oxid'         => 'discountforbasket20%',
            'oxaddsum'     => 20,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 40,
        ),
        4 => array(
        // 35% discount for basket
            'oxid'         => 'discountforbasket35%',
            'oxaddsum'     => 35,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 50,
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
            'totalBrutto' => '532,84',
            // Total NETTO
            'totalNetto'  => '956,76',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '85,08'
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '95,68',
                'discountforbasket20%' => '172,22',
                'discountforbasket35%' => '241,10',
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
            'grandTotal'  => '1.161,61'
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
