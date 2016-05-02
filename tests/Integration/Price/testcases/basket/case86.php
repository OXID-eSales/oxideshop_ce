<?php
/**
 * Price enter mode: brutto
 * Price view mode: brutto  
 * Product count: 1
 * VAT info: count of used vat's =19%
 * Currency rate: -
 * Discounts: 1
 *  1. 2,55% discount for product
 *  ...
 * Vouchers: -
 * Wrapping:  -
 * Gift cart: -
 * Short description: Vat and rounding issue test case: shop discount with articles ( Discount (from 0 unit to 99999) ) 
*/
$aData = array (
     'articles' => array (
         0 => array (
             'oxid'    => 'rounding_issue_test_article',
             'oxprice' => 298.55,
             'oxvat'   => 19,
             'amount'  => 200,
         ),
     ),
    'discounts' => array (
        0 => array (
            'oxid'         => 'discount_2_55_forShop',
            'oxaddsum'     => 2.55,
            'oxaddsumtype' => '%',
            'oxamount' => 0,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxarticles' => array ( 'rounding_issue_test_article' ),
        ),
    ),
    'expected' => array (
        'articles' => array (
            'rounding_issue_test_article' => array ( '290,94', '58.188,00' ),
        ),
        'totals' => array (
            'totalBrutto' => '58.188,00',
            'totalNetto'  => '48.897,48',
            'vats' => array (
                    '19' => '9.290,52'
            ),
            'grandTotal'  => '58.188,00'
        ),
    ),
    'options' => array (
            'config' => array (
                    'blEnterNetPrice' => false,
                    'blShowNetPrice' => false     
            ),
    )
);