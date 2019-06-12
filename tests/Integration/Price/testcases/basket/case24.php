<?php
/*
/**
 * Price enter mode: netto
 * Price view mode:  netto
 * Product count: 2
 * VAT info: 19% Default VAT for all Products , Vat for product 7% and 8%.
 * Currency rate: 1.0
 * Discounts: 2
 *  1. shop discount -10%for product 111
 *  2. shop discount 5.5% for product 1113, 1114

 * Vouchers: -
 * Wrapping: +
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Short description:
 * Resting Vat in Mode Neto-Neto
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 111,
            'oxprice'                  => 5.02,
            'oxvat'                    => 7,
            // Amount in basket
            'amount'                   => 1,
        ),
        1 => array(
         // oxarticles db fields
            'oxid'                     => 1112,
            'oxprice'                  => 99,00,
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
            'oxvat'                    => 8,
            // Amount in basket
            'amount'                   => 1,
        ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // 10% discount for basket
            'oxid'         => 'discount10%',
            'oxaddsum'     => -10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 111 ),
            'oxsort' => 10,
        ),
        1 => array(
            // 10% discount for basket
            'oxid'         => 'discount5.5',
            'oxaddsum'     => 5.5,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1112, 1113 ),
            'oxsort' => 20,
        ),
    ),
    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             111 => array( '5,52', '5,52' ),
             1112 => array( '93,56', '93,56' ),
             1113 => array( '945,95', '945,95' ),
             1114 => array( '5,02', '5,02' ),

        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '1.248,35',
            // Total NETTO
            'totalNetto'  => '1.050,05',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                7 => '0,39',
                8 => '0,40',
                19 => '197,51',
            ),

            // GRAND TOTAL
            'grandTotal'  => '1.248,35'
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
