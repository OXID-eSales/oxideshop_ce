<?php
/*
 * Price enter mode: Brutto;
 * Price view mode: Brutto;
 * Product count: 3;
 * VAT info:  count of used vat =1(19%);
 * Currency rate: -;
 * Discounts: -;
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
 * Test checking when disabled fraction quantity ('blAllowUnevenAmounts' => false,),
 * Test is moved from selenium test "testFrontendOrdersFractionQuantities"
 */
$aData = array(
//test mark as shipped because need to implementation product amount calculation.
// if 'blAllowUnevenAmounts' is false, product amound== 3.4 should be rounding to 3
 // 'skipped' => 1,
    'articles' => array(
        0 => array(
                'oxid'                     => 1003,
                'oxprice'                  => 75,
                'oxvat'                    => 19,
                //product 1003 amound is 3, because 3.4 is rounded to 3
                'amount'                   => 3.4,
        ),
        1 => array(
                'oxid'                     => 1001,
                'oxprice'                  => 100,
                'oxvat'                    => 10,
                //product 1001 amound is used 0, because 0.3 is rounded to 0
                'amount'                   => 0.3,
        ),
        2 => array(
                'oxid'                     => 1000,
                'oxprice'                  => 50,
                'oxvat'                    => 19,
                'oxunitname'               => 'kg',
                'oxunitquantity'           => 10,
                'oxweight'                 => 10,
                //product 1000 amound is used 2, because 1.5 is rounded to 2
                'amount'                   => 1.5,
            
        ),
    ),
    'expected' => array(
        'articles' => array(
                 1003 => array( '75,00', '225,00' ),
                // 1001 => array ( '100', '0,33' ),
                 1000 => array( '50,00', '100,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '325,00',
                // calculation Netto=325/1.19=273.109
                'totalNetto'  => '273,11',
                'vats' => array(
                // calculation Vat=325*(19/119)=51.89
                        19 => '51,89',
                ),
                // calculation Total=51,89+273,11=325
                'grandTotal'  => '325,00'
        ),
    ),
    'options' => array(
        'config' => array(
            'blAllowUnevenAmounts' => false,
            'blEnterNetPrice' => false,
            'blShowNetPrice' => false,
         ),
    ),
);
