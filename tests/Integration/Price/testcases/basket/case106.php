<?php
/**
 * Price enter mode: netto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: used VAT =20%
 * Currency rate: 1.00
 * Discounts: -
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS  -
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description:
 * Payment methods calculation in Neto-Bruto Mode.Payment 10%
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 1,00,
            'oxvat'                    => 20,
            // Amount in basket
            'amount'                   => 1,
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
                'oxaddsum' => 10,
                'oxaddsumtype' => '%',
                'oxfromamount' => 0,
                'oxtoamount' => 1000000,
                'oxchecked' => 1,
                // 1. Value of all goods in cart
                'oxaddsumrules'=>1,
            ),
        ),

    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             111 => array( '1,20', '1,20' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1,20',
            // Total NETTO
            'totalNetto'  => '1,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                20 => '0,20',
            ),


            // Total delivery amounts
            'delivery' => array(
                'brutto' => '0,79',
                'netto' => '0,66',
                'vat' => '0,13'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '0,14',
                'netto' => '0,12',
                'vat' => '0,02'
            ),
            // GRAND TOTAL
            'grandTotal'  => '2,13'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => true,
            'blShowNetPrice' => false,
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
