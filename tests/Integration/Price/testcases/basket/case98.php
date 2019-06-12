<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 6
 * VAT info: count of used vat =2(99% and 9%)
 * Currency rate: 1.00
 * Discounts: 6
 *  1.  10% discount for basket
 *  2. -10% discount for product's 111, 1112
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
 * Vat and rounding issue. 6 products.
 * for all products VAT=99%, spec. vat for one product 9%,  two discount for product, three discount for basket.Mode Neto-Neto
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 0.5,
            'oxvat'                    => 99,
            // Amount in basket
            'amount'                   => 3,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 5.02,
            'oxvat'                    => 99,
            // Amount in basket
            'amount'                   => 2,
        ),
        2 => array(
         // oxarticles db fields
            'oxid'                     => 1113,
            'oxprice'                  => 1001,
            'oxvat'                    => 99,
            // Amount in basket
            'amount'                   => 1,
        ),
        3 => array(
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 5.02,
            'oxvat'                    => 99,
            // Amount in basket
            'amount'                   => 1,
        ),
        4 => array(
         // oxarticles db fields
            'oxid'                     => 1115,
            'oxprice'                  => 100.55,
            'oxvat'                    => 99,
            // Amount in basket
            'amount'                   => 1,
        ),
        5 => array(
         // oxarticles db fields
            'oxid'                     => 1116,
            'oxprice'                  => 5,
            'oxvat'                    => 9,
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
            // -10% discount for product 111, 1112
            'oxid'         => 'procdiscountfor111',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 111, 1112 ),
            'oxsort' => 20,
        ),
        2 => array(
            // 5.5% discount for product 1113, 1114
            'oxid'         => 'procdiscountfor1113',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1113, 1114, 1115 ),
            'oxsort' => 30,
        ),
        3 => array(
            // 20% discount for basket
            'oxid'         => 'procdiscount20%',
            'oxaddsum'     => 20,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 40,
        ),
        4 => array(
            // 35% discount for basket
            'oxid'         => 'procdiscount35%',
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
                'oxaddsum' => 55,
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
                'oxaddsum' => 0.55,
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
             1112 => array( '5,52', '11,04' ),
             1113 => array( '945,95', '945,95' ),
             1114 => array( '4,74', '4,74' ),
             1115 => array( '95,02', '95,02' ),
             1116 => array( '5,00', '5,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '988,26',
            // Total NETTO
            'totalNetto'  => '1.063,40',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                99 => '490,38',
                9 => '0,21',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '106,34',
                'procdiscount20%'=>'191,41',
                'procdiscount35%'=>'267,98',

            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '1.163,89',
                'netto' => '584,87',
                'vat' => '579,02'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '1,09',
                'netto' => '0,55',
                'vat' => '0,54'
            ),
            // GRAND TOTAL
            'grandTotal'  => '2.153,24'
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
