<?php
/*
/**
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products  and spec VAT=10%
 * Currency rate: 1.0
 * Discounts: -
 * Vouchers: -
 * Wrapping: +
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment +
 *  2. Delivery +
 *  3. TS -
 * Short description:
 * if in shop there are two products with different VATs. and both products have same total value
 */
$aData = array(
    // Product
    'articles' => array(
         0 => array(
            // oxarticles db fields
            'oxid'                     => 1001,
            'oxprice'                  => 200.00,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array(
            // oxarticles db fields
            'oxid'                     => 1002,
            'oxprice'                  => 20.00,
            'oxvat'                    => 10,
            // Amount in basket
            'amount'                   => 10,

        ),

    ),
    // Additional costs
    'costs' => array(
   
        // Delivery
        'delivery' => array(
            0 => array(
                // oxdelivery DB fields
                'oxactive' => 1,
                'oxaddsum' => 55.00,
                'oxaddsumtype' => '%',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 275,
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
            1001 => array( '200,00', '200,00' ),
            1002 => array( '20,00', '200,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '400,00',
            // Total NETTO
            'totalNetto'  => '349,89',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '31,93',
                10 => '18,18',

            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '261,80',
                'netto' => '220,00',
                'vat' => '41,80'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '327,25',
                'netto' => '275,00',
                'vat' => '52,25'
            ),
         
            // GRAND TOTAL
            'grandTotal'  => '989,05'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
            'blShowVATForDelivery'=> true,
            'blShowVATForPayCharge'=> true,
            'blShowVATForWrapping'=> true,
            'sAdditionalServVATCalcMethod' => 'biggest_net', //W: true,
            'blDeliveryVatOnTop' => true,
            'blPaymentVatOnTop' => true,
            'blWrappingVatOnTop' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
