<?php
/**
 * Price enter mode:  brutto
 * Price view mode:  brutto
 * Product count: count of used products
 * VAT info: count of used vat's (list)
 * Currency rate: 1.0 (change if needed)
 * Wrapping:  -
 * Gift cart: -;
 * Costs VAT caclulation rule: bigest net
 * Short description: 2 products with different vat. Payment, shipping fees. Calculate VAT according to the biggest net value
 */

$aData = array(
    'skipped' => 1, //investigating bug #0005772
    // Product
    'articles' => array (
        0 => array (
            // oxarticles db fields
            'oxid'                     => 1001,
            'oxprice'                  => 10.00,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array (
            // oxarticles db fields
            'oxid'                     => 1002,
            'oxprice'                  => 10.00,
            'oxvat'                    => 7,
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
                'oxaddsum' => 3.90,
                'oxaddsumtype' => 'abs',
                'oxdeltype' => 'p',
                'oxfinalize' => 1,
                'oxparamend' => 99999,
            ),
        ),
        // Payment
        'payment' => array(
            0 => array(
                // oxpayments DB fields
                'oxaddsum' => 7.50,
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
            1001 => array ( '10,00', '10,00' ),
            1002 => array ( '10,00', '10,00' ),

        ),
        // Expectations of other totals
        'totals' => array (
            // Total BRUTTO
            'totalBrutto' => '20,00',
            // Total NETTO
            'totalNetto'  => '17,75',
            // Total VAT amount: vat% => total cost
            'vats' => array (
                19 => '1,60',
                7 => '0,65',
            ),
            // Total delivery amounts
            'delivery' => array(
                'brutto' => '3,90',
                'netto' => '3,64',
                'vat' => '0,26'
            ),
            // Total payment amounts
            'payment' => array(
                'brutto' => '7,50',
                'netto' => '7,01',
                'vat' => '0,49'
            ),
            // GRAND TOTAL
            'grandTotal'  => '31,40'
        ),
    ),
    // Test case options
    'options' => array (
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
            'blShowVATForDelivery'=> false,
            'blShowVATForPayCharge'=> false,
            'blShowVATForWrapping'=> false,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);