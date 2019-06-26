<?php
/**
 * Price enter mode:  brutto
 * Price view mode:   brutto
 * Product count: count of used products
 * VAT info: count of used vat's (list)
 * Currency rate: 0.68
 * Discounts: -
 * Vouchers: -
 * Wrapping: -
 * Gift cart: -
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment -
 *  2. Delivery  -
 *  3. TS -
 * Actions with basket or order:
 * Short description: bug entry / support case other info;
 */
$aData = array(
    'articles' => array(
        0 => array(
                'oxid'                     => 9200,
                'oxprice'                  => 87,
                'oxvat'                    => 17,
                'amount'                   => 1,
        ),
    ),
    'expected' => array(
        'articles' => array(
                 9200 => array( '59,16', '59,16' ),
        ),
        'totals' => array(
                'totalBrutto' => '59,16',
                'totalNetto'  => '50,56',
                'vats' => array(
                        17 => '8,60',
                ),
                'grandTotal'  => '59,16'
        ),
    ),
    'options' => array(
        'config' => array(
                'blEnterNetPrice' => false,
                'blShowNetPrice' => false
        ),
        'activeCurrencyRate' => 0.68,
    ),
);
