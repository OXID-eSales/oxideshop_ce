<?php
/**
 * Price enter mode: netto
 * Price view mode: netto
 * Product count: 2
 * VAT info: count of used vat =2(33% and 50%)
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
 * Vat and rounding issue. 2 products.
 * for all products VAT=33%, for one product VAT=50% spec. one discount for basket= 10%. Mode Neto-Neto
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
            'oxprice'                  => 945.95,
            'oxvat'                    => 50,
            // Amount in basket
            'amount'                   => 2,
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
                 'oxparam'=> 0.1,
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
             1112 => array( '945,95', '1.891,90' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '2.554,72',
            // Total NETTO
            'totalNetto'  => '1.892,45',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                33 => '0,16',
                50 => '851,36',
            ),
            // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'discountforbasket10%' => '189,25'
            ),

            // Total delivery amounts
            'delivery' => array(
                'brutto' => '1.561,28',
                'netto' => '1.040,85',
                'vat' => '520,43'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '82,50',
                'netto' => '55,00',
                'vat' => '27,50'
            ),
            // GRAND TOTAL
            'grandTotal'  => '4.198,50'
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
