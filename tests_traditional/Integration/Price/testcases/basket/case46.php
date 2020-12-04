<?php
/*
 * 5 products with different vat. Bruto-Bruto mode.
 */
$aData = array(/**
 * Price enter mode: netto / brutto
 * Price view mode: netto / brutto
 * Product count: count of used products
 * VAT info: count of used vat's (list)
 * Currency rate: 1.0 (change if needed)
 * Discounts: count
 *  1. basket 10 %
 * Wrapping:  -
 * Gift cart: -;
 * Costs VAT caclulation rule: proportiona
 */

    // Product
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => 1001,
            'oxprice'                  => 1002.55,
            'oxvat'                    => 19,
            // Amount in basket
            'amount'                   => 2,
        ),
        1 => array(
          // oxarticles db fields
            'oxid'                     => 1002,
            'oxprice'                  => 11.56,
            'oxvat'                    => 13,
            // Amount in basket
            'amount'                   => 2,

        ),
         2 => array(
          // oxarticles db fields
            'oxid'                     => 1003,
            'oxprice'                  => 1326.89,
            'oxvat'                    => 3,
            // Amount in basket
            'amount'                   => 6,

        ),
         3 => array(
          // oxarticles db fields
            'oxid'                     => 1004,
            'oxprice'                  => 6.66,
            'oxvat'                    => 17,
            // Amount in basket
            'amount'                   => 6,

        ),
         4 => array(
          // oxarticles db fields
            'oxid'                     => 1005,
            'oxprice'                  => 0.66,
            'oxvat'                    => 33,
            // Amount in basket
            'amount'                   => 6,

        ),
    ),
    // Discounts
    'discounts' => array(
        // oxdiscount DB fields
        0 => array(
            // ID needed for expectation later on, specify meaningful name
            'oxid'         => 'tenpercentdiscount',
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),

    ),

    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1001 => array( '1.002,55', '2.005,10' ),
             1002 => array( '11,56', '23,12' ),
             1003 => array( '1.326,89', '7.961,34' ),
             1004 => array( '6,66', '39,96' ),
             1005 => array( '0,66', '3,96' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '10.033,48',
            // Total NETTO
            'totalNetto'  => '8.524,80',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '288,13',
                13 => '2,39',
                3  => '208,70',
                17 => '5,23',
                33 => '0,88',
            ),
        // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'tenpercentdiscount' => '1.003,35',
            ),
            // GRAND TOTAL
            'grandTotal'  => '9.030,13'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
