<?php
/*
/**
 * Price enter mode: bruto
 * Price view mode:  bruto
 * Product count: 1
 * VAT info: 19% Default VAT for all Products
 * Currency rate: 1.0
 * Discounts: -
 * Vouchers: -
 * Wrapping: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery -
 *  3. TS -
 * Short description:
 * From articlePrice.csv: article final price calculations. 9206 - 1st
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9206,
                'oxprice'                  => 103,
                'oxvat'                    => 19,
                'amount'                   => 1,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9206 => array( '103,00', '103,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '103,00',
                'totalNetto'  => '86,55',
                'vats' => array(
                        19 => '16,45',
                ),
                'grandTotal'  => '103,00'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
        ),
        'activeCurrencyRate' => 1,
    ),
);
