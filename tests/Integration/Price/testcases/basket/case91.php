<?php
/**
 * Price enter mode: brutto
 * Price view mode: netto
 * Product count: 5
 * VAT info: count of used vat =5(19%, 13%, 3%, 17% and 33%)
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
 * 5 products with different vat. Bruto-Neto mode.
 */

$aData = array(
    // Product
    'articles' => array(
        0 => array(
            // oxarticles db fields
            'oxid'                     => '_test1001',
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
             '_test1001' => array( '842,48', '1.684,96' ),
             1002 => array( '10,23', '20,46' ),
             1003 => array( '1.288,24', '7.729,44' ),
             1004 => array( '5,69', '34,14' ),
             1005 => array( '0,50', '3,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '9.030,12',
            // Total NETTO
            'totalNetto'  => '9.472,00',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '288,13',
                13 => '2,39',
                3  => '208,69',
                17 => '5,22',
                33 => '0,89',
            ),
        // Total discount amounts: discount id => total cost
            'discounts' => array(
                // Expectation for special discount with specified ID
                'tenpercentdiscount' => '947,20',
            ),
            // GRAND TOTAL
            'grandTotal'  => '9.030,12'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
