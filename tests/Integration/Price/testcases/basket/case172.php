<?php
/**
 * Price enter mode: netto / brutto
 * Price view mode: netto / brutto
 * Product count: count of used products
 * VAT info: 19%
 * Currency rate: 1.0
 * Discounts: count
 *  1. discaunt for product 50%;
 * Short description: bug entry / support case other info;
 * shop has discount assigned to product 1126 with amount restrictions ( 3 < discount < 999 ),  test case is moved from unit test
 */
$aData = array(
    // Articles
    'articles' => array(
        0 => array(
                    'oxid'                     => 1126,
                    'oxprice'                  => 34.00,
                    'oxvat'                    => 19,
                    'amount'                   => 3,
        ),
    ),

    // Discounts
    'discounts' => array(
        0 => array(
            'oxid'         => 'testdisc',
            'oxaddsum'     => 50,
            'oxaddsumtype' => '%',
            'oxamount' => 3,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array( 1126 ),
            'oxsort' => 10,
        ),
    ),

    // TEST EXPECTATIONS
    'expected' => array(
        // Article expected prices: ARTICLE ID => ( Unit price, Total Price )
        'articles' => array(
             1126 => array( '17,00', '51,00' ),
        ),
        // Expectations of other totals
        'totals' => array(
            // Total BRUTTO
            'totalBrutto' => '51,00',
            // Total NETTO
            'totalNetto'  => '42,86',
            // Total VAT amount: vat% => total cost
            'vats' => array(
                19 => '8,14'
            ),


            // GRAND TOTAL
            'grandTotal'  => '51,00'
        ),
    ),
    // Test case options
    'options' => array(
        // Configs (real named)
        'config' => array(
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
            'bl_perfLoadSelectLists' => true,
        ),
        // Other options
        'activeCurrencyRate' => 1,
    ),
);
