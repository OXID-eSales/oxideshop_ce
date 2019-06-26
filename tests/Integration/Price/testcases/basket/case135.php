<?php
/**
 * Price enter mode: brutto;
 * Price view mode: brutto;
 * Product count: 3587;
 * VAT info: -;
 * Currency rate: -;
 * Discounts: 1
 * 1. 2% discount for product 3587
 * Wrapping:  -;
 * Gift cart: -;
 * Costs VAT caclulation rule: -;
 * Wrapping: -;
 * Gift cart:  -;
 * Vouchers: -;
 * Costs:
 *  1. Payment -;
 *  2. Delivery ;
 *  3. TS -
 * Short description:
 * Brutto-Brutto mode.
 @bug #3587: general discount for shop */
$aData = array(
    'articles' => array(
         0 => array(
                 'oxid'                     => '3587',
                 'oxtitle'                  => 'newspaper',
                 'oxprice'                  => 2.98,
                 'amount'                   => 200,
         ),
    ),
    'discounts' => array(
            0 => array(
                    'oxid'         => 'discount2forShop',
                    'oxaddsum'     => 2,
                    'oxaddsumtype' => '%',
                    'oxamount' => 0,
                    'oxamountto' => 99999,
                    'oxactive' => 1,
                    'oxsort' => 10,
            ),
    ),
    'expected' => array(
            'articles' => array(
                    '3587' => array( '2,92', '584,00' ),
            ),
            'totals' => array(
                    'totalBrutto' => '584,00',
                    'totalNetto'  => '490,76',
                    'vats' => array(
                            '19' => '93,24'
                    ),
                    'grandTotal'  => '584,00'
            ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
            ),
    )
);
