<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto
 * Product count: 1
 * VAT info: count of used vat's =19%
 * Currency rate: -
 * Discounts: 1
 *  1. 2,55% discount for basket
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description: Vat and rounding issue test case: basket discount without articles ( Discount (from 1 unit to 99999) )
 */
$aData = array(
     'articles' => array(
         0 => array(
             'oxid'    => 'rounding_issue_test_article',
             'oxprice' => 298.55,
             'oxvat'   => 19,
             'amount'  => 200,
         ),
     ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'discount_2_55_forShop',
            'oxaddsum'     => 2.55,
            'oxaddsumtype' => '%',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
            'rounding_issue_test_article' => array( '298,55', '59.710,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '59.710,00',
            'discounts' => array(
                    'discount_2_55_forShop' => '1.522,61',
            ),
            'totalNetto'  => '48.896,97',
            'vats' => array(
                    '19' => '9.290,42'
            ),
            'grandTotal'  => '58.187,39'
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
            ),
    )
);
