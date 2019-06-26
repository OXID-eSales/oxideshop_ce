<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 4
 * VAT info: vat =19%
 * Currency rate: 1.00
 * Discounts: 2
 *  1.  10% discount for basket
 *  2. -10% discount for product's (111, 1112, 1114)
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 * Vat and rounding issue. 4 articles. for product is set spec. VAT=55%, for all other products VAT=19%,
 *one discount for basket and one discount for product(111, 1112, 1114 ) .Mode Neto-Neto
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
            'oxprice'                  => 1,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        3 => array(
         // oxarticles db fields
            'oxid'                     => 1114,
            'oxprice'                  => 1001,
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
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
         1 => array(
            // -10% discount for product 111, 1112, 1114
            'oxid'         => 'procdiscountfor111',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 111, 1112, 1114 ),
            'oxsort' => 20,
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
             111 => array( '0,50', '0,50' ),
             1112 => array( '4,97', '4,97' ),
             1113 => array( '0,90', '0,90' ),
             1114 => array( '990,99', '990,99' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.186,86',
            // Total NETTO
            'totalNetto'  => '997,36',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '189,50',
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '652,77',
                'netto' => '548,55',
                'vat' => '104,22'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '0,65',
                'netto' => '0,55',
                'vat' => '0,10'
            ),
            // GRAND TOTAL
            'grandTotal'  => '1.840,28'
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
