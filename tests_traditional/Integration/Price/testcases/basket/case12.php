<?php
/*
/**
 * Price enter mode: neto
 * Price view mode:  neto
 * Product count: 1
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts:
 *  1. shop abs discount for product 9203
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Short description:
 * From articlePrice.csv: article final price calculations. 9203 - 1st
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9203,
                'oxprice'                  => 29.99,
                'oxvat'                    => 19,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_9203',
                'oxaddsum'     => 2.01,
                'oxaddsumtype' => 'abs',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 9203 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9203 => array( '27,98', '27,98' ),
        ),
        'totals' => array(
                'totalBrutto' => '33,30',
                'totalNetto'  => '27,98',
                'vats' => array(
                        19 => '5,32',
                ),
                'grandTotal'  => '33,30'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => true,
                'blShowNetPrice' => true,
        ),
        'activeCurrencyRate' => 1,
    ),
);
