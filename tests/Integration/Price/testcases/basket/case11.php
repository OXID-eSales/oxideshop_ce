<?php
/*
/**
 * Price enter mode: bruto
 * Price view mode:  bruto
 * Product count: 1
 * VAT info: 18% Default VAT for all Products
 * Currency rate: 0.56
 * Discounts: abs_discount_for_product 9205
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Short description:
 * From articlePrice.csv: article final price calculations. 9205 - 1st
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9205,
                'oxprice'                  => 25.90,
                'oxvat'                    => 18,
                'amount'                   => 1,
        ),
    ),
    'discounts' => array(
        0 => array(
                'oxid'         => 'abs_discount_for_9205',
                'oxaddsum'     => 5.31,
                'oxaddsumtype' => 'abs',
                'oxamount' => 0,
                'oxamountto' => 99999,
                'oxactive' => 1,
                'oxarticles' => array( 9205 ),
                'oxsort' => 10,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9205 => array( '11,53', '11,53' ),
        ),
        'totals' => array(
                'totalBrutto' => '11,53',
                'totalNetto'  => '9,77',
                'vats' => array(
                        18 => '1,76',
                ),
                'grandTotal'  => '11,53'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false,
        ),
        'activeCurrencyRate' => 0.56,
    ),
);
