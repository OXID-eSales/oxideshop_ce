<?php
/**
 * Price enter mode: netto
 * Price view mode: netto 
 * Product count: 6
 * VAT info: count of used vat =2(19% and 55%)
 * Currency rate: 1.00
 * Discounts: 4
 *  1.  10% discount for basket
 *  2. -10% discount for product's 111, 1112
 *  3.  5.5% discount for product's 1113 and 1114
 *  4. -5.2% discount for product 1115
 * Costs:
 *  1. Payment + 
 *  2. Delivery + 
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description: 
 * Vat and rounding issue. 6 articles. two different VAT(for one the product is set spec. VAT=55%, for all other products VAT=19%),  three discount for product, one discount for basket.Mode Neto-Neto
 */
$aData = array(
    // Articles
    'articles' => array (
        0 => array (
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 0.5,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array (
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 5.02,
            'oxvat'                    => 55,
            // Amount in basket
            'amount'                   => 5,
        ),
        2 => array (
         // oxarticles db fields
            'oxid'                     => 1113,
            'oxprice'                  => 1001,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        3 => array (
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 100.55,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        4 => array (
         // oxarticles db fields
            'oxid'                     => 1115,
            'oxprice'                  => 100.55,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        5 => array (
         // oxarticles db fields
            'oxid'                     => 1116,
            'oxprice'                  => 1.00,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
    ),
    // Discounts
    'discounts' => array (
        // oxdiscount DB fields
        0 => array (
            // 10% discount for basket
            'oxid'         => 'discountforbasket10%',
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
        ),
         1 => array (
            // -10% discount for product 111, 1112
            'oxid'         => 'procdiscountfor111',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 111, 1112 ),
        ),
        2 => array (
            // 5.5% discount for product 1113, 1114
            'oxid'         => 'procdiscountfor1113',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 1113, 1114 ),
        ),

        3 => array (
            //-5.2% discount for product 1115
            'oxid'         => 'discountforbasket1115',
            'oxaddsum'     => -5.2,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 1115),
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
    'expected' => array (
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array (
             111 => array ( '0,55', '0,55' ),
             1112 => array ( '5,52', '27,60' ),
             1113 => array ( '945,95', '945,95' ),
             1114 => array ( '95,02', '95,02' ),
             1115 => array ( '105,78', '105,78' ),
             1116 => array ( '1,00', '1,00' ),
        ),
        // Expectations of other totals
        'totals' => array (
            // Total BRUTTO
            'totalBrutto' => '1.268,33',
            // Total NETTO
            'totalNetto'  => '1.175,90',
            // Total VAT amount: vat% => total cost
            'vats' => array (
                19 => '196,36',
                55 => '13,66',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array (
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '117,59',

            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '699,66',
                'netto' => '587,95',
                'vat' => '111,71'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '59,50',
                'netto' => '50,00',
                'vat' => '9,50'
            ),
            // GRAND TOTAL
            'grandTotal'  => '2.027,49'
        ),
    ),
    // Test case options
    'options' => array (
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