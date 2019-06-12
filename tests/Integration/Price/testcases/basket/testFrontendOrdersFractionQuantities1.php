<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 1;
 * VAT info:  count of used vat =1(5%);
 * Currency rate: -;
 * Discounts: 1;
 * 1. 10% ;
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery -;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 * Test checking when active fraction quantity ('blAllowUnevenAmounts' => true,),
 * Test is moved from selenium test "testFrontendOrdersFractionQuantities"
 */
$aData = array(

    'articles' => array(

        0 => array(
                'oxid'                     => 1000,
                'oxprice'                  => 50,
                'oxvat'                    => 5,
                'oxunitname'               => 'kg',
                'oxunitquantity'           => 10,
                'oxweight'                 => 10,
                'amount'                   => 3.4,

        ),
    ),
        // Discounts
    'discounts' => array(
        0 => array(
            // Discount 10%
            'oxid'         => 'test',
            'oxshopid' => 1,
            'oxaddsum'     => 10,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxprice' => 0,
            'oxpriceto'=> 999999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 1000 => array( '45,00', '153,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '153,00',
                'totalNetto'  => '145,71',
                'vats' => array(
                        5 => '7,29',
                ),
                'grandTotal'  => '153,00'
        ),
    ),
    'options' => array(
        'config' => array(
            'blAllowUnevenAmounts' => true,
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
         ),
    ),
);
