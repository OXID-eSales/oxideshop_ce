<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 6
 * VAT info: vat =33%
 * Currency rate: 1.00
 * Discounts: 1
 *  1.  10% discount for basket
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 * Vat and rounding issue. 4 products.
 * for all products VAT=33%, spec. one discount for basket.Mode Neto-Neto
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 0.55,
            'oxvat'                    => 33,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 1101.10,
            'oxvat'                    => 33,
            // Amount in basket
            'amount'                   => 1,
        ),
        2 => array(
         // oxarticles db fields
            'oxid'                     => 1113,
            'oxprice'                  => 110,
            'oxvat'                    => 33,
            // Amount in basket
            'amount'                   => 1,
        ),
        3 => array(
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 1.00,
            'oxvat'                    => 33,
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
    ),
    // Additional costs
    'costs' => array(

        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 55,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparam' => 0.1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 55,
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
             1112 => array( '1.101,10', '1.101,10' ),
             1113 => array( '110,00', '110,00' ),
             1114 => array( '1,00', '1,00' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.451,54',
            // Total NETTO
            'totalNetto'  => '1.212,65',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                33 => '360,16',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '121,27',

            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '887,06',
                'netto' => '666,96',
                'vat' => '220,10'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '73,15',
                'netto' => '55,00',
                'vat' => '18,15'
            ),
            // GRAND TOTAL
            'grandTotal'  => '2.411,75'
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
