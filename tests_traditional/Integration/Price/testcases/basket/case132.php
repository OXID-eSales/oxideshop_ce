<?php
/*
 * Price enter mode: brutto
 * Price view mode:  brutto
 * Product count: 1
 * Discounts: 1
 *  1. 500abs discount for basket
 * Short description:
 * @bug #3727:
 * Discount with such options:
 * FROM-TO range of units: 1-99999
 * Sum: 500 abs (500 EUR)
 * Product price less than discount value.
 */
$aData = array(
    'articles' => array(
         0 => array(
             'oxid'                     => '3727',
             'oxprice'                  => 5,
             'amount'                   => 1,
         ),
     ),
    'discounts' => array(
        0 => array(
            'oxid'         => 'discount500forShop',
            'oxaddsum'     => 500,
            'oxaddsumtype' => 'abs',
            'oxamount' => 1,
            'oxamountto' => 99999,
            'oxactive' => 1,
            'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
            '3727' => array( '5,00', '5,00' ),
        ),
        'totals' => array(
            'totalBrutto' => '5,00',
            'discounts' => array(
                'discount500forShop' => '5,00',
            ),
            'totalNetto'  => '0,00',
            'vats' => array(
                '19' => '0,00'
            ),
            'grandTotal'  => '0,00'
        ),
    ),
    'options' => array(
            'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
            ),
    )
);
