<?php
/**
 * Price enter mode: netto / bruto
 * Price view mode: netto / brutto
 * Product count: count of used products
 * VAT info: 17%
 * Currency rate: 1.0
 * Discounts: -
 * Vouchers: -
 * Wrapping: -
 * Gift cart: -;
 * Costs VAT caclulation rule: max
 * Costs:
 *  1. Payment  -
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
                 9200 => array( '87,00', '87,00' ),
        ),
        'totals' => array(
                'totalBrutto' => '87,00',
                'totalNetto'  => '74,36',
                'vats' => array(
                        17 => '12,64',
                ),
                'grandTotal'  => '87,00'
        ),
    ),
    'options' => array(
        'config' => array(
            'blEnterNetPrice' => false,
            'blViewNetPrice' => false,
        ),
        'activeCurrencyRate' => 1,
    ),
);
